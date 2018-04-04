@extends('pc.layout.matchlist')
@section('match_list_content')
    <div class="ConInner">
        <div id="Calendar">
            <ul>
                @foreach($calendar as $item)
                    <a class="li {{$item['on'] ? 'on':''}}" href="{{'/match/foot/schedule/'.$item['date'].'/schedule.html'}}">
                        <p class="date">{{$item['dateStr']}}</p>
                        <p class="week">{{$item['w']}}</p>
                    </a>
                @endforeach
            </ul>
            <input type="text" name="date" placeholder="{{$currDate}}" onchange="clickDate(this)">
        </div>
        <div id="Control">
            <div class="inbox">
                <p class="column">
                    <button id="column_first" class="on" onclick="matchFilter('first')">精简</button><button id="column_lottery" onclick="matchFilter('lottery')">竞彩</button><button id="column_live" onclick="matchFilter('live')">直播</button><button id="column_all" onclick="matchFilter('all')">完整</button>
                </p>
                <p class="number">共<b>{{$total}}</b>场&nbsp;隐藏<b id="hideMatchCount">-</b>场<span  onclick="matchFilter('all')">【显示】</span></p>
                <p class="filter"><button class="league">选择赛事</button><button class="odd">选择盘路</button></p>
            </div>
        </div>
        <table id="Table">
            <colgroup>
                <col num="1" width="90px">
                <col num="2" width="50px">
                <col num="3">
                <col num="4" width="35px">
                <col num="5" width="60px">
                <col num="6" width="35px">
                <col num="7">
                <col num="8" width="50px">
                <col num="9" width="210px">
                <col num="10" width="140px">
                <col num="11" width="90px">
            </colgroup>
            <thead>
            <tr>
                <th>赛事</th>
                <th>时间</th>
                <th colspan="5">对阵</th>
                <th>直播</th>
                <th>亚盘指数</th>
                <th>大小球指数</th>
                <th>分析</th>
            </tr>
            </thead>
            @if(isset($matches) && count($matches))
                <tbody id="End">
                @foreach($matches as $match)
                    @component('pc.cell.match_list_schedule_cell',['match'=>$match,'sport'=>$sport])
                    @endcomponent
                @endforeach
                </tbody>
            @endif
        </table>
        <div id="Simulation">
            <table>
                <colgroup>
                    <col num="1" width="90px">
                    <col num="2" width="50px">
                    <col num="3">
                    <col num="4" width="50px">
                    <col num="5" width="210px">
                    <col num="6" width="140px">
                    <col num="7" width="90px">
                </colgroup>
                <thead>
                <tr>
                    <th>赛事</th>
                    <th>时间</th>
                    <th>对阵</th>
                    <th>直播</th>
                    <th>亚盘指数</th>
                    <th>大小球指数</th>
                    <th>分析</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('match_list_date')
    <div class="abox">
        <ul>
            <a class="li" href="/match/foot/schedule/immediate.html">即时比分</a>
            <a class="li" href="/match/foot/schedule/{{$lastDate}}/result.html">完场赛果</a>
            <a class="li on" href="/match/foot/schedule/{{$nextDate}}/schedule.html">未来赛程</a>
        </ul>
    </div>
@endsection

@section('css_match_list')
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/pc/css/schedule.css">
@endsection
@section('js_match_list')
    <script type="text/javascript" src="{{$cdn}}/pc/js/immediate.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            setPage();
        }
        function clickDate(date) {
            var value = date.value.replace(/-/g,"");
            var url = '{{env('APP_URL')}}' + '/match/foot/schedule/'+value+'/schedule.html';
            window.location.href = url;
        }
    </script>
@endsection