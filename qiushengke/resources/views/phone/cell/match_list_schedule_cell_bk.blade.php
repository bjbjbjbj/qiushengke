<?php
$status = $match['status'];
$mid = $match['mid'];
$lid = $match['lid'];
//赛制
//半全场赛制
$isHalfFormat = $match['system'] == 1;
$matchTime = \App\Http\Controllers\PC\CommonTool::getBasketCurrentTime($status, $match['live_time_str'], $isHalfFormat);
$matchTime = explode(' ',$matchTime)[0];
$matchUrl = \App\Http\Controllers\PC\CommonTool::matchWapPathWithId($mid,2);

//亚赔
$asiaUp = "-";
$asiaMiddle = "-";
$asiaDown = "-";
//    $oddAsia = $match->oddAsian();
if (isset($match['asiamiddle2'])) {
    $asiaUp = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['asiaup2']);
    $asiaDown = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['asiadown2']);
    $asiaMiddle = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['asiamiddle2'], true);
}

//大小分
$ouUp = "-";
$ouMiddle = "-";
$ouDown = "-";
//    $oddOu = $match->oddOU();
if (isset($match['goalmiddle2'])) {
    $ouUp = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['goalup2']);
    $ouDown = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['goaldown2']);
    $ouMiddle = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['goalmiddle2'], true);
}
//欧赔
$europeUp = "-";
$europeDown = "-";
//    $oddOu = $match->oddOU();
if (isset($match['oumiddle2'])) {
    $europeUp = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['ouup2']);
    $europeDown = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['oudown2']);
}

//赛事背景色
$bgRgb = \App\Http\Controllers\PC\CommonTool::getLeagueBgRgb($lid);
$r = $bgRgb['r'];
$g = $bgRgb['g'];
$b = $bgRgb['b'];

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

$liveUrl = \App\Http\Controllers\PC\CommonTool::matchWapLivePathWithId($match['mid'],2);

if (isset($match['asiamiddle2'])){
    $ra = \App\Http\Controllers\PC\OddCalculateTool::getMatchAsiaOddResult($match['hscore'],$match['ascore'],$match['asiamiddle2'],true);
    $ra = $ra == 3 ? '赢' :($ra == 1 ? '走' :'输');
}
else
    $ra = '-';

$ro = \App\Http\Controllers\PC\OddCalculateTool::getMatchResult($match['hscore'],$match['ascore']);
$ro = $ro == 3 ? '胜' :($ro == 1 ? '平' :'负');

if (isset($match['goalmiddle2'])){
    $rg = \App\Http\Controllers\PC\OddCalculateTool::getMatchSizeOddResult($match['hscore'],$match['ascore'],$match['goalmiddle2']);
    $rg = $rg == 3 ? '大' :($rg == 1 ? '走' :'小');
}
else
    $rg = '-';
?>

<a href="{{$matchUrl}}" isMatch="1" class="default {{$show?'show':'hide'}}" id="m_table_{{$mid}}" match="{{$mid}}" league="{{$lid}}" nba="{{$isNBA?"nba":""}}" lottery="{{$isLottery?"lottery":""}}" live="{{$hasLive?"live":""}}">
    <div class="match">
        <div class="time">
            <p class="league">{{$league_name}}</p>
            <p>{{date('H:i', $match['time'])}}</p>
        </div>
        <div class="team">
            <p><img src="{{\App\Http\Controllers\PC\CommonTool::getIconBK($match['hicon'])}}" onerror="this.src='{{$cdn}}/phone/img/icon_teamDefault.png'">{{$match['hname']}}</p>
            <p><img src="{{\App\Http\Controllers\PC\CommonTool::getIconBK($match['aicon'])}}" onerror="this.src='{{$cdn}}/phone/img/icon_teamDefault.png'">{{$match['aname']}}</p>
        </div>
        @if($status == 0)
            @if($hasLive)
                <div class="liveFuture"><img src="{{$cdn}}/phone/img/soccer_icon_living_n.png"></div>
            @else
                <div class="liveFuture">暂无<br/>直播</div>
            @endif
        @endif
        <div class="allOdd">
            <p>欧：{{$europeUp}} {{$europeDown}}<br/>亚：{{$asiaUp}} {{$asiaMiddle}} {{$asiaDown}}<br/>大：{{$ouUp}} {{$ouMiddle}} {{$ouDown}}</p>
        </div>
    </div>
</a>
