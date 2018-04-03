<?php
if ($key == 'asia'){
    $className = 'asia';
    $winT = '主赢';
    $drawT = '走水';
    $loseT = '主输';

    $winTm = '赢';
    $drawTm = '走';
    $loseTm = '输';
}
else if($key == 'goal'){
    $className = 'goal';
    $winT = '大球';
    $drawT = '走水';
    $loseT = '小球';

    $winTm = '大';
    $drawTm = '走';
    $loseTm = '小';
}
else if($key == 'ou'){
    $className = 'europe';
    $winT = '胜';
    $drawT = '平';
    $loseT = '负';

    $winTm = '胜';
    $drawTm = '平';
    $loseTm = '负';
}

//重新构建5场的统计
$win5 = 0;
$draw5 = 0;
$lose5 = 0;
if (isset($odds) && count($odds) > 0) {
    $matchCount = count($odds['matches']);
    $resultCount = count($odds['result']);
    for($i = 0 ; $i < min(5,$resultCount);$i++){
        $result = $odds['result'][$i];
        if ($result == 3){
            $win5++;
        }
        elseif ($result == 1){
            $draw5++;
        }
        else{
            $lose5++;
        }
    }
    if ($matchCount > 0){
        $odds['win5'] = 100*$win5/min(5,$resultCount);
        $odds['draw5'] = 100*$draw5/min(5,$resultCount);
        $odds['lose5'] = 100*$lose5/min(5,$resultCount);
    }
} else {
    $matchCount = 0;
    $resultCount = 0;
}
?>
<div class="con {{$className}}" @if(!$show)  style="display: none;" @endif>
    <p class="result" num="10">{{$winT}}：{{$odds['win10']}}%&nbsp;&nbsp;{{$drawT}}：{{$odds['draw10']}}%&nbsp;&nbsp;{{$loseT}}：{{$odds['lose10']}}%</p>
    <p class="result" num="5" style="display: none;">{{$winT}}：{{$odds['win5']}}%&nbsp;&nbsp;{{$drawT}}：{{$odds['draw5']}}%&nbsp;&nbsp;{{$loseT}}：{{$odds['lose5']}}%</p>
    <p class="num"><button class="on" value="10">近10场</button><button value="5">近5场</button></p>
    <table>
        <colgroup>
            <col num="1" width="12%">
            <col num="2" width="9.4%">
            <col num="3" width="">
            <col num="4" width="8%">
            <col num="5" width="">
            <col num="6" width="60px">
            <col num="7" width="8.5%">
            <col num="8" width="60px">
            <col num="9" width="60px">
            <col num="10" width="8.5%">
            <col num="11" width="60px">
            <col num="12" width="50px">
        </colgroup>
        <thead>
        <tr>
            <th>赛事</th>
            <th>日期</th>
            <th>主队</th>
            <th>比分（半场）</th>
            <th>客队</th>
            <th colspan="3">初盘</th>
            <th colspan="3">终盘</th>
            <th>结果</th>
        </tr>
        </thead>
        <tbody>
        @for($i = 0 ; $i < min(10,$matchCount); $i++)
            <?php
            $result = $odds['result'][$i];
            $match = $odds['matches'][$i];
            $time = strtotime($match['time']);
            $time = date('Y.m.d',$time);
            $resultCss = $result == 3 ? 'red' : ($result == 1 ? 'green':'blue');
            $resultCn = $result == 3 ? $winTm : ($result == 1 ? $drawTm : $loseTm);
            $middle1 = $match['middle1'];
            $middle2 = $match['middle2'];
            if ($key == 'asia')
            {
                $middle1 = \App\Http\Controllers\PC\CommonTool::getHandicapCn($middle1);
                $middle2 = \App\Http\Controllers\PC\CommonTool::getHandicapCn($middle2);
            }
            ?>
            <tr>
                <td>{{$match['lname']}}</td>
                <td>{{$time}}</td>
                <td>{{$match['hname']}}</td>
                <td>{{$match['hscore']}}-{{$match['ascore']}}（{{$match['hscorehalf']}}-{{$match['ascorehalf']}}）</td>
                <td>{{$match['aname']}}</td>
                <td>{{$match['up1']}}</td>
                <td>{{$middle1}}</td>
                <td>{{$match['down1']}}</td>
                <td>{{$match['up2']}}</td>
                <td>{{$middle2}}</td>
                <td>{{$match['down2']}}</td>
                <td class="{{$resultCss}}">{{$resultCn}}</td>
            </tr>
        @endfor
        </tbody>
    </table>
</div>