@extends('phone.layout.base')
@section('content')
    <div id="Navigation">
        <div class="tab">
            <a class="on">即时比分</a>
            <a href="/wap/match/basket/schedule/{{$lastDate}}/result_t.html">完场赛果</a>
            <a href="/wap/match/basket/schedule/{{$nextDate}}/schedule_t.html">未来赛程</a>
            {{--<a href="hotleague.html">热门赛事</a>--}}
        </div>
        <div class="filter">
            <p class="in league">重要赛事</p>
            <p class="in odd">全部盘口</p>
            <p class="num">共<span>251</span>场&nbsp;&nbsp;隐藏<span>52</span>场</p>
        </div>
    </div>
    <div id="List">
        @foreach($matches as $match)
            @component('phone.cell.match_list_cell_bk',['match'=>$match,'sport'=>$sport])
            @endcomponent
        @endforeach
    </div>
    <div class="filterBox" id="LeagueFilter" style="display: none;">
        <div class="default">
            <div class="tab">
                <input type="radio" name="leagueFilter" id="LeagueImportant" value="Important" checked><label for="LeagueImportant">重要赛事</label>
                <input type="radio" name="leagueFilter" id="LeagueLottery" value="Lottery"><label for="LeagueLottery">竞彩赛事</label>
                <input type="radio" name="leagueFilter" id="LeagueAll" value="All"><label for="LeagueAll">全部赛事</label>
                <input type="radio" name="leagueFilter" id="LeagueSelf" value="Self"><label for="LeagueSelf">自定义</label>
            </div>
            <ul id="Important">
                <li><input type="checkbox" name="league" id="Important_111" disabled checked><label for="Important_111">中超</label></li>
                <li><input type="checkbox" name="league" id="Important_222" disabled checked><label for="Important_222">中超</label></li>
                <li><input type="checkbox" name="league" id="Important_333" disabled checked><label for="Important_333">中超</label></li>
                <li><input type="checkbox" name="league" id="Important_444" disabled checked><label for="Important_444">中超</label></li>
                <li><input type="checkbox" name="league" id="Important_555" disabled checked><label for="Important_555">中超</label></li>
                <li><input type="checkbox" name="league" id="Important_666" disabled checked><label for="Important_666">中超</label></li>
                <li><input type="checkbox" name="league" id="Important_777" disabled checked><label for="Important_777">中超</label></li>
            </ul>
            <ul id="Lottery" style="display: none;">
                <li><input type="checkbox" name="league" id="Lottery_111" disabled checked><label for="Lottery_111">中超</label></li>
                <li><input type="checkbox" name="league" id="Lottery_222" disabled checked><label for="Lottery_222">中超</label></li>
                <li><input type="checkbox" name="league" id="Lottery_333" disabled checked><label for="Lottery_333">中超</label></li>
                <li><input type="checkbox" name="league" id="Lottery_444" disabled checked><label for="Lottery_444">中超</label></li>
                <li><input type="checkbox" name="league" id="Lottery_555" disabled checked><label for="Lottery_555">中超</label></li>
                <li><input type="checkbox" name="league" id="Lottery_666" disabled checked><label for="Lottery_666">中超</label></li>
                <li><input type="checkbox" name="league" id="Lottery_777" disabled checked><label for="Lottery_777">中超</label></li>
            </ul>
            <ul id="All" style="display: none;">
                <li><input type="checkbox" name="league" id="All_111" disabled checked><label for="All_111">中超</label></li>
                <li><input type="checkbox" name="league" id="All_222" disabled checked><label for="All_222">中超</label></li>
                <li><input type="checkbox" name="league" id="All_333" disabled checked><label for="All_333">中超</label></li>
                <li><input type="checkbox" name="league" id="All_444" disabled checked><label for="All_444">中超</label></li>
                <li><input type="checkbox" name="league" id="All_555" disabled checked><label for="All_555">中超</label></li>
                <li><input type="checkbox" name="league" id="All_666" disabled checked><label for="All_666">中超</label></li>
                <li><input type="checkbox" name="league" id="All_777" disabled checked><label for="All_777">中超</label></li>
            </ul>
            <ul id="Self" style="display: none;">
                <li><input type="checkbox" name="league" id="Self_111" checked><label for="Self_111">中超</label></li>
                <li><input type="checkbox" name="league" id="Self_222" checked><label for="Self_222">中超</label></li>
                <li><input type="checkbox" name="league" id="Self_333" checked><label for="Self_333">中超</label></li>
                <li><input type="checkbox" name="league" id="Self_444" checked><label for="Self_444">中超</label></li>
                <li><input type="checkbox" name="league" id="Self_555" checked><label for="Self_555">中超</label></li>
                <li><input type="checkbox" name="league" id="Self_666" checked><label for="Self_666">中超</label></li>
                <li><input type="checkbox" name="league" id="Self_777" checked><label for="Self_777">中超</label></li>
            </ul>
            <div class="comfirmLine">
                <input type="checkbox" name="liveOnly" id="liveOnlyLeague">
                <label for="liveOnlyLeague">只显示有直播信号</label>
                <button class="comfirm">确认</button>
            </div>
            <button class="close"></button>
        </div>
    </div>
    <div class="filterBox" id="OddFilter" style="display: none;">
        <div class="default">
            <div class="tab">
                <input type="radio" name="oddFilter" id="OddAsia" value="Asia" checked><label for="OddAsia">亚盘</label>
                <input type="radio" name="oddFilter" id="OddGoal" value="Goal"><label for="OddGoal">大小球</label>
            </div>
            <ul id="Asia">
                <li><input type="checkbox" name="league" id="Asia_111" checked><label for="Asia_111">未开盘</label></li>
                <li><input type="checkbox" name="league" id="Asia_222" checked><label for="Asia_222">平手</label></li>
                <div class="line"></div>
                <li><input type="checkbox" name="league" id="Asia_333" checked><label for="Asia_333">让平手/半球</label></li>
                <li><input type="checkbox" name="league" id="Asia_444" checked><label for="Asia_444">让半球</label></li>
                <li><input type="checkbox" name="league" id="Asia_555" checked><label for="Asia_555">让半球/一球</label></li>
                <li><input type="checkbox" name="league" id="Asia_666" checked><label for="Asia_666">让一球</label></li>
                <li><input type="checkbox" name="league" id="Asia_777" checked><label for="Asia_777">让一球/球半</label></li>
                <li><input type="checkbox" name="league" id="Asia_888" checked><label for="Asia_888">让一球半</label></li>
                <li><input type="checkbox" name="league" id="Asia_999" checked><label for="Asia_999">让一球半/两球</label></li>
                <li><input type="checkbox" name="league" id="Asia_000" checked><label for="Asia_000">让两球</label></li>
                <li><input type="checkbox" name="league" id="Asia_101" checked><label for="Asia_101">让两球/两球半</label></li>
                <div class="line"></div>
                <li><input type="checkbox" name="league" id="Asia_33" checked><label for="Asia_33">受平手/半球</label></li>
                <li><input type="checkbox" name="league" id="Asia_44" checked><label for="Asia_44">受半球</label></li>
                <li><input type="checkbox" name="league" id="Asia_55" checked><label for="Asia_55">受半球/一球</label></li>
                <li><input type="checkbox" name="league" id="Asia_66" checked><label for="Asia_66">受一球</label></li>
                <li><input type="checkbox" name="league" id="Asia_77" checked><label for="Asia_77">受一球/球半</label></li>
                <li><input type="checkbox" name="league" id="Asia_88" checked><label for="Asia_88">受一球半</label></li>
                <li><input type="checkbox" name="league" id="Asia_99" checked><label for="Asia_99">受一球半/两球</label></li>
                <li><input type="checkbox" name="league" id="Asia_00" checked><label for="Asia_00">受两球</label></li>
                <li><input type="checkbox" name="league" id="Asia_10" checked><label for="Asia_10">受两球/两球半</label></li>
            </ul>
            <ul id="Goal" style="display: none;">
                <li><input type="checkbox" name="league" id="Goal_111" checked><label for="Goal_111">未开盘</label></li>
                <div class="line"></div>
                <li><input type="checkbox" name="league" id="Goal_222" checked><label for="Goal_222">2球以下</label></li>
                <li><input type="checkbox" name="league" id="Goal_333" checked><label for="Goal_333">2球</label></li>
                <li><input type="checkbox" name="league" id="Goal_444" checked><label for="Goal_444">2/2.5球</label></li>
                <li><input type="checkbox" name="league" id="Goal_555" checked><label for="Goal_555">2.5球</label></li>
                <li><input type="checkbox" name="league" id="Goal_666" checked><label for="Goal_666">2.5/3球</label></li>
                <li><input type="checkbox" name="league" id="Goal_777" checked><label for="Goal_777">3球</label></li>
                <li><input type="checkbox" name="league" id="Goal_888" checked><label for="Goal_888">3/3.5球</label></li>
                <li><input type="checkbox" name="league" id="Goal_999" checked><label for="Goal_999">3.5球</label></li>
                <li><input type="checkbox" name="league" id="Goal_000" checked><label for="Goal_000">3.5球以上</label></li>
            </ul>
            <div class="comfirmLine">
                <input type="checkbox" name="liveOnly" id="liveOnlyGoal">
                <label for="liveOnlyGoal">只显示有直播信号</label>
                <button class="comfirm">确认</button>
            </div>
            <button class="close"></button>
        </div>
    </div>
    @component('phone.layout.bottom',['index'=>1,'cdn'=>$cdn])
    @endcomponent
@endsection
@section('js')
    <script type="text/javascript" src="{{$cdn}}/phone/js/immediate.js"></script>
    <script type="text/javascript">
    </script>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/phone/css/immediate_bk.css">
@endsection