@extends('phone.layout.base')
@section('content')
    <div id="Navigation">
        <div class="tab">
            <a href="/wap/match/foot/schedule/immediate.html">即时比分</a>
            <a href="/wap/match/foot/schedule/{{$lastDate}}/result.html">完场赛果</a>
            <a href="/wap/match/foot/schedule/{{$nextDate}}/schedule.html">未来赛程</a>
            <a class="on">热门赛事</a>
        </div>
    </div>
    <div id="List">
        @foreach($footLeagues as $link)
            <?php
            if(array_key_exists($link['id'], \App\Http\Controllers\PC\League\LeagueController::footLeagueIcons))
                $icon = \App\Http\Controllers\PC\League\LeagueController::footLeagueIcons[$link['id']];
            else
                $icon = $cdn.'/phone/img/icon_teamDefault.png';
            ?>
            <a class="default" href="{{'/wap'.$link['url']}}"><img src="{{$icon}}"><p>{{$link['name']}}</p></a>
        @endforeach
    </div>
    <dl id="Bottom">
        <dd class="on"><a><img src="img/tab_0_seleted.png"><p>足球</p></a></dd>
        <dd><a href="immediate_bk.html"><img src="img/tab_2_normal.png"><p>篮球</p></a></dd>
        <dd><a href="anchor.html"><img src="img/tab_1_normal.png"><p>主播</p></a></dd>
    </dl>
    @component('phone.layout.bottom',['index'=>0,'cdn'=>$cdn])
    @endcomponent
@endsection
@section('js')
    <script type="text/javascript">
        window.onload = function () {
            setPage();
        }
    </script>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/phone/css/hotleague.css">
@endsection