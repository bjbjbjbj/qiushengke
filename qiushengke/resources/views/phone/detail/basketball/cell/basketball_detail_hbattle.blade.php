<?php
$count = 0;
$ouWin = 0;//主胜
$ouDraw = 0;//平局
$ouLose = 0;//主负
$asia_win_count = 0;//亚盘赢盘常数
        $hscore = 0;
$ascore = 0;
foreach($data as $tmpMatch){
    $count++;
    if($tmpMatch['hscore'] > $tmpMatch['ascore'])
        if($tmpMatch['hid'] == $hid)
            $ouWin++;
        else
            $ouLose++;
    elseif($tmpMatch['hscore'] < $tmpMatch['ascore'])
        if($tmpMatch['hid'] == $hid)
            $ouLose++;
        else
            $ouWin++;
    else
        $ouDraw++;
    if (isset($tmpMatch['asiamiddle1'])) {
        $asia_host_score = $tmpMatch['hscore'] - $tmpMatch['asiamiddle1'];
        if ($asia_host_score > $tmpMatch['ascore']) {
            $asia_win_count++;
        }
    }
    if ($tmpMatch['hid'] == $hid){
        $hscore += $tmpMatch['hscore'];
        $ascore += $tmpMatch['ascore'];
    }
    else{
        $hscore += $tmpMatch['ascore'];
        $ascore += $tmpMatch['hscore'];
    }
}
?>
<div class="proportionBox" ha="{{$ha}}" le="{{$league}}">
    <div class="proportion">
        <p class="host" style="width: {{$count == 0 ? 0 : round($ouWin/$count, 1)*100}}%;"></p>
        <p class="away" style="width: {{$count == 0 ? 0 : round($ouLose/$count, 1)*100}}%;"></p>
        <div class="host">
            <img src="{{$base['hicon']}}" onerror="this.src='{{$cdn}}/pc/img/icon_teamDefault.png'">
            <p class="match"><b>{{$ouWin}}</b>胜</p>
            <p class="score">（场均<span>{{$count > 0 ? round($hscore/$count,1) : '-'}}</span>分）</p>
        </div>
        <div class="away">
            <img src="{{$base['aicon']}}" onerror="this.src='{{$cdn}}/pc/img/icon_teamDefault.png'">
            <p class="match"><b>{{$ouLose}}</b>胜</p>
            <p class="score">（场均<span>{{$count > 0 ? round($ascore/$count,1) : '-'}}</span>分）</p>
        </div>
    </div>
    <p class="summary">共{{$count}}场，胜率：<b>{{$count == 0 ? 0 : round($ouWin/$count, 3) * 100}}%</b></p>
</div>
<table ha="{{$ha}}" le="{{$league}}">
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
            if($match['hid'] == $hid)
                $asia_result = '<p class="win">赢</p>';
            else
                $asia_result = '<p class="lose">输</p>';
        } else if($asia_host_score == $match['ascore']) {
            $asia_result = '<p class="draw">走</p>';
        } else {
            if($match['hid'] == $hid)
                $asia_result = '<p class="lose">输</p>';
            else
                $asia_result = '<p class="win">赢</p>';
        }
        if ($match['asiamiddle1'] == null){
            $asia_result = '<p class="">-</p>';
        }
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