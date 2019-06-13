@extends('layout')

@section('title')
  {{$title}}
@endsection

@section('form')
  <form method="post" action="/comments">
    {{csrf_field()}}
    <!-- <label for="vedio_id" >影片 youtube 網址</label> -->
    <input placeholder="影片 youtube 網址" name="vedio_id" class="form-control"></input>
    <br>
    <!-- <label for="keyword">影片關鍵字</label> -->
    <input  placeholder="影片關鍵字" name="keyword" class="form-control"></input>
    <br>
    <!-- <label for="pick_number">選出幾位？</label> -->
    <input type="number" placeholder="選出幾位？" name="pick_number" min="1" max="10 0" class="form-control"></input>
    <br>
    <div class="form-check">
      <input type="checkbox" id="check_cheat" name="check_cheat" class="form-check-input"></input>
      <label for="check_cheat" class="form-check-label">檢查重複留言</label>
    </div>
    <br>
    <button type="submit" class="btn btn-info">Pick!</button>
  </form>
  <hr/>
@endsection

@section('result')
  @if (isset($result))
  <h2>Results</h2>
    @if(!empty($result))
    <div id="result">
      <div id="picked_result" style="width: 50%; display: inline-block;">
        <label for="pick_table">恭喜{{sizeof($result)}}位幸運的觀眾們：</label>
        <table name="pick_table" class="table">
          <thead class="thead-dark">
            <tr>
              <th>順序</th>
              <th>Youtube使用者名稱</th>
              <th>發表時間</th>
              <th>最後更新時間</th>
              <th>留言內容</th>
            </tr>
          </thead>
          <tbody>
        <?php
          $index=1;
        ?>
          @foreach($result as $r)
            <tr>
              <th>{{$index}}</th>
              <th>{{$r['authorName']}}</th>  
              <th>{{$r['publishedTime']}}</th> 
              <th>{{$r['updatedTime']}}</th>  
              <th>{{$r['textDisplay']}}</th> 
              <?php $index=$index+1; ?> 
            </tr>
          @endforeach
        
          </tbody>
        </table>
      </div>
      <div id="cheat_result" style="width: 30%; float:right;">
        <label for="cheat_table">以下是有重複留言的{{ sizeof($cheaters) }}位觀眾們：</label>
        <table name="cheat_table" class="table" >
          <thead class="thead-dark">
            <tr>
              <th>user name</th>
              <th>cheat times</th>  
            </tr>
          </thead>
        @foreach($cheaters as $id => $cheater)
          <tr>
            <th>{{$cheater->name}}</th>
            <th>{{$cheater->times}}</th>  
          </tr>
        @endforeach
        </table>
      </div>
    </div>
    @else
        沒人中獎
    @endif
  <hr>
  @else
      
  @endif
@endsection