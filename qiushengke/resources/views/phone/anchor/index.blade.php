@extends('phone.layout.base')
@section('content')
    <div id="Navigation">
        <div class="tab">
            <input type="radio" name="tab" id="Tab_Live" value="Live" checked><label for="Tab_Live">正在直播</label>
            {{--<input type="radio" name="tab" id="Tab_Anchor" value="Anchor"><label for="Tab_Anchor">进驻主播</label>--}}
        </div>
    </div>
    <div id="Live">
        @if(isset($lives) && count($lives) > 0)
            <div class="default live">
                <div class="title">赛事直播</div>
                @foreach($lives as $live)
                    <?php $url = \App\Http\Controllers\PC\CommonTool::matchWapLivePathWithId($live->mid, $live->sport) ?>
                    <a href="{{$url}}">
                        <img src="/phone/img/live_demo.jpg">
                        <p>{{$live['match']['hname']}} <span>VS</span> {{$live['match']['aname']}}</p>
                    </a>
                @endforeach
            </div>
        @endif
        @if(isset($anchorRooms) && count($anchorRooms) > 0)
            <div class="default live anchor">
                <div class="title">主播直播</div>
                @foreach($anchorRooms as $anchorRoom)
                    <?php
                    $roomMatches = $anchorRoom->roomMatches;
                    $url = '';
                    if (isset($roomMatches) && count($roomMatches) > 0) {
                        $roomMatch = $roomMatches[0];
                        $url = \App\Http\Controllers\PC\CommonTool::matchWapLivePathWithId($roomMatch->mid, $roomMatch->sport);
                    }
                    ?>
                    <a href="{{$url}}">
                        <img src="/phone/img/live_demo.jpg">
                        <img class="face" src="{{$anchorRoom->anchor->icon}}">
                        <p>{{$anchorRoom->anchor->name}}解说</p>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
    <!--<div id="Anchor" style="display: none;">
        <div class="default">
            <a href="video.html" class="living">
                <img src="img/face_demo.jpg">
                <p>么么哒cos姐</p>
            </a>
            <a>
                <img src="img/face_demo.jpg">
                <p>么么哒cos姐</p>
            </a>
        </div>
    </div> -->
    @component('phone.layout.bottom',['index'=>2,'cdn'=>$cdn])
    @endcomponent
@endsection
@section('js')
    <script type="text/javascript" src="{{$cdn}}/phone/js/anchor.js"></script>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/phone/css/anchor.css?123">
@endsection