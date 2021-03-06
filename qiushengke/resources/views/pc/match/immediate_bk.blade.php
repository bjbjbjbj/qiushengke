@extends('pc.layout.matchlist_bk')
@section('match_list_content')
    <div class="ConInner">
        <div id="Control">
            <div class="inbox">
                <p class="save"><button onclick="confirmFilter('match',false)">保留</button><button onclick="confirmFilter('match',true)">删除</button></p>
                <p class="array"><button id="array_time" class="on" onclick="changeBKFilter('time')">按时间排序</button><button id="array_league" onclick="changeBKFilter('league')">按联赛排序</button></p>
                <p class="column">
                    <button id="column_nba" class="on" onclick="matchFilter('nba')">NBA</button><button id="column_live" onclick="matchFilter('live')">直播</button><button id="column_all" onclick="matchFilter('all')">完整</button>
                </p>
                <p class="number">共<b>{{$total}}</b>场&nbsp;隐藏<b id="hideMatchCount">-</b>场<span onclick="matchFilter('all')">【显示】</span></p>
                <p class="filter"><button class="league">选择赛事</button></p>
            </div>
        </div>
        @foreach($matches as $match)
            @component('pc.cell.match_list_cell_bk',['match'=>$match,'sport'=>2,'showChoose'=>1])
            @endcomponent
        @endforeach
    </div>
@endsection

@section('match_list_date')
    <div class="abox">
        <ul>
            <a class="li on">即时比分</a>
            <a class="li" href="/match/basket/schedule/{{$lastDate}}/result_t.html">完场赛果</a>
            <a class="li" href="/match/basket/schedule/{{$nextDate}}/schedule_t.html">未来赛程</a>
        </ul>
    </div>
@endsection

@section('css_match_list')
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/pc/css/immediate_bk.css">
@endsection
@section('js_match_list')
    <script type="text/javascript" src="{{$cdn}}/pc/js/immediate.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            setPage();
        }
    </script>
@endsection