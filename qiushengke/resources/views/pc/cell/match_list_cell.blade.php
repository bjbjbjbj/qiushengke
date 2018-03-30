<?php
$status = $match['status'];
$mid = $match['mid'];
$lid = $match['lid'];

$matchTime = \App\Http\Controllers\PC\CommonTool::getMatchCurrentTime($match['time'],$match['timehalf'],$match['status']);;

$matchUrl = \App\Http\Controllers\PC\CommonTool::matchPathWithId($mid,$sport);
if (empty($matchTime)) {
    $lineup = $match['has_lineup'];//首发 \App\Models\Match\MatchLineup::query()->find($match->id);//
}
$hasLineup = isset($lineup)?$lineup:false;
//推荐数
$articleCount = isset($articlesCount[$mid])?$articlesCount[$mid]:0;

//角球比分
$cornerScore = "-";
//半场比分
$halfScore = "";
if ($status > 0 || $status == -1) {
    $halfScore = ($status == 1) ? "" : ('（'.$match['hscorehalf'] . " - " . $match['ascorehalf'].'）');

    if (isset($match['h_corner'])) {
        $cornerScore = $match['h_corner'] . " - " . $match['a_corner'];
    }
}

//亚赔
$asiaUp = "-";
$asiaMiddle = "-";
$asiaDown = "-";
//    $oddAsia = $match->oddAsian();
if (isset($match['asiamiddle2'])) {
    $asiaUp = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['asiaup2']);
    $asiaDown = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['asiadown2']);
    $asiaMiddle = \App\Http\Controllers\PC\CommonTool::getHandicapCn($match['asiamiddle2'], "-", \App\Http\Controllers\PC\CommonTool::k_odd_type_asian);
}

//大小球
$goalUp = "-";
$goalMiddle = "-";
$goalDown = "-";
//    $oddOu = $match->oddOU();
if (isset($match['goalmiddle2'])) {
    $goalUp = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['goalup2']);
    $goalDown = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['goaldown2']);
    $goalMiddle = \App\Http\Controllers\PC\CommonTool::getHandicapCn($match['goalmiddle2'], "-", \App\Http\Controllers\PC\CommonTool::k_odd_type_ou);
}

//欧赔
$ouUp = "-";
$ouMiddle = "-";
$ouDown = "-";
//    $oddOu = $match->oddOU();
if (isset($match['oumiddle2'])) {
    $ouUp = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['ouup2']);
    $ouDown = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['oudown2']);
    $ouMiddle = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['oumiddle2']);
}

//odd模块数据
$cell_odd = array();
$cell_odd['all']['1']['up1'] = $match['asiaup1'];
$cell_odd['all']['1']['middle1'] = $match['asiamiddle1'];
$cell_odd['all']['1']['down1'] = $match['asiadown1'];
$cell_odd['all']['2']['up1'] = $match['goalup1'];
$cell_odd['all']['2']['middle1'] = $match['goalmiddle1'];
$cell_odd['all']['2']['down1'] = $match['goaldown1'];
$cell_odd['all']['3']['up1'] = $match['ouup1'];
$cell_odd['all']['3']['middle1'] = $match['oumiddle1'];
$cell_odd['all']['3']['down1'] = $match['oudown1'];

$cell_odd['all']['1']['up2'] = $match['asiaup2'];
$cell_odd['all']['1']['middle2'] = $match['asiamiddle2'];
$cell_odd['all']['1']['down2'] = $match['asiadown2'];
$cell_odd['all']['2']['up2'] = $match['goalup2'];
$cell_odd['all']['2']['middle2'] = $match['goalmiddle2'];
$cell_odd['all']['2']['down2'] = $match['goaldown2'];
$cell_odd['all']['3']['up2'] = $match['ouup2'];
$cell_odd['all']['3']['middle2'] = $match['oumiddle2'];
$cell_odd['all']['3']['down2'] = $match['oudown2'];

//赛事背景色
$bgRgb = \App\Http\Controllers\PC\CommonTool::getLeagueBgRgb($lid);
$r = $bgRgb['r'];
$g = $bgRgb['g'];
$b = $bgRgb['b'];

$asiaOddArray = \App\Http\Controllers\PC\CommonTool::getMatchOdds($match['asiamiddle2'], \App\Http\Controllers\PC\CommonTool::k_odd_type_asian);
$typeCn = $asiaOddArray['typeCn'];
if (str_contains($typeCn, "受")) {
    $asiaOdd = "asiaDown_" . $asiaOddArray['sort'];
} elseif($typeCn == '未开盘' || $typeCn == '平手') {
    $asiaOdd = "asiaMiddle_" . $asiaOddArray['sort'];
} else {
    $asiaOdd = "asiaUp_" . $asiaOddArray['sort'];
}
$ouOddArray = \App\Http\Controllers\PC\CommonTool::getMatchOdds($match['goalmiddle2'], \App\Http\Controllers\PC\CommonTool::k_odd_type_ou);
$ouOdd = "ou_" . $ouOddArray['sort'];

//是否是一级赛事
$isFirst = ($match['genre'] >> 1 & 1) == 1;
//是否是竞彩
$isLottery = isset($match['betting_num']);

//比赛事件

//默认是否显示
$show = true;

$hasLive = $match['pc_live'];

$liveUrl = \App\Http\Controllers\PC\CommonTool::matchLivePathWithId($match['mid']);
?>
<tr isMatch="1" class="{{$show?'show':'hide'}}" id="m_tr_{{$mid}}" match="{{$mid}}" league="{{$lid}}" asiaOdd="{{$asiaOdd}}" ouOdd="{{$ouOdd}}" first="{{$isFirst?"first":""}}" lottery="{{$isLottery?"lottery":""}}" live="{{$hasLive?"live":""}}">
    <td><button name="match" class="choose" value="0" mid="{{$mid}}" name="match" id="match_{{$mid}}" onclick="clickMatchBtn(this)"></button></td>
    <td><p class="leagueLine" style="background: rgb({{$r}}, {{$g}}, {{$b}});"></p>{{$match['league']}}</td>
    <td id="date_{{$mid}}">{{date('H:i', $match['time'])}}
    </td>
    <td id="time_{{$mid}}">{!! $matchTime !!}</td>
    <td id="h_team_{{$mid}}" class="host">
        <a class="team" href="{{$matchUrl}}" target="_blank">
                <span
                        @if($match['h_red'] > 0)
                        class="redCard"
                        @else
                        class="redCard hide"
                        @endif
                        id="{{$mid}}_h_red">{{$match['h_red']}}</span>
                <span
                        @if($match['h_yellow'] > 0)
                        class="yellowCard"
                        @else
                        class="yellowCard hide"
                        @endif
                        id="{{$mid}}_h_yellow">{{$match['h_yellow']}}</span>
            <span class="name">{{$match['hname']}}</span>
        </a>
    </td>
    <td><a href="{{$matchUrl}}" target="_blank"><img alt="{{$match['hname']}}" id="{{$mid}}_h_icon" class="icon" src="{{strlen($match['hicon'])>0?$match['hicon'] : (env('CDN_URL') . '/pc/img/icon_teamDefault.png')}}"></a></td>
    <td>
        <a href="{{$matchUrl}}" target="_blank"
           @if($match['status'] == -1 || $match['status'] > 0)
           onmouseover="getMousePos(this); ct=window.setInterval('refreshMatchTech(\'{{$mid}}\')',200)" onmouseout="window.clearInterval(ct)"
                @endif
        >
            <p class="fullScore" id="score_{{$mid}}">
                @if($status == 0)
                    VS
                @elseif($status == -1 || $status > 0)
                    {{$match['hscore'] .' - '. $match['ascore']}}
                @else
                    {{\App\Http\Controllers\PC\CommonTool::getStatusTextCn($status)}}
                @endif
            </p>
            @if($status == -1 || $status > 0)
                <p class="halfScore" id="half_score_{{$mid}}">半 {{$match['hscorehalf'] .' - '. $match['ascorehalf']}}</p>
            @endif
        </a>
        <div class="even">
            <p class="team">
                <span>{{$match['hname']}}</span>
                <span>{{$match['aname']}}</span>
            </p>
            <div id="{{$mid}}_eboxCon">

            </div>
            <div id="{{$mid}}_tboxCon">

            </div>
        </div>
    </td>
    <td><a href="{{$matchUrl}}" target="_blank"><img alt="{{$match['aname']}}"id="{{$mid}}_a_icon"  class="icon" src="{{strlen($match['aicon'])>0?$match['aicon'] : (env('CDN_URL') . '/pc/img/icon_teamDefault.png')}}"></a></td>
    <td class="away">
        <a class="team" href="{{$matchUrl}}" target="_blank">
                <span
                        @if($match['a_red'] > 0)
                        class="redCard"
                        @else
                        class="redCard hide"
                        @endif
                        id="{{$mid}}_a_red">{{$match['a_red']}}</span>
            <span
                    @if($match['a_yellow'] > 0)
                    class="yellowCard"
                    @else
                    class="yellowCard hide"
                    @endif
                    id="{{$mid}}_a_yellow">{{$match['a_yellow']}}</span>
            <span class="name">{{$match['aname']}}</span>
        </a>
    </td>
    <td id="ch_score_{{$mid}}">{{$cornerScore}}</td>
    <td>
        {{--比赛状态 直播方式--}}
        @if($status == 0)
            @if($hasLive)
                <a class="live" href="{{$liveUrl}}" target="_blank"><img id="live_{{$match['mid']}}" src="{{env('CDN_URL')}}/pc/img/icon_living.png"></a>
            @else
                <a class="live" href="{{$liveUrl}}" target="_blank"><img id="live_{{$match['mid']}}" src="{{env('CDN_URL')}}/pc/img/icon_action_light.png"></a>
            @endif
        @elseif($status == -1)
            @if($hasLive)
                <a class="live" href="{{$matchUrl}}" target="_blank"><img id="live_{{$match['mid']}}" src="{{env('CDN_URL')}}/pc/img/icon_lived.png"></a>
            @else
                <a class="live" href="{{$matchUrl}}" target="_blank"><img id="live_{{$match['mid']}}" src="{{env('CDN_URL')}}/pc/img/icon_action_grey.png"></a>
            @endif
        @else
            @if($hasLive)
                <a class="live video" href="{{$liveUrl}}" target="_blank">直播中</a>
            @else
                <a class="live flash" href="{{$liveUrl}}" target="_blank">动画</a>
            @endif
        @endif
    </td>
    <td>
        <a
                onmouseover="getMousePos(this); ct2=window.setInterval('refreshOddByMid(\'{{$mid}}\')',200)" onmouseout="window.clearInterval(ct2)"
        >
        <p class="asia">
            <span class="" value="{{$asiaUp}}">{{$asiaUp}}</span><span class="odd" value="{{$match['asiamiddle2']}}">{{$asiaMiddle}}</span><span class="" value="{{$asiaDown}}">{{$asiaDown}}</span>
        </p>
        <p class="goal">
            <span value="{{$goalUp}}">{{$goalUp}}</span><span class="odd" value="{{$match['goalmiddle2']}}">{{$goalMiddle}}</span><span value="{{$goalDown}}">{{$goalDown}}</span>
        </p>
        <div class="odd">
            <p class="league">{{$match['league']}}&nbsp;{{isset($match['round'])?'&nbsp;第'.$match['round'].'轮':''}}</p>
            <table>
                @component('pc.cell.match_list_cell_odd',['cell_odd'=>$cell_odd,'mid'=>$mid])
                @endcomponent
            </table>
        </div>
        </a>
    </td>
    <td>
        <a target="_blank" href="{{$matchUrl}}">析</a>
        <a target="_blank" href="/match/foot/odd.html?mid={{$mid}}&type=1">亚</a>
        <a target="_blank" href="/match/foot/odd.html?mid={{$mid}}&type=2">欧</a>
        <a target="_blank" href="/match/foot/odd.html?mid={{$mid}}&type=3">大</a>
    </td>
    {{--<td><button class="top" value="{{$isTop}}"></button></td>--}}
</tr>