<?php
$total = $result['win'] + $result['draw'] + $result['lose'];
$win = 0;
$draw = 0;
$lose = 0;
if ($total > 0){
    $win = round($result['win'] / $total, 1);
    $draw = round($result['draw'] / $total, 1);
    $lose = 1 - $win - $draw;
}
?>
<div class="canvasBox" ha="{{$ha}}" le="{{$le}}">
    <div class="canvasArea">
        <div class="circle"><canvas width="140px" height="140px" value="{{$win}}" color="#34b45d"></canvas></div>
        <p>主胜<b class="red">{{$result['win']}}</b></p>
    </div>
    <div class="canvasArea">
        <div class="circle"><canvas width="140px" height="140px" value="{{$draw}}" color="#9e9e9e"></canvas></div>
        <p>平局<b class="green">{{$result['draw']}}</b></p>
    </div>
    <div class="canvasArea">
        <div class="circle"><canvas width="140px" height="140px" value="{{$lose}}" color="#1974bd"></canvas></div>
        <p>主负<b class="gray">{{$result['lose']}}</b></p>
    </div>
    <p class="summary">共{{$total}}场，大球率：<b>{{$result['winPercent']}}%</b></p>
</div>
<table ha="{{$ha}}" le="{{$le}}">
    <thead>
    <tr>
        <th>日期</th>
        <th>赛事</th>
        <th colspan="3">角球比分</th>
        <th>盘口</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $match)
        <tr>
            <td>{{substr($match['time'], 0, 10)}}</td>
            <td>{{$match['league']}}</td>
            <td @if($tid == $match['hid']) class="host" @endif >{{$match['hname']}}</td>
            <td>{{$match['h_corner']}}-{{$match['a_corner']}}<p class="goal">{{$match['h_half_corner']}}-{{$match['a_half_corner']}}</p></td>
            <td @if($tid == $match['aid']) class="host" @endif >{{$match['aname']}}</td>
            <td>{{$match['middle2']}}
                @if(isset($match['middle2']))
                    @if($match['h_corner'] + $match['a_corner'] > $match['middle2'])
                        <p class="big">大</p>
                    @elseif($match['h_corner'] + $match['a_corner'] < $match['middle2'])
                        <p class="small">小</p>
                    @else
                        <p class="draw">走</p>
                    @endif
                @else
                    <p>-</p>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>