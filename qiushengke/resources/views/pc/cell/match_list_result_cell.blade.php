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
$ouOddArray = \App\Http\Controllers\PC\CommonTool::getMatchOdds($match['oumiddle2'], \App\Http\Controllers\PC\CommonTool::k_odd_type_ou);
$ouOdd = "ou_" . $ouOddArray['sort'];

//是否是一级赛事
$isFirst = ($match['genre'] >> 1 & 1) == 1;
//是否是竞彩
$isLottery = isset($match['betting_num']);

//比赛事件

//默认是否显示
$show = (array_key_exists('hide',$match) && $match['hide']) ? false : true;

$hasLive = $match['pc_live'];

$show = false;
?>
<tr isMatch="1" class="{{$show?'show':'hide'}}" id="m_tr_{{$mid}}" match="{{$mid}}" league="{{$lid}}" asiaOdd="{{$asiaOdd}}" ouOdd="{{$ouOdd}}" first="{{$isFirst?"first":""}}" lottery="{{$isLottery?"lottery":""}}" live="{{$hasLive?"live":""}}">
    <td><p class="leagueLine" style="background: rgb({{$r}}, {{$g}}, {{$b}});"></p>{{$match['league']}}</td>
    <td>{{date('H:i', $match['time'])}}</td>
    <td class="host" colspan="2">
        <a class="team" href="{{$matchUrl}}" target="_blank">
            @if($match['h_red'] > 0)
                <span class="redCard">{{$match['h_red']}}</span>
            @endif
            @if($match['h_yellow'] > 0)
                <span class="yellowCard">{{$match['h_yellow']}}</span>
            @endif
            <span class="name">{{$match['hname']}}</span>
        </a>
    </td>
    <td>
        <a href="{{$matchUrl}}" target="_blank"
           @if($match['status'] == -1 || $match['status'] > 0)
           onmouseover="getMousePos(this); ct=window.setInterval('refreshMatchTech(\'{{$mid}}\')',200)" onmouseout="window.clearInterval(ct)"
                @endif
        >
            <p class="fullScore">{{$match['hscore'] .' - '. $match['ascore']}}</p>
            <p class="halfScore">半 {{$match['hscorehalf'] .' - '. $match['ascorehalf']}}</p>
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
    <td class="away" colspan="2">
        <a class="team" href="{{$matchUrl}}" target="_blank">
            @if($match['a_red'] > 0)
                <span class="redCard">{{$match['a_red']}}</span>
            @endif
            @if($match['a_yellow'] > 0)
                <span class="yellowCard">{{$match['a_yellow']}}</span>
            @endif
            <span class="name">{{$match['aname']}}</span>
        </a>
    </td>
    @if(array_key_exists('h_corner',$match))
        <td>{{$match['h_corner'] . "-" . $match['a_corner']}}</td>
    @else
        <td>-</td>
    @endif
    <td>
        @if('-' == $asiaMiddle)
            -
        @else
            <?php
            $asiaResult = \App\Http\Controllers\PC\OddCalculateTool::getMatchAsiaOddResult($match['hscore'],$match['ascore'],$match['asiamiddle2'],true);
            ?>
            @if(3 == $asiaResult)
                <p class="odd"><span class="win">赢</span>{{$asiaMiddle}}</p>
            @elseif(1 == $asiaResult)
                <p class="odd"><span class="draw">走</span>{{$asiaMiddle}}</p>
            @else
                <p class="odd"><span class="lose">输</span>{{$asiaMiddle}}</p>
            @endif
        @endif
    </td>
    <td>
        <p class="odd">
            @if($match['hscore'] > $match['ascore'])
                <span class="win">胜</span>
            @elseif($match['hscore'] == $match['ascore'])
                <span class="draw">平</span>
            @else
                <span class="lose">负</span>
            @endif
        </p>
    </td>
    <td>
        @if('-' == $goalMiddle)
            -
        @else
            @if($match['hscore'] + $match['ascore'] > $match['goalmiddle2'])
                <p class="odd"><span class="big">大</span>({{$goalMiddle}})</p>
            @elseif($match['hscore'] + $match['ascore'] == $match['goalmiddle2'])
                <p class="odd"><span class="goaldraw">走</span>({{$goalMiddle}})</p>
            @else
                <p class="odd"><span class="small">小</span>({{$goalMiddle}})</p>
            @endif
        @endif
    </td>
    <td>
        <a target="_blank" href="{{$matchUrl}}">析</a>
        <a target="_blank" href="/match/foot/odd.html?mid={{$mid}}&type=1">指数</a>
    </td>
</tr>