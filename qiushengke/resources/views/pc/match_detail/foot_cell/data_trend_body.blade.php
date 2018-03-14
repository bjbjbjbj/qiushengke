<tbody>
@if(isset($data['all']))
    <?php
    $tmp = $data['all'];
    ?>
    <tr>
        <td>总</td>
        <td>{{$tmp['asiaWin'] + $tmp['asiaDraw'] + $tmp['asiaLose']}}</td>
        <td>{{$tmp['asiaWin']}}</td>
        <td>{{$tmp['asiaDraw']}}</td>
        <td>{{$tmp['asiaLose']}}</td>
        <td>{{$tmp['asiaPercent']}}%</td>
        <td>{{$tmp['goalBig']}}</td>
        <td>{{$tmp['goalBigPercent']}}%</td>
        <td>{{$tmp['goalSmall']}}</td>
        <td>{{$tmp['goalSmallPercent']}}%</td>
    </tr>
@endif
@if(isset($data['home']))
    <?php
    $tmp = $data['home'];
    ?>
    <tr>
        <td>主</td>
        <td>{{$tmp['asiaWin'] + $tmp['asiaDraw'] + $tmp['asiaLose']}}</td>
        <td>{{$tmp['asiaWin']}}</td>
        <td>{{$tmp['asiaDraw']}}</td>
        <td>{{$tmp['asiaLose']}}</td>
        <td>{{$tmp['asiaPercent']}}%</td>
        <td>{{$tmp['goalBig']}}</td>
        <td>{{$tmp['goalBigPercent']}}%</td>
        <td>{{$tmp['goalSmall']}}</td>
        <td>{{$tmp['goalSmallPercent']}}%</td>
    </tr>
@endif
@if(isset($data['away']))
    <?php
    $tmp = $data['away'];
    ?>
    <tr>
        <td>客</td>
        <td>{{$tmp['asiaWin'] + $tmp['asiaDraw'] + $tmp['asiaLose']}}</td>
        <td>{{$tmp['asiaWin']}}</td>
        <td>{{$tmp['asiaDraw']}}</td>
        <td>{{$tmp['asiaLose']}}</td>
        <td>{{$tmp['asiaPercent']}}%</td>
        <td>{{$tmp['goalBig']}}</td>
        <td>{{$tmp['goalBigPercent']}}%</td>
        <td>{{$tmp['goalSmall']}}</td>
        <td>{{$tmp['goalSmallPercent']}}%</td>
    </tr>
@endif
@if(isset($data['six']))
    <?php
    //处理数据
    $count = count($data['six']['result']);
    $awin = 0;
    $adraw = 0;
    $alose = 0;
    foreach ($data['six']['asia'] as $tmp){
        if ($tmp == 3)
            $awin++;
        elseif ($tmp == 1)
            $adraw++;
        else
            $alose++;
    }
    $gwin = 0;
    $gdraw = 0;
    $glose = 0;
    foreach ($data['six']['goal'] as $tmp){
        if ($tmp == 3)
            $gwin++;
        elseif ($tmp == 1)
            $gdraw++;
        else
            $glose++;
    }
    ?>
    <tr class="gray">
        <td>近6</td>
        <td>{{$count}}</td>
        <td>{{$awin}}</td>
        <td>{{$adraw}}</td>
        <td>{{$alose}}</td>
        <td>{{round(100*$awin/$count,2)}}%</td>
        <td>{{$gwin}}</td>
        <td>{{round(100*$gwin/$count,2)}}%</td>
        <td>{{$glose}}</td>
        <td>{{round(100*$glose/$count,2)}}%</td>
    </tr>
@endif
</tbody>