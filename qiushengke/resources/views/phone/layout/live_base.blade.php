@extends('phone.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/phone/css/video.css">
@endsection
@section('js')
    <script type="text/javascript" src="{{$cdn}}/phone/js/video.js"></script>
    <script type="text/javascript">
        function changeChannel() {
            var obj = $('select#channelSelect')[0];
            var Link = obj.value;
            document.getElementById('Iframe').src = Link;
        }

        changeChannel();
    </script>
    @yield('live_js')
@endsection
@section('content')
    <div id="Navigation">
        <div class="banner">
            比赛直播
            @if(isset($lives))
            <select id="channelSelect" onchange="changeChannel()">
                @for($i = 0 ; $i < count($lives); $i++)
                    <?php
                    $channel = $lives[$i];
                    ?>
                    <?php
                    $preUrl = str_replace("http://","http://",env('APP_URL'));
                    $link = $preUrl.'/live/player/player-'.$channel['id'].'.html';
                    ?>
                    @if($i == 0)
                            <option value="{{$link}}">{{$channel['name']}}</option>
                    @else
                            <option value="{{$link}}" selected="selected">{{$channel['name']}}</option>
                    @endif
                @endfor
            </select>
            @endif
        </div>
    </div>
    <iframe src="" id="Iframe"></iframe>
    <div class="tab">
        <input type="radio" name="tab" id="Tab_Info" value="Info" checked><label for="Tab_Info">比赛信息</label>
        <input type="radio" name="tab" id="Tab_Event" value="Event"><label for="Tab_Event">比赛事件</label><!--足球-->
        <input type="radio" name="tab" id="Tab_Player" value="Player"><label for="Tab_Player">球员统计</label><!--篮球-->
    </div>
@endsection