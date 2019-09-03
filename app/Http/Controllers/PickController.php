<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect;
use Google_Client;
use Google_Service_YouTube;
use Storage;
use google\appengine\api\cloud_storage\CloudStorageTools;

class PickController extends Controller
{
    public function about()
    {
    	return view('about')->withTitle("About Author");
    }
    public function initializeSimplePick(Request $request)
    {
		$title = "Simple Pick";
		$controller = new PickController();
		$keyword = strval($request->get("keyword"));
		$result = $request->get('result');
		$auth = false;
    	return view('comments')->withTitle($title)->withAuth($auth);
    }
    public function getComments($videoId){
    	// Call set_include_path() as needed to point to your client library.
				
		$controller = new PickController();
		$client = $this::getGoogleAPIServiceAccountClient();
		return ["66"];

    }

    public function login() 
    {
    		$client = $this::getGoogleAPIServiceAccountClient();
			$this->jump2GoogleOAuth($client);
    }

    public function pick(Request $request){
    	/*
    		Prepare variables to be used
    	*/
    	$title = "Simple Pick";
		$checkCheat = strval($request->get("check_cheat"));
		$keyword = strval($request->get("keyword"));
		$pickNumber = intval($request->get("pick_number"));
		$parts = parse_url(strval($request->get("vedio_id")));
		parse_str($parts['query'], $query);
		$VIDEO_ID = $query['v'];
    	$validAuthorIds = [];
    	$cheaters = [];
		$count = 0;
    	

		$auth = true;	
		$client = $this::getGoogleAPIServiceAccountClient();
		$youtube = new Google_Service_YouTube($client);
		$nextPageToken = "";
		$listParameter = array(
			    'videoId' => $VIDEO_ID,
			    'textFormat' => 'plainText',
			    'maxResults' =>100,
			    'searchTerms' => $keyword
	    );
		do {
			// Query Comments Page by Page in case there are too many comments
			$comments = $youtube->commentThreads->listCommentThreads('snippet', $listParameter);
	    	$nextPageToken = isset($comments['nextPageToken'])? $comments['nextPageToken']: "";
	    	$listParameter['pageToken'] = $nextPageToken;

	    	foreach ($comments as $key => $value) 
	    	{
				$author = [];
				$snippet = $value["snippet"]["topLevelComment"]["snippet"];
				$author['authorId'] = $snippet["authorChannelId"]["value"];
				$author['authorName'] = $snippet["authorDisplayName"];

				// Check Mode in ON, and the AuthorID has been checked already
				if ($checkCheat == "on" && isset($validAuthorIds[$author['authorId']]))
				{
					$cheaters[$author['authorId']] = isset($cheaters[$author['authorId']])? ['times' => $cheaters[$author['authorId']]['times'] + 1, 'name' => $author['authorName']] : ['times' => 2, 'name' => $author['authorName']];

					// remove cheater from valid list
					unset($validAuthorIds[$author['authorId']]);
					continue;
				}
				
				$updatedTime = date_create($snippet["updatedAt"]);
				$author['updatedTime'] = date_format($updatedTime, "Y/m/d H:i:s"); 

				$publishedTime = date_create($snippet["publishedAt"]);
				$author['publishedTime'] = date_format($publishedTime, "Y/m/d H:i:s"); 

				$author['textDisplay'] = $snippet["textDisplay"]; 

				//add checked authors
				$validAuthorIds[$author['authorId']] = $author;
	    	}
		} while($nextPageToken !== "");
		$pickNumber = $pickNumber <= sizeof($validAuthorIds) ? $pickNumber : sizeof($validAuthorIds);

		if(empty($validAuthorIds))
		{
			throw new \Exception("Youtube API 緩衝中 或是該影片沒有留言");
		}
		$resultKeys = is_array(array_rand($validAuthorIds, $pickNumber))? array_rand($validAuthorIds, $pickNumber): array(array_rand($validAuthorIds, $pickNumber));
		$result = [];
		foreach($resultKeys as $key) 
		{
			$result[] = $validAuthorIds[$key];
		}
    	return view('comments')->withResult($result)->withCheaters($cheaters)->withTitle($title)->withAuth($auth);
    	
    }

	private static function getGoogleAPIServiceAccountClient()
	{
		$client = new Google_Client();
        $options = ['gs' => ['acl' => 'public-read']];
        $context = stream_context_create($options);
        $fileName = "gs://luckykids.appspot.com/LuckyKids-e8131d77171c.json";
        dd(CloudStorageTools::serve($fileName));


        $client->setAuthConfig(Storage::disk('local')->getAdapter()->getPathPrefix() . 'private/cred.json');
        $client->useApplicationDefaultCredentials();
		$client->addScope(Google_Service_YouTube::YOUTUBE_FORCE_SSL);
		return $client;
	}

	

}
