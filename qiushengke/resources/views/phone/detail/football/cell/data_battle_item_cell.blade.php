<?php $total = $result['win'] + $result['draw'] + $result['lose']; ?>
<div class="canvasBox" ha="{{$ha}}" le="{{$le}}">
    <div class="canvasArea">
        <div class="circle"><canvas width="140px" height="140px" value="0.4" color="#34b45d"></canvas></div>
        <p>主胜<b class="red">{{$result['win']}}</b></p>
    </div>
    <div class="canvasArea">
        <div class="circle"><canvas width="140px" height="140px" value="0.2" color="#9e9e9e"></canvas></div>
        <p>平局<b class="green">{{$result['draw']}}</b></p>
    </div>
    <div class="canvasArea">
        <div class="circle"><canvas width="140px" height="140px" value="0.4" color="#1974bd"></canvas></div>
        <p>主负<b class="gray">{{$result['lose']}}</b></p>
    </div>
    <p class="summary">共{{$total}}场，胜率：<b>{{$result['winPercent']}}%</b>，赢盘率：<b>{{$result['oddPercent']}}%</b></p>
</div>
<table ha="{{$ha}}" le="{{$le}}">
    <thead>
    <tr>
        <th>日期</th>
        <th>赛事</th>
        <th colspan="3">比分</th>
        <th>主让</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $match)
        <?php
        $goal_total = $match['hscore'] + $match['ascore'];
        $goal_result = $goal_total > $match['goalmiddle1'] ? '大' : ($goal_total == $match['goalmiddle1'] ? '走' : '小');
        $asia_host_score = $match['hscore'] - $match['asiamiddle1'];
        if ($asia_host_score > $match['ascore']) {
            if ($match['hid'] == $hid)
                $asia_result = '<p class="win">赢</p>';
            else
                $asia_result = '<p class="lose">输</p>';
        } else if($asia_host_score == $match['ascore']) {
            $asia_result = '<p class="draw">走</p>';
        } else {
            if ($match['hid'] == $hid)
                $asia_result = '<p class="lose">输</p>';
            else
                $asia_result = '<p class="win">赢</p>';
        }
        if ($match['asiamiddle1'] == null)
            $asia_result = '<p class="">-</p>';
        ?>
        <tr>
            <td>{{substr($match['time'],0, 10)}}</td>
            <td>{{$match['league']}}</td>
            <td @if($match['hid'] == $hid) class="host red" @endif>{{$match['hname']}}</td>
            <td>{{$match['hscore']}} - {{$match['ascore']}}<p class="goal">{{$goal_result}}{{\App\Http\Controllers\PC\CommonTool::getOddMiddleString($match['goalmiddle1'])}}</p></td>
            <td @if($match['aid'] == $hid) class="host red" @endif>{{$match['aname']}}</td>
            @if($match['asiamiddle1'] == null)
                <td>{{''}}{!! $asia_result !!}</td>
            @else
                <td>{{\App\Http\Controllers\PC\CommonTool::getOddMiddleString($match['asiamiddle1'])}}{!! $asia_result !!}</td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>