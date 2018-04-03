<?php
$week = array('周日','周一','周二','周三','周四','周五','周六');
$hicon = strlen($match['hicon']) > 0 ? $match['hicon'] : '/pc/img/icon_teamDefault.png';
$aicon = strlen($match['aicon']) > 0 ? $match['aicon'] : '/pc/img/icon_teamDefault.png';

$statusCn = \App\Models\QSK\Match\BasketMatch::getStatusTextCn($match['status'], $match['system']);
?>
<div id="Info">
    <div class="mbox">
        <dl>
            <dd class="host">
                <img src="{{$hicon}}">
                <p>{{$match['hname']}}</p>
            </dd>
            <dt>
                @if($match['status'] > 0)
                    <p class="time">{{$statusCn}}&nbsp;&nbsp;{{$match['live_time_str']}}</p>
                    <p class="score"><span class="host">{{$match['hscore']}}</span><span class="away">{{$match['ascore']}}</span></p>
                @elseif($match['status'] == 0)
                    <p class="time">{{date('Y-m-d', $match['time'])}}  {{date('H:i', $match['time'])}}  {{$week[date('w',$match['time'])]}}</p>
                    <p class="score"><span class="host">0</span><span class="away">0</span></p>
                @else
                    <p class="time">{{$statusCn}}</p>
                    <p class="score"><span class="host">0</span><span class="away">0</span></p>
                @endif
            </dt>
            <dd class="away">
                <img src="{{$aicon}}">
                <p>{{$match['aname']}}</p>
            </dd>
        </dl>
        <div class="adorn away"></div>
        <div class="adorn host"></div>
    </div>
</div>