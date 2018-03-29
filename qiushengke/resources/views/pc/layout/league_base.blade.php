@extends('pc.layout.base')
@section('navContent')
    @component('pc.layout.nav_content',['type'=>0])
    @endcomponent
    @component('pc.cell.top_leagues',['links'=>$footLeagues])
    @endcomponent
@endsection
@section('js')
    <script type="text/javascript" src="{{$cdn}}/pc/js/league.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            setPage();
        }
    </script>
    @yield('league_js')
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/pc/css/league.css">
@endsection
@section('content')
    <div id="Con">
        <div class="right">
            <div class="rbox" id="Info">
                <p class="title">赛事介绍</p>
                <div class="con">
                    @if(array_key_exists($league['id'], \App\Http\Controllers\PC\League\LeagueController::footLeagueIcons))
                        <p class="info"><img src="{{\App\Http\Controllers\PC\League\LeagueController::footLeagueIcons[$league['id']]}}">{{$league['name']}}<span>{{$season['name']}}赛季</span></p>
                    @else
                        <p class="info"><img src="/pc/img/icon_teamDefault.png">{{$league['name']}}<span>{{$season['name']}}赛季</span></p>
                    @endif
                    <?php
                        $describeStr = $league['describe'];
                        $describes = explode("<br>", $describeStr);
                    ?>
                    @foreach($describes as $describe)
                        <p class="text">{{$describe}}</p>
                    @endforeach
                </div>
                <button class="open">查看详情</button>
            </div>
            <div class="rbox" id="Video">
                @if(isset($videos) && count($videos) > 0)
                    <p class="title">精彩视频</p>
                    <ul>
                        @foreach($videos as $video)
                            <a class="li" target="_blank" href="{{$video['content']}}">
                                <p class="img"><img src="{{$video['cover']}}"></p>
                                <p class="text"><span>{{$video['title']}}</span></p>
                            </a>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        @yield('league_content')
    </div>
@endsection