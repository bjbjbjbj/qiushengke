<?php
$total = count($matches);
$win = 0;
$draw = 0;
$lose = 0;
$win5 = 0;
$draw5 = 0;
$lose5 = 0;
if ($total > 0){
    $total = 0;
    for ($i = 0 ; $i < min(10,count($matches));$i++){
        $match = $matches[$i];
        if (!isset($match['middle2'])){
            continue;
        }
        $total++;
        $result = \App\Http\Controllers\PC\OddCalculateTool::getMatchSizeOddResult($match['h_corner'],$match['a_corner'],$match['middle2']);
        if (3 == $result){
            $win++;
            if ($i < 5){
                $win5++;
            }
        }
        elseif (1 == $result){
            $draw++;
            if ($i < 5){
                $draw5++;
            }
        }
        else{
            $lose++;
            if ($i < 5){
                $lose5++;
            }
        }
    }
    if ($total > 0){
        $win = round(100*$win/min(10,$total),2);
        $draw = round(100*$draw/min(10,$total),2);
        $lose = 100 - $win - $draw;

        $win5 = round(100*$win5/min(5,$total),2);
        $draw5 = round(100*$draw5/min(5,$total),2);
        $lose5 = 100 - $win5 - $draw5;
    }
}
?>
<div class="con" ma="{{$ma}}" ha="{{$ha}}">
    <dl num="10">
        <dt class="win">大球{{$win}}%</dt>
        <dt class="draw">走水{{$draw}}%</dt>
        <dt class="lose">小球{{$lose}}%</dt>
        <dd><p class="win" style="width: {{$win}}%"></p><p class="draw" style="width: {{$draw}}%"></p><p class="lose" style="width: {{$lose}}%"></p></dd>
    </dl>
    <dl num="5" style="display: none;">
        <dt class="win">大球{{$win5}}%</dt>
        <dt class="draw">走水{{$draw5}}%</dt>
        <dt class="lose">小球{{$lose5}}%</dt>
        <dd><p class="win" style="width: {{$win5}}%"></p><p class="draw" style="width: {{$draw5}}%"></p><p class="lose" style="width: {{$lose5}}%"></p></dd>
    </dl>
    <table>
        <colgroup>
            <col num="1" width="120px">
            <col num="2" width="120px">
            <col num="3" width="">
            <col num="4" width="11.5%">
            <col num="5" width="">
            <col num="6" width="6.25%">
            <col num="7" width="6.25%">
            <col num="8" width="6.25%">
            <col num="9" width="6.25%">
        </colgroup>
        <thead>
        <tr>
            <th>赛事</th>
            <th>日期</th>
            <th>主队</th>
            <th>角球比分（半场）</th>
            <th>客队</th>
            <th colspan="3">盘口</th>
            <th>大小</th>
        </tr>
        </thead>
        <tbody>
        @for($i = 0 ; $i < min(10,count($matches));$i++)
            <?php
            $match = $matches[$i];
            $time = strtotime($match['time']);
            $time = date('Y.m.d',$time);
            $time = substr($time,2);
            $lid = $match['lid'];
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
            ?>
            <tr>
                <td><p class="line" style="background: rgb({{$r}}, {{$g}}, {{$b}});"></p>{{$match['league']}}</td>
                <td>{{$time}}</td>
                <td>{{$match['hname']}}</td>
                <td>{{$match['h_corner']}}-{{$match['a_corner']}}<span class="half">（{{$match['h_half_corner']}}-{{$match['a_half_corner']}}）</span></td>
                <td>{{$match['aname']}}</td>
                <td>{{$match['up2']}}</td>
                <td>{{$match['middle2']}}</td>
                <td>{{$match['down2']}}</td>
                @if(isset($match['middle2']))
                    @if($match['h_corner'] + $match['a_corner'] > $match['middle2'])
                        <td class="red">大</td>
                    @elseif($match['h_corner'] + $match['a_corner'] < $match['middle2'])
                        <td class="blue">小</td>
                    @else
                        <td class="green">走</td>
                    @endif
                @else
                    <td>-</td>
                @endif
            </tr>
        @endfor
        </tbody>
    </table>
</div>