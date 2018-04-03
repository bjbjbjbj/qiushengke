<?php
$status = $match['status'];
$mid = $match['mid'];
$lid = $match['lid'];
//赛制
//半全场赛制
$isHalfFormat = $match['system'] == 1;
$matchTime = \App\Http\Controllers\PC\CommonTool::getBasketCurrentTime($status, $match['live_time_str'], $isHalfFormat);
$matchUrl = \App\Http\Controllers\PC\CommonTool::matchPathWithId($mid,2);

//亚赔
$asiaUp = "";
$asiaMiddle = "";
$asiaDown = "";
//    $oddAsia = $match->oddAsian();
if (isset($match['asiamiddle2'])) {
    $asiaUp = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['asiaup2']);
    $asiaDown = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['asiadown2']);
    $asiaMiddle = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['asiamiddle2'], true);
}

//大小分
$ouUp = "";
$ouMiddle = "";
$ouDown = "";
//    $oddOu = $match->oddOU();
if (isset($match['goalup2'])) {
    $ouUp = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['goalup2']);
    $ouDown = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['goaldown2']);
    $ouMiddle = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['goalmiddle2'], true);
}
//欧赔
$europeUp = "";
$europeDown = "";
//    $oddOu = $match->oddOU();
if (isset($match['oumiddle2'])) {
    $europeUp = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['ouup2']);
    $europeDown = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['oudown2']);
}

//赛事背景色
if(isset($match['color'])){
    $r = hexdec(substr($match['color'],0,2));
    $g = hexdec(substr($match['color'],2,2));
    $b = hexdec(substr($match['color'],4,2));
}
else{
    $bgRgb = \App\Http\Controllers\PC\CommonTool::getLeagueBgRgb($lid);
    $r = $bgRgb['r'];
    $g = $bgRgb['g'];
    $b = $bgRgb['b'];
}

//是否是NBA
$isNBA = $lid == 1;
//是否是竞彩
$isLottery = isset($match['betting_num']);

//半场比分
$h_half = \App\Http\Controllers\PC\CommonTool::getBasketHalfScoreTxt($match, true);
$a_half = \App\Http\Controllers\PC\CommonTool::getBasketHalfScoreTxt($match, false);;

//分差
$half_diff = \App\Http\Controllers\PC\CommonTool::getBasketScoreTxt($match, true, true);
$whole_diff = \App\Http\Controllers\PC\CommonTool::getBasketScoreTxt($match, false, true);

//总分
$half_total = \App\Http\Controllers\PC\CommonTool::getBasketScoreTxt($match, true, false);
$whole_total = \App\Http\Controllers\PC\CommonTool::getBasketScoreTxt($match, false, false);

//加时
$h_ots = is_array($match['h_ot']) ? $match['h_ot'] : array();
$a_ots = is_array($match['a_ot']) ? $match['h_ot'] : array();
$otCount = min(count($h_ots), count($a_ots));

//默认是否显示
$show = true;
$league_name = isset($match['league']) ? $match['league'] : '';
$hasLive = $match['live'];

$liveUrl = \App\Http\Controllers\PC\CommonTool::matchLivePathWithId($match['mid'],2);
?>
<table isMatch="1" class="{{$show?'show':'hide'}}" id="m_table_{{$mid}}" match="{{$mid}}" league="{{$lid}}" nba="{{$isNBA?"nba":""}}" lottery="{{$isLottery?"lottery":""}}" live="{{$hasLive?"live":""}}">
    <thead>
    <tr>
        <th class="league"><p id="status_{{$mid}}" class="{{$status > 0 ? "live":""}}">
                @if($showChoose)
                <button name="match" class="choose" value="0" mid="{{$mid}}" name="match" id="match_{{$mid}}" class="choose" onclick="clickMatchBtn(this)"></button>
                @endif
                {{$league_name}}</p></th>
        <th class="team">
            @if($status == 0)
                <span id="time_{{$mid}}">未开始</span>
            @elseif($status == -1)
                <span id="time_{{$mid}}">已结束</span>
            @else
                <span id="time_{{$mid}}">{{$matchTime}}</span>
            @endif
        </th>
        @if($isHalfFormat)
            <th>上半场</th>
            <th>下半场</th>
        @else
            @if($otCount > 1)
                <th class="th_name">一</th>
                <th class="th_name">二</th>
                <th class="th_name">三</th>
                <th class="th_name">四</th>
            @else
                <th class="th_name">一节</th>
                <th class="th_name">二节</th>
                <th class="th_name">三节</th>
                <th class="th_name">四节</th>
            @endif
        @endif
        @for($i = 0; $i < 2; $i++)
            <th
                    @if($otCount > $i)
                    class="period"
                    @else
                    class="period hide"
                    @endif
                    name="ot_{{$i+1}}">OT{{$i+1}}</th>
        @endfor
        <th class="half">上下</th>
        <th class="full">全场</th>
        <th class="difference">分差</th>
        <th class="total">总分</th>
        <th class="europe">欧赔</th>
        <th class="asia">让分</th>
        <th class="goal">大小分</th>
        <th class="data">数据</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td rowspan="2">
            <p class="time">{{date('n月j日 H:i', $match['time'])}}</p>
            @if($hasLive)
                @if($match['status'] > 0)
                    <a id="live_{{$mid}}" class="live" href="{{$liveUrl}}" target="_blank"><span>直播中</span></a>
                @else
                    <a id="live_{{$mid}}" class="live" href="{{$liveUrl}}" target="_blank"><img src="/pc/img/icon_living.png"></a>
                @endif
            @endif
        </td>
        <td class="team"><img src="{{\App\Http\Controllers\PC\CommonTool::getIconBK($match['hicon'])}}"><p>{{$match['hname']}}</p></td>
        <td class="score" id="h_score1_{{$mid}}">{{\App\Http\Controllers\PC\CommonTool::getBasketScore($match['hscore_1st'])}}</td>
        @if(!$isHalfFormat)
        <td class="score" id="h_score2_{{$mid}}">{{\App\Http\Controllers\PC\CommonTool::getBasketScore($match['hscore_2nd'])}}</td>
        @endif
        <td class="score" id="h_score3_{{$mid}}">{{\App\Http\Controllers\PC\CommonTool::getBasketScore($match['hscore_3rd'])}}</td>
        @if(!$isHalfFormat)
        <td class="score" id="h_score4_{{$mid}}">{{\App\Http\Controllers\PC\CommonTool::getBasketScore($match['hscore_4th'])}}</td>
        @endif
        @for($i = 0; $i < 2; $i++)
            @if($otCount > $i)
                <td class="score" id="h_ot{{1+$i}}_{{$mid}}"><p>{{$h_ots[$i]}}</p></td>
            @else
                <td class="hide" id="h_ot{{1+$i}}_{{$mid}}"><p></p></td>
            @endif
        @endfor
        <td class="score" id="h_score_half_{{$mid}}">{{$h_half}}</td>
        <td class="score fullScore" id="h_score_full_{{$mid}}">{{\App\Http\Controllers\PC\CommonTool::getBasketScore($match['hscore'])}}</td>
        <td id="score_half_diff_{{$mid}}">{{$half_diff}}</td>
        <td id="score_half_total_{{$mid}}">{{$half_total}}</td>
        <td class="ou"><span>{{\App\Http\Controllers\PC\CommonTool::float2Decimal($match['ouup2'])}}</span></td>
        <td class="asia">
            @if($asiaMiddle >= 0)
                <span>{{$asiaMiddle}}</span>
            @endif
            <p class="">{{$asiaUp}}</p>
        </td>
        <td class="goal">
            <span>{{$ouMiddle>0?'大 '.$ouMiddle:''}}</span>
            <p class="">{{$ouUp}}</p>
        </td>
        <td rowspan="2">
            <?php
            $matchUrl = \App\Http\Controllers\PC\CommonTool::matchPathWithId($mid,2);
            ?>
            <a target="_blank" href="{{$matchUrl}}">析</a>&nbsp;&nbsp;<a target="_blank" href="{{$matchUrl}}#odd">指数</a>
        </td>
    </tr>
    <tr>
        <td class="team"><img src="{{\App\Http\Controllers\PC\CommonTool::getIconBK($match['aicon'])}}"><p>{{$match['aname']}}</p></td>
        <td class="score" id="a_score1_{{$mid}}">{{\App\Http\Controllers\PC\CommonTool::getBasketScore($match['ascore_1st'])}}</td>
        @if(!$isHalfFormat)
        <td class="score" id="a_score2_{{$mid}}">{{\App\Http\Controllers\PC\CommonTool::getBasketScore($match['ascore_2nd'])}}</td>
        @endif
        <td class="score" id="a_score3_{{$mid}}">{{\App\Http\Controllers\PC\CommonTool::getBasketScore($match['ascore_3rd'])}}</td>
        @if(!$isHalfFormat)
        <td class="score" id="a_score4_{{$mid}}">{{\App\Http\Controllers\PC\CommonTool::getBasketScore($match['ascore_4th'])}}</td>
        @endif
        @for($i = 0; $i < 2; $i++)
            @if($otCount > $i)
                <td class="score" id="a_ot{{1+$i}}_{{$mid}}"><p>{{$a_ots[$i]}}</p></td>
            @else
                <td class="hide" id="a_ot{{1+$i}}_{{$mid}}"><p></p></td>
            @endif
        @endfor
        <td class="score" id="a_score_half_{{$mid}}">{{$a_half}}</td>
        <td class="score fullScore" id="a_score_full_{{$mid}}">{{\App\Http\Controllers\PC\CommonTool::getBasketScore($match['ascore'])}}</td>
        <td id="score_whole_diff_{{$mid}}">{{$whole_diff}}</td>
        <td id="score_whole_total_{{$mid}}">{{$whole_total}}</td>
        <td>{{$asiaUp}}</td>
        <td class="asia">
            <p class="">{{$asiaDown}}</p>
        </td>
        <td class="goal">
            <span>{{$ouMiddle>0?'小 '.$ouMiddle:''}}</span>
            <p class="">{{$ouDown}}</p>
        </td>
    </tr>
    </tbody>
</table>