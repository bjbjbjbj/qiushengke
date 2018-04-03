<?php
$week = array('周日','周一','周二','周三','周四','周五','周六');
$hicon = strlen($match['hicon']) > 0 ? $match['hicon'] : '/pc/img/icon_teamDefault.png';
$aicon = strlen($match['aicon']) > 0 ? $match['aicon'] : '/pc/img/icon_teamDefault.png';
?>

<div id="Info">
    <div class="mbox">
        <dl>
            <dd class="host">
                <img src="{{$hicon}}">
                <p>{{$match['hname']}}</p>
            </dd>
            <dt>
            @if($match['status'] == 0)
                <p class="time">{{date('Y-m-d  H:i',$match['time'])}}  {{$week[date('w',$match['time'])]}}</p>
            @else
                <p class="time">{{date('Y-m-d  H:i',$match['time'])}}  {{$week[date('w',$match['time'])]}}</p>
            @endif
            <p class="score"><span class="host">{{$match['hscore']}}</span><span class="away">{{$match['ascore']}}</span></p><!--隐藏时增加hid-->
        <!-- <button>(隐藏比分)</button> -->
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