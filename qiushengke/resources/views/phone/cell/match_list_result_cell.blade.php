<?php
$status = $match['status'];
$mid = $match['mid'];
$lid = $match['lid'];

$matchTime = \App\Http\Controllers\PC\CommonTool::getMatchWapCurrentTime($match['time'],$match['timehalf'],$match['status']);;

$matchUrl = \App\Http\Controllers\PC\CommonTool::matchWapPathWithId($mid,$sport);
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
    $asiaMiddle = \App\Http\Controllers\PC\CommonTool::panKouTextWap($match['asiamiddle2']);
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

$liveUrl = \App\Http\Controllers\PC\CommonTool::matchWapLivePathWithId($match['mid']);

$hicon = isset($match['hicon'])?$match['hicon']:'/phone/img/icon_teamDefault.png';
$aicon = isset($match['aicon'])?$match['aicon']:'/phone/img/icon_teamDefault.png';

if (isset($match['asiamiddle2'])){
    $ra = \App\Http\Controllers\PC\OddCalculateTool::getMatchAsiaOddResult($match['hscore'],$match['ascore'],$match['asiamiddle2'],true);
    $ra = $ra == 3 ? '赢' :($ra == 1 ? '走' :'输');
}
else
    $ra = '-';
if (isset($match['oumiddle2'])){
    $ro = \App\Http\Controllers\PC\OddCalculateTool::getMatchResult($match['hscore'],$match['ascore']);
    $ro = $ro == 3 ? '胜' :($ro == 1 ? '平' :'负');
}
else
    $ro = '-';
if (isset($match['goalmiddle2'])){
    $rg = \App\Http\Controllers\PC\OddCalculateTool::getMatchSizeOddResult($match['hscore'],$match['ascore'],$match['goalmiddle2']);
    $rg = $rg == 3 ? '大' :($rg == 1 ? '走' :'小');
}
else
    $rg = '-';
?>
<a href="{{$matchUrl}}" isMatch="1" class="default {{$show?'show':'hide'}}" id="m_tr_{{$mid}}" match="{{$mid}}" league="{{$lid}}" asiaOdd="{{$asiaOdd}}" ouOdd="{{$ouOdd}}" first="{{$isFirst?"first":""}}" lottery="{{$isLottery?"lottery":""}}" live="{{$hasLive?"live":""}}">
    <div class="match">
        <div class="time">
            <p class="result">{{$ra}}&nbsp;{{$ro}}&nbsp;{{$rg}}</p>
            <p class="league">{{$match['league']}}</p>
        </div>
        <div class="team">
            <p><img src="{{$hicon}}">{{$match['hname']}}</p>
            <p><img src="{{$aicon}}">{{$match['aname']}}</p>
        </div>
        <div class="fullScore"><p>{{$match['hscore']}}</p><p>{{$match['ascore']}}</p></div>
        <div class="halfScore"><p>{{$match['hscorehalf']}}</p><p>{{$match['ascorehalf']}}</p></div>
        <div class="card">
            <p>
                <span
                        @if($match['h_red'] > 0)
                        class="red"
                        @else
                        class="hide"
                        @endif
                        id="{{$mid}}_h_red">{{$match['h_red']}}</span>
                <span
                        @if($match['h_yellow'] > 0)
                        class="yellow"
                        @else
                        class="hide"
                        @endif
                        id="{{$mid}}_h_yellow">{{$match['h_yellow']}}</span>
            </p>
            <p>
                <span
                        @if($match['a_red'] > 0)
                        class="red"
                        @else
                        class="hide"
                        @endif
                        id="{{$mid}}_a_red">{{$match['a_red']}}</span>
                <span
                        @if($match['a_yellow'] > 0)
                        class="yellow"
                        @else
                        class="hide"
                        @endif
                        id="{{$mid}}_a_yellow">{{$match['a_yellow']}}</span>
            </p>
        </div>
    </div>
</a>