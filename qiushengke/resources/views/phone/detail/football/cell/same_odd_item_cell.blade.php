@if(isset($odd))
<?php
    if ($type == 1) {
        $id = "SameOdd_Asia";
        $class = "sameOddAsia";
        $name = "sameOdd_asia";
    } else if ($type == 2) {
        $id = "SameOdd_Goal";
        $class = "sameOddGoal";
        $name = "sameOdd_goal";
    } else {
        $id = "SameOdd_Europe";
        $class = "sameOddEurope";
        $name = "sameOdd_europe";
    }
    $result = isset($odd['result']) ? $odd['result'] : [];
?>
<div id="{{$id}}" class="{{$class}} default childNode" style="display: @if($type != 1) none @endif;" ha="0" le="0">
        <div class="title">历史同赔</div>
        <div class="proportion" ha="0" le="0">
            <p class="win" style="width: {{isset($odd['win']) ? $odd['win'] : 0}}%;"><b>{{isset($odd['win']) ? $odd['win'] : 0}}%</b></p>
            <p class="draw" style="width: {{isset($odd['draw']) ? $odd['draw'] : 0}}%;"><b>{{isset($odd['draw']) ? $odd['draw'] : 0}}%</b></p>
            <p class="lose" style="width: {{isset($odd['lose']) ? $odd['lose'] : 0}}%;"><b>{{isset($odd['lose']) ? $odd['lose'] : 0}}%</b></p>
        </div>
        @if(isset($odd['matches']))
        <table ha="0" le="0">
            <thead>
            <tr>
                <th>赛事</th>
                <th>对阵</th>
                <th>比分</th>
                <th>初盘/终盘</th>
                <th>结果</th>
            </tr>
            </thead>
            <tbody>
            @foreach($odd['matches'] as $mIndex=>$match)
                <?php
                    $mr = isset($result[$mIndex]) ? $result[$mIndex] : '';
                    $uClass = \App\Http\Controllers\PC\CommonTool::colorOfWapUpDown($match['up1'], $match['up2']);
                    $mClass = \App\Http\Controllers\PC\CommonTool::colorOfWapUpDown($match['middle1'], $match['middle2']);
                    $dClass = \App\Http\Controllers\PC\CommonTool::colorOfWapUpDown($match['down1'], $match['down2']);

                    $uClass = empty($uClass) ? '' : 'class="'.$uClass.'"';
                    $mClass = empty($mClass) ? '' : 'class="'.$mClass.'"';
                    $dClass = empty($dClass) ? '' : 'class="'.$dClass.'"';

                    if ($type == 3) {
                        $m1 = $match['middle1'];
                        $m2 = $match['middle2'];
                    } else {
                        $m1 = \App\Http\Controllers\PC\CommonTool::getOddMiddleString($match['middle1']);
                        $m2 = \App\Http\Controllers\PC\CommonTool::getOddMiddleString($match['middle2']);
                    }
                ?>
                <tr>
                    <td>{{$match['lname']}}</td>
                    <td>
                        <p>{{$match['hname']}}</p>
                        <p>{{$match['aname']}}</p>
                    </td>
                    <td>
                        <p>{{$match['hscore']}}</p>
                        <p>{{$match['ascore']}}</p>
                    </td>
                    <td>
                        <p><span>{{$match['up1']}}</span><span>{{$m1}}</span><span>{{$match['down1']}}</span></p>
                        <p><span {!! $uClass !!}>{{$match['up2']}}</span><span {!! $mClass !!}>{{$m2}}</span><span {!! $dClass !!}>{{$match['down2']}}</span></p>
                    </td>
                    <td>
                        @if($type == 1)
                            @if($mr == 3)<p class="win">赢</p> @elseif($mr == 1)<p class="draw">走</p> @elseif($mr == 0)<p class="lose">输</p>@endif
                        @elseif ($type == 3)
                            @if($mr == 3)<p class="win">胜</p> @elseif($mr == 1)<p class="draw">平</p> @elseif($mr == 0)<p class="lose">负</p>@endif
                        @else
                            @if($mr == 3)<p class="win">大</p> @elseif($mr == 1)<p class="draw">走</p> @elseif($mr == 0)<p class="lose">小</p>@endif
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif
</div>
@endif