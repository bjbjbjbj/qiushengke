<?php
$week = array('周日','周一','周二','周三','周四','周五','周六');
$hicon = strlen($match['hicon']) > 0 ? $match['hicon'] : '/pc/img/icon_teamDefault.png';
$aicon = strlen($match['aicon']) > 0 ? $match['aicon'] : '/pc/img/icon_teamDefault.png';

function getOdd($odd) {
    if (isset($odd) && strlen($odd) > 0) {
        return $odd;
    }
    return "-";
}
$asiaup2 = getOdd($match['asiaup2']);
$asiamiddle2 = getOdd($match['asiamiddle2']);
$asiadown2 = getOdd($match['asiadown2']);
$goalup2 = getOdd($match['goalup2']);
$goalmiddle2 = getOdd($match['goalmiddle2']);
$goaldown2 = getOdd($match['goaldown2']);
$ouup2 = getOdd($match['ouup2']);
$oudown2 = getOdd($match['oudown2']);
?>
<div id="Info" class="basketball">
    <p class="info">{{$match['league']}}<br/>比赛时间：{{date('Y-m-d', $match['time'])}}  {{date('H:i', $match['time'])}}  {{$week[date('w',$match['time'])]}}</p>
    <p class="team host"><span class="img"><img src="{{$hicon}}"></span>{{$match['hname']}}</p>
    <p class="team away"><span class="img"><img src="{{$aicon}}"></span>{{$match['aname']}}</p>
    @if($match['status'] > 0 || $match['status'] == -1)
        <p class="score">{{$match['hscore']}} - {{$match['ascore']}}</p>
    @elseif($match['status'] == 0)
        <p class="score">VS</p>
    @else
        <p class="score">{{$match['current_time']}}</p>
    @endif
    <div class="odd">
        <p>亚：{{$asiaup2}}&nbsp;&nbsp;{{$asiamiddle2}}&nbsp;&nbsp;{{$asiadown2}}</p>
        <p>欧：{{$ouup2}}&nbsp;&nbsp;&nbsp;&nbsp;{{$oudown2}}</p>
        <p>大：{{$goalup2}}&nbsp;&nbsp;{{$goalmiddle2}}&nbsp;&nbsp;{{$goaldown2}}</p>
    </div>
</div>