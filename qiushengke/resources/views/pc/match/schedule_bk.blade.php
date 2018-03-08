@extends('pc.layout.matchlist_bk')
@section('match_list_content')
    <div class="ConInner">
        <div id="Calendar">
            <ul>
                @foreach($calendar as $item)
                    <a class="li {{$item['on'] ? 'on':''}}" href="{{'/match/basket/schedule_'.$item['date'].'_t.html'}}">
                        <p class="date">{{$item['dateStr']}}</p>
                        <p class="week">{{$item['w']}}</p>
                    </a>
                @endforeach
            </ul>
            <input type="text" name="date" placeholder="请选择日期">
        </div>
        <div id="Control">
            <div class="inbox">
                <p class="array"><button id="array_time" class="on" onclick="changeBKFilter('time')">按时间排序</button><button id="array_league" onclick="changeBKFilter('league')">按联赛排序</button></p>
                <p class="column">
                    <button id="column_nba" class="on" onclick="matchFilter('nba')">NBA</button><button id="column_live" onclick="matchFilter('live')">直播</button><button id="column_all" onclick="matchFilter('all')">完整</button>
                </p>
                <p class="number">共<b>{{$total}}</b>场&nbsp;隐藏<b id="hideMatchCount">-</b>场<span>【显示】</span></p>
                <p class="filter"><button class="league">选择赛事</button></p>
            </div>
        </div>
        @foreach($matches as $match)
            @component('pc.cell.match_list_cell_bk',['match'=>$match,'sport'=>2,'showChoose'=>0])
            @endcomponent
        @endforeach
    </div>
@endsection

@section('match_list_date')
    <div class="abox">
        <ul>
            <a class="li" href="/match/basket/immediate_t.html">即时比分</a>
            <a class="li" href="/match/basket/result_{{$lastDate}}_t.html">完场赛果</a>
            <a class="li on">未来赛程</a>
        </ul>
    </div>
@endsection

@section('css_match_list')
    <link rel="stylesheet" type="text/css" href="/pc/css/immediate_bk.css">
@endsection
@section('js_match_list')
    <script type="text/javascript" src="/pc/js/immediate.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            setPage();
        }
    </script>
@endsection