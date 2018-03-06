@extends('pc.layout.matchlist')
@section('match_list_content')
    <div class="ConInner">
        <div id="Control">
            <div class="inbox">
                <p class="save"><button onclick="confirmFilter('match',false)">保留</button><button onclick="confirmFilter('match',true)">删除</button></p>
                <p class="column">
                    <button id="column_first" class="on" onclick="matchFilter('first')">精简</button><button id="column_lottery" onclick="matchFilter('lottery')">竞彩</button><button id="column_live" onclick="matchFilter('live')">直播</button><button id="column_all" onclick="matchFilter('all')">完整</button>
                </p>
                <p class="number">共<b>{{$total}}</b>场&nbsp;隐藏<b id="hideMatchCount">-</b>场<span>【显示】</span></p>
                <div class="sound">
                    <button>进球声</button>
                    <ul>
                        <li class="on">进球声</li>
                        <li>静音</li>
                    </ul>
                </div>
                <p class="filter"><button class="league">选择赛事</button><button class="odd">选择盘路</button></p>
            </div>
        </div>
        <table id="Table">
            <colgroup>
                <col num="1" width="40px">
                <col num="2" width="90px">
                <col num="3" width="50px">
                <col num="4" width="60px">
                <col num="5">
                <col num="6" width="35px">
                <col num="7" width="60px">
                <col num="8" width="35px">
                <col num="9">
                <col num="10" width="60px">
                <col num="11" width="60px">
                <col num="12" width="186px">
                <col num="13" width="80px">
                <col num="14" width="40px">
            </colgroup>
            <thead>
            <tr>
                <th><button class="choose" value="0" onclick="clickAll(this)"></button></th>
                <th>赛事</th>
                <th>时间</th>
                <th>状态</th>
                <th colspan="5">对阵</th>
                <th>角球</th>
                <th>直播</th>
                <th>指数</th>
                <th>分析</th>
                <th>顶</th>
            </tr>
            </thead>
            @if(isset($topMatches) && count($topMatches))
                <tbody id="Top" name="match" class="hide">
                @foreach($topMatches as $match)
                    @component('pc.cell.match_list_cell',['match'=>$match,'sport'=>$sport,'isTop'=>1])
                    @endcomponent
                @endforeach
                </tbody>
            @endif
            @if(isset($liveMatches) && count($liveMatches))
                <tbody id="Live" name="match" class="hide">
                <tr>
                    <th colspan="14"><p>正在比赛</p></th>
                </tr>
                @foreach($liveMatches as $match)
                    @component('pc.cell.match_list_cell',['match'=>$match,'sport'=>$sport,'isTop'=>0])
                    @endcomponent
                @endforeach
                </tbody>
            @endif
            @if(isset($afterMatches) && count($afterMatches))
                <tbody id="After" name="match" class="hide">
                <tr>
                    <th colspan="14"><p>稍后比赛</p></th>
                </tr>
                @foreach($afterMatches as $match)
                    @component('pc.cell.match_list_cell',['match'=>$match,'sport'=>$sport,'isTop'=>0])
                    @endcomponent
                @endforeach
                </tbody>
            @endif
            @if(isset($endMatches) && count($endMatches))
                <tbody id="End" name="match" class="hide">
                <tr>
                    <th colspan="14"><p>完场赛事</p></th>
                </tr>
                @foreach($endMatches as $match)
                    @component('pc.cell.match_list_cell',['match'=>$match,'sport'=>$sport,'isTop'=>0])
                    @endcomponent
                @endforeach
                </tbody>
            @endif
        </table>
        <div id="Simulation">
            <table>
                <colgroup>
                    <col num="1" width="40px">
                    <col num="2" width="90px">
                    <col num="3" width="50px">
                    <col num="4" width="60px">
                    <col num="5">
                    <col num="10" width="60px">
                    <col num="11" width="60px">
                    <col num="12" width="186px">
                    <col num="13" width="80px">
                    <col num="14" width="40px">
                </colgroup>
                <thead>
                <tr>
                    <th><button class="choose" value="0"></button></th>
                    <th>赛事</th>
                    <th>时间</th>
                    <th>状态</th>
                    <th>对阵</th>
                    <th>角球</th>
                    <th>直播</th>
                    <th>指数</th>
                    <th>分析</th>
                    <th>顶</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('match_list_date')
    <div class="abox">
        <ul>
            <a class="li on">即时比分</a>
            <a class="li" href="/match/foot/result_{{$lastDate}}.html">完场赛果</a>
            <a class="li" href="/match/foot/schedule_{{$nextDate}}.html">未来赛程</a>
        </ul>
    </div>
@endsection

@section('css_match_list')
    <link rel="stylesheet" type="text/css" href="/pc/css/immediate.css">
@endsection
@section('js_match_list')
    <script type="text/javascript" src="/pc/js/immediate.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            setPage();
        }
    </script>
@endsection