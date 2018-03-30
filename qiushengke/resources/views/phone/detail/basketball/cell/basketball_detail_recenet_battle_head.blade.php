<?php
$count = 0;
$ouWin = 0;//主胜
$ouDraw = 0;//平局
$ouLose = 0;//主负
$asia_win_count = 0;//亚盘赢盘常数
foreach($hmatch as $match){
    $count++;
    if($match['hscore'] > $match['ascore'])
        if($match['hid'] == $hid)
            $ouWin++;
        else
            $ouLose++;
    elseif($match['hscore'] < $match['ascore'])
        if($match['hid'] == $hid)
            $ouLose++;
        else
            $ouWin++;
    else
        $ouDraw++;
    if (isset($match['asiamiddle1'])) {
        $asia_host_score = $match['hscore'] - $match['asiamiddle1'];
        if ($asia_host_score > $match['ascore']) {
            $asia_win_count++;
        }
    }
}

$count2 = 0;
$ouWin2 = 0;//主胜
$ouDraw2 = 0;//平局
$ouLose2 = 0;//主负
$asia_win_count2 = 0;//亚盘赢盘常数
foreach($amatch as $match){
    $count2++;
    if($match['hscore'] > $match['ascore'])
        if($match['hid'] == $aid)
            $ouWin2++;
        else
            $ouLose2++;
    elseif($match['hscore'] < $match['ascore'])
        if($match['hid'] == $aid)
            $ouLose2++;
        else
            $ouWin2++;
    else
        $ouDraw2++;
    if (isset($match['asiamiddle1'])) {
        $asia_host_score2 = $match['hscore'] - $match['asiamiddle1'];
        if ($asia_host_score2 > $match['ascore']) {
            $asia_win_count2++;
        }
    }
}

?>
<div class="proportionBox" ha="{{$ha}}" le="{{$le}}">
    <div class="proportion">
        <p class="host" style="width: {{($ouWin + $ouWin2) > 0 ? round($ouWin/($ouWin + $ouWin2), 3) * 100 : 0}}%;"></p>
        <p class="away" style="width: {{($ouWin + $ouWin2) > 0 ? round($ouWin2/($ouWin + $ouWin2), 3) * 100 : 0}}%;"></p>
        <div class="host">
            <img src="{{$base['hicon']}}" onerror="this.src='{{$cdn}}/pc/img/icon_teamDefault.png'">
            <p class="match"><b>{{$ouWin}}</b>胜<b class="lose">{{$ouLose}}</b>负</p>
            <p class="score">胜<span>{{$count > 0 ? round($ouWin/$count, 4) * 100 : 0}}%</span>赢盘<span>{{$count > 0 ? round($asia_win_count/$count, 4) * 100 : 0}}%</span></p>
        </div>
        <div class="away" onerror="this.src='{{asset('img/customer3/icon_team_default.png')}}'">
            <img src="{{$base['aicon']}}"  onerror="this.src='{{$cdn}}/pc/img/icon_teamDefault.png'">
            <p class="match"><b>{{$ouWin2}}</b>胜<b class="lose">{{$ouLose2}}</b>负</p>
            <p class="score">胜<span>{{$count2 > 0 ? round($ouWin2/$count2, 4) * 100 : 0}}%</span>赢盘<span>{{$count2 > 0 ? round($asia_win_count2/$count2, 4) * 100 : 0}}%</span></p>
        </div>
    </div>
</div>