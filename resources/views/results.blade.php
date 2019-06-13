@extends('layout')

@section('title')
  {{$title}}
@endsection

@section('result')
  <p>test</p>
  @foreach($result as $r)
    <li>{{$r}}</li>
  @endforeach
  
@endsection
