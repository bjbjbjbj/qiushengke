<?php
$status = $match['status'];
$mid = $match['mid'];
$lid = $match['lid'];

$matchTime = \App\Http\Controllers\PC\CommonTool::getMatchCurrentTime($match['time'],$match['timehalf'],$match['status']);;
$sport = 1;
$matchUrl = \App\Http\Controllers\PC\CommonTool::matchPathWithId($mid,$sport);
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
if (isset($match['asiamiddle1'])) {
    $asiaUp = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['asiaup1']);
    $asiaDown = \App\Http\Controllers\PC\CommonTool::float2Decimal($match['asiadown1']);
    $asiaMiddle = \App\Http\Controllers\PC\CommonTool::getHandicapCn($match['asiamiddle1'], "-", \App\Http\Controllers\PC\CommonTool::k_odd_type_asian);
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
?>
<tr>
    <td>{{date('m.d', $match['time'])}}<br/>{{date('H:i', $match['time'])}}</td>
    <td><a target="_blank" href="{{$matchUrl}}" class="team">{{$match['hname']}}</a></td>
    <td><a target="_blank" href="{{$matchUrl}}"><img src="{{strlen($match['hicon'])>0?$match['hicon'] : (env('CDN_URL') . '/pc/img/icon_teamDefault.png')}}"></a></td>
    <td>
        <a target="_blank" href="{{$matchUrl}}">
            <p class="fullScore">
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
    </td>
    <td><a target="_blank" href="{{$matchUrl}}"><img src="{{strlen($match['aicon'])>0?$match['aicon'] : (env('CDN_URL') . '/pc/img/icon_teamDefault.png')}}"></a></td>
    <td><a target="_blank" href="{{$matchUrl}}" class="team">{{$match['aname']}}</a></td>
    <td>
        <p class="asia">
            <span>{{$asiaUp}}</span><span class="odd">{{$asiaMiddle}}</span><span>{{$asiaDown}}</span>
        </p>
        <p class="goal">
            <span>{{$goalUp}}</span><span class="odd">{{$goalMiddle}}</span><span>{{$goalDown}}</span>
        </p>
    </td>
    <td>
        <a target="_blank" href="{{$matchUrl}}">析</a>  <a target="_blank" href="{{$matchUrl}}#odd">指数</a>
    </td>
</tr>