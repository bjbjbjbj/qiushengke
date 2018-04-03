<?php
//先处理数据
$matches = $data;
$count = count($matches);
$tempMatches = array();
$h10WinCount = 0;
$h10TotalScore = 0;
$h5WinCount = 0;
$h5TotalScore = 0;
$a10WinCount = 0;
$a10TotalScore = 0;
$a5WinCount = 0;
$a5TotalScore = 0;
//逻辑没做优惠,先完成功能
for ($i = 0 ; $i < min(10,count($matches));$i++){
    $match = $matches[$i];
    //胜负
    $hscore = $match['hid'] == $tid ? $match['hscore'] : $match['ascore'];
    $ascore = $match['hid'] == $tid ? $match['ascore'] : $match['hscore'];
    $h10TotalScore += $hscore;
    $a10TotalScore += $ascore;
    if ($i < 5) {
        $h5TotalScore += $hscore;
        $a5TotalScore += $ascore;
    }
    $asiaResult = \App\Http\Controllers\PC\OddCalculateTool::getMatchAsiaOddResult($match['hscore'],$match['ascore'],$match['asiamiddle1'],$match['hid'] == $tid);
    $goalResult = \App\Http\Controllers\PC\OddCalculateTool::getMatchSizeOddResult($match['hscore'],$match['ascore'],$match['goalmiddle1']);
    $ouResult = \App\Http\Controllers\PC\OddCalculateTool::getMatchResult($match['hscore'],$match['ascore'],$match['hid'] == $tid);
    if ($asiaResult == 3) {
        $asiaCss = "red";
        $asiaResultCn = "赢";
    } else if ($asiaResult == 0) {
        $asiaCss = "blue";
        $asiaResultCn = "输";
    } else {
        $asiaCss = "green";
        $asiaResultCn = $asiaResult < 0 ? "-" : "走";
    }
    $match['asiaCss'] = $asiaCss;
    $match['asiaResultCn'] = $asiaResultCn;
    if ($goalResult == 3) {
        $goalCss = "red";
        $goalResultCn = "大";
    } else if ($goalResult == 0) {
        $goalCss = "blue";
        $goalResultCn = "小";
    } else {
        $goalCss = "green";
        $goalResultCn = $asiaResult < 0 ? "-" : "走";
    }
    $match['goalCss'] = $goalCss;
    $match['goalResultCn'] = $goalResultCn;
    if ($ouResult == 3){
        $ouCss = "red";
        $ouResultCn = "胜";
        $h10WinCount++;
        if ($i < 5){
            $h5WinCount++;
        }
    } else{
        $ouCss = "blue";
        $ouResultCn = "负";
        $a10WinCount++;
        if ($i < 5){
            $a5WinCount++;
        }
    }
    $match['ouCss'] = $ouCss;
    $match['ouResultCn'] = $ouResultCn;

    $tempMatches[] = $match;
}

//计算结果
$totalCount10 = $h10WinCount + $a10WinCount;
$totalCount5 = $h5WinCount + $a5WinCount;
$h10AvgScore = $totalCount10 > 0 ? number_format($h10TotalScore / $totalCount10, 2) : 0;
$h5AvgScore = $totalCount5 > 0 ? number_format($h5TotalScore / $totalCount5, 2) : 0;
$a10AvgScore = $totalCount10 > 0 ? number_format($a10TotalScore / $totalCount10, 2) : 0;
$a5AvgScore = $totalCount5 > 0 ? number_format($a5TotalScore / $totalCount5, 2) : 0;
?>
<div class="con" ma="{{$ma}}" ha="{{$ha}}">
    @component('pc.match_detail.basket_cell.data_battle_item_canbox',['key'=>10,'hWinCount'=>$h10WinCount,'aWinCount'=>$a10WinCount,'hAvgScore'=>$h10AvgScore,'aAvgScore'=>$a10AvgScore])
    @endcomponent
    @component('pc.match_detail.basket_cell.data_battle_item_canbox',['key'=>5,'hWinCount'=>$h5WinCount,'aWinCount'=>$a5WinCount,'hAvgScore'=>$h5AvgScore,'aAvgScore'=>$a5AvgScore,'show'=>0])
    @endcomponent
    @component('pc.match_detail.basket_cell.data_match_table', ['matches'=>$tempMatches])
    @endcomponent
</div>