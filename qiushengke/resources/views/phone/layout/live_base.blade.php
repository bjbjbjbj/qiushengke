@extends('phone.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/phone/css/video.css">
@endsection
@section('js')
    <script type="text/javascript" src="{{$cdn}}/phone/js/video.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            setPage();
            setDataUpdate('{{$sport}}','{{$match['mid']}}');
        }

        function changeChannel() {
            var obj = $('select#channelSelect')[0];
            var Link = obj.value;
            document.getElementById('Iframe').src = Link;
        }

        changeChannel();

        function clickHideScore(button) {
            if (button.innerHTML == '隐藏比分'){
                $('div#Info div.score p')[0].className = 'hide';
                button.innerHTML = '显示比分'
            }
            else{
                $('div#Info div.score p')[0].className = '';
                button.innerHTML = '隐藏比分'
            }
        }
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
                    if(isset($channel['anchor_id'])){
                        $type = 2;
                    }
                    else{
                        $type = 1;
                    }
                    ?>
                    <?php
                    $preUrl = str_replace("http://","http://",env('APP_URL'));
                    $link = $preUrl.'/live/player/player-'.$channel['id'].'-'.$type.'.html';
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
    @yield('live_tab')
@endsection