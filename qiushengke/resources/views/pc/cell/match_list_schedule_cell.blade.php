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
$asiaUp1 = "-";
$asiaMiddle1 = "-";
$asiaDown1 = "-";
//    $oddAsia = $match->oddAsian();
if (isset($match['asiamiddle2'])) {
    $asiaUp = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['asiaup2']);
    $asiaDown = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['asiadown2']);
    $asiaMiddle = \App\Http\Controllers\PC\CommonTool::getHandicapCn($match['asiamiddle2'], "-", \App\Http\Controllers\PC\CommonTool::k_odd_type_asian);
}
if (isset($match['asiamiddle1'])) {
    $asiaUp1 = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['asiaup1']);
    $asiaDown1 = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['asiadown1']);
    $asiaMiddle1 = \App\Http\Controllers\PC\CommonTool::getHandicapCn($match['asiamiddle1'], "-", \App\Http\Controllers\PC\CommonTool::k_odd_type_asian);
}

//大小球
$goalUp = "-";
$goalMiddle = "-";
$goalDown = "-";
$goalUp1 = "-";
$goalMiddle1 = "-";
$goalDown1 = "-";
//    $oddOu = $match->oddOU();
if (isset($match['goalmiddle2'])) {
    $goalUp = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['goalup2']);
    $goalDown = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['goaldown2']);
    $goalMiddle = \App\Http\Controllers\PC\CommonTool::getHandicapCn($match['goalmiddle2'], "-", \App\Http\Controllers\PC\CommonTool::k_odd_type_ou);
}
if (isset($match['goalmiddle1'])) {
    $goalUp1 = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['goalup1']);
    $goalDown1 = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['goaldown1']);
    $goalMiddle1 = \App\Http\Controllers\PC\CommonTool::getHandicapCn($match['goalmiddle1'], "-", \App\Http\Controllers\PC\CommonTool::k_odd_type_ou);
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
$show = false;
?>
<tr isMatch="1" class="{{$show?'show':'hide'}}" id="m_tr_{{$mid}}" match="{{$mid}}" league="{{$lid}}" asiaOdd="{{$asiaOdd}}" ouOdd="{{$ouOdd}}" first="{{$isFirst?"first":""}}" lottery="{{$isLottery?"lottery":""}}" live="{{$hasLive?"live":""}}">
    <td><p class="leagueLine" style="background: rgb({{$r}}, {{$g}}, {{$b}});"></p>{{$match['league']}}</td>
    <td>{{date('H:i', $match['time'])}}</td>
    <td class="host" colspan="2">
        <a class="team" href="{{$matchUrl}}" target="_blank">
            <p class="name">{{$match['hname']}}</p>
            <p class="fullname"><span>{{$match['hname']}}</span></p>
        </a>
    </td>
    <td><a href="{{$matchUrl}}" target="_blank"><p class="fullScore">VS</p></a></td>
    <td class="away" colspan="2">
        <a class="team" href="{{$matchUrl}}" target="_blank">
            <p class="name">{{$match['aname']}}</p>
            <p class="fullname"><span>{{$match['aname']}}</span></p>
        </a>
    </td>
    <td>
        @if($hasLive)
            <a class="live" href="{{$matchUrl}}" target="_blank"><img id="live_{{$match['mid']}}" src="{{env('CDN_URL')}}/pc/img/icon_living.png"></a>
        @else
            <a class="live" href="{{$matchUrl}}" target="_blank"><img id="live_{{$match['mid']}}" src="{{env('CDN_URL')}}/pc/img/icon_action_light.png"></a>
        @endif
    </td>
    <td>
        <p class="start">
            <span class="" value="{{$asiaUp1}}">{{$asiaUp1}}</span><span class="odd" value="{{$match['asiamiddle1']}}">{{$asiaMiddle1}}</span><span class="" value="{{$asiaDown1}}">{{$asiaDown1}}</span>
        </p>
        <p class="now">
            <span class="" value="{{$asiaUp}}">{{$asiaUp}}</span><span class="odd" value="{{$match['asiamiddle2']}}">{{$asiaMiddle}}</span><span class="" value="{{$asiaDown}}">{{$asiaDown}}</span>
        </p>
    </td>
    <td>
        <p class="start">
            <span value="{{$goalUp1}}">{{$goalUp1}}</span><span class="goal" value="{{$match['goalmiddle1']}}">{{$goalMiddle1}}</span><span value="{{$goalDown1}}">{{$goalDown1}}</span>
        </p>
        <p class="now">
            <span value="{{$goalUp}}">{{$goalUp}}</span><span class="goal" value="{{$match['goalmiddle2']}}">{{$goalMiddle}}</span><span value="{{$goalDown}}">{{$goalDown}}</span>
        </p>
    </td>
    <td>
        <a target="_blank" href="{{$matchUrl}}">析</a>
        <a target="_blank" href="/match/foot/odd.html?mid={{$mid}}&type=1">指数</a>
    </td>
</tr>