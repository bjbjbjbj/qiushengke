<?php
$status = $match['status'];
$mid = $match['mid'];
$lid = $match['lid'];

//$matchTime = \App\Http\Controllers\PC\CommonTool::getMatchCurrentTime($match['time'],$match['timehalf'],$match['status']);;
$matchTime = date('Y-m-d H:i',$match['time']);;
$sport = 2;
$matchUrl = \App\Http\Controllers\PC\CommonTool::matchPathWithId($mid,$sport);

//亚赔
$asiaUp = "-";
$asiaMiddle = "-";
$asiaDown = "-";
//    $oddAsia = $match->oddAsian();
if (isset($match['asiamiddle1'])) {
    $asiaUp = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['asiaup1']);
    $asiaDown = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['asiadown1']);
    $asiaMiddle = \App\Http\Controllers\PC\CommonTool::getHandicapCn($match['asiamiddle1'], "-", \App\Http\Controllers\PC\CommonTool::k_odd_type_asian,\App\Http\Controllers\PC\CommonTool::kSportBasketball);
    $asiaMiddle = str_replace('受','-',$asiaMiddle);
    $asiaMiddle = str_replace('让','',$asiaMiddle);
    $asiaMiddle = str_replace('分','',$asiaMiddle);
}

//大小球
$goalUp = "-";
$goalMiddle = "-";
$goalDown = "-";
//    $oddOu = $match->oddOU();
if (isset($match['goalmiddle2'])) {
    $goalUp = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['goalup2']);
    $goalDown = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['goaldown2']);
    $goalMiddle = \App\Http\Controllers\PC\CommonTool::getHandicapCn($match['goalmiddle2'], "-", \App\Http\Controllers\PC\CommonTool::k_odd_type_ou,\App\Http\Controllers\PC\CommonTool::kSportBasketball);
}

$asiaOddArray = \App\Http\Controllers\PC\CommonTool::getMatchOdds($match['asiamiddle2'], \App\Http\Controllers\PC\CommonTool::k_odd_type_asian,\App\Http\Controllers\PC\CommonTool::kSportBasketball);
$typeCn = $asiaOddArray['typeCn'];
if (str_contains($typeCn, "受")) {
    $asiaOdd = "asiaDown_" . $asiaOddArray['sort'];
} elseif($typeCn == '未开盘' || $typeCn == '平手') {
    $asiaOdd = "asiaMiddle_" . $asiaOddArray['sort'];
} else {
    $asiaOdd = "asiaUp_" . $asiaOddArray['sort'];
}
$ouOddArray = \App\Http\Controllers\PC\CommonTool::getMatchOdds($match['goalmiddle2'], \App\Http\Controllers\PC\CommonTool::k_odd_type_ou,\App\Http\Controllers\PC\CommonTool::kSportBasketball);
$ouOdd = "ou_" . $ouOddArray['sort'];

$hicon = strlen($match['hicon'])>0?$match['hicon'] : (env('CDN_URL') . '/pc/img/icon_teamDefault.png');
$aicon = strlen($match['aicon'])>0?$match['aicon'] : (env('CDN_URL') . '/pc/img/icon_teamDefault.png');
?>
<tr>
    <td>{{date('m.d', $match['time'])}}<br/>{{date('H:i', $match['time'])}}</td>
    <td><a href="" class="team">{{$match['hname']}}</a></td>
    <td><a href=""><img src="{{$hicon}}"></a></td>
    <td><a href=""><p class="fullScore">
                @if($status == 0)
                    VS
                @elseif($status == -1 || $status > 0)
                    {{$match['hscore'] .' - '. $match['ascore']}}
                @endif
            </p></a></td>
    <td><a href=""><img src="{{$aicon}}"></a></td>
    <td><a href="" class="team">{{$match['aname']}}</a></td>
    <td>{{$asiaMiddle}}</td>
    <td>{{$goalMiddle}}</td>
    <td>
        <a target="_blank" href="{{$matchUrl}}">析</a>  <a target="_blank" href="{{$matchUrl}}#odd">指数</a>
    </td>
</tr>