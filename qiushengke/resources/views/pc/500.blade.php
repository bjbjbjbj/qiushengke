@extends('pc.layout.base')
@section('navContent')
    @component('pc.layout.nav_content',['type'=>0])
    @endcomponent
@endsection
@section('content')
    <div id="Content" type="{{$error}}">
        <div class="inner"><p>抱歉，您访问的页面出错了</p></div>
    </div>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="/pc/css/error.css">
@endsection