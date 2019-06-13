@extends('navbar')
@section('body')
<div class="container">
	<div class="mt-5">
		<h1>@yield('title')</h1>
	</div>

	<div>
		@yield("form")
	</div>
	<div>
		@yield("result")
	</div>
	
</div>
@endsection