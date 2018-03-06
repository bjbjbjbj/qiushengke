<?php
//亚赔
$asiaUp1 = "-";
$asiaMiddle1 = "-";
$asiaDown1 = "-";
$asiaUp2 = "-";
$asiaMiddle2 = "-";
$asiaDown2 = "-";
if (isset($cell_odd['all']['1']['middle1'])) {
    $asiaUp1 = \App\Http\Controllers\PC\CommonTool::float2Decimal($cell_odd['all']['1']['up1']);
    $asiaDown1 = \App\Http\Controllers\PC\CommonTool::float2Decimal($cell_odd['all']['1']['down1']);
    $asiaMiddle1 = \App\Http\Controllers\PC\CommonTool::getHandicapCn($cell_odd['all']['1']['middle1'], "-", \App\Http\Controllers\PC\CommonTool::k_odd_type_asian);
}
if (isset($cell_odd['all']['1']['middle2'])) {
    $asiaUp2 = \App\Http\Controllers\PC\CommonTool::float2Decimal($cell_odd['all']['1']['up2']);
    $asiaDown2 = \App\Http\Controllers\PC\CommonTool::float2Decimal($cell_odd['all']['1']['down2']);
    $asiaMiddle2 = \App\Http\Controllers\PC\CommonTool::getHandicapCn($cell_odd['all']['1']['middle2'], "-", \App\Http\Controllers\PC\CommonTool::k_odd_type_asian);
}

//大小球
$goalUp1 = "-";
$goalMiddle1 = "-";
$goalDown1 = "-";
$goalUp2 = "-";
$goalMiddle2 = "-";
$goalDown2 = "-";
if (isset($cell_odd['all']['2']['middle1'])) {
    $goalUp1 = \App\Http\Controllers\PC\CommonTool::float2Decimal($cell_odd['all']['2']['up1']);
    $goalDown1 = \App\Http\Controllers\PC\CommonTool::float2Decimal($cell_odd['all']['2']['down1']);
    $goalMiddle1 = \App\Http\Controllers\PC\CommonTool::getHandicapCn($cell_odd['all']['2']['middle1'], "-", \App\Http\Controllers\PC\CommonTool::k_odd_type_ou);
}
if (isset($cell_odd['all']['2']['middle2'])) {
    $goalUp2 = \App\Http\Controllers\PC\CommonTool::float2Decimal($cell_odd['all']['2']['up2']);
    $goalDown2 = \App\Http\Controllers\PC\CommonTool::float2Decimal($cell_odd['all']['2']['down2']);
    $goalMiddle2 = \App\Http\Controllers\PC\CommonTool::getHandicapCn($cell_odd['all']['2']['middle2'], "-", \App\Http\Controllers\PC\CommonTool::k_odd_type_ou);
}


//欧赔
$ouUp1 = "-";
$ouMiddle1 = "-";
$ouDown1 = "-";
$ouUp2 = "-";
$ouMiddle2 = "-";
$ouDown2 = "-";
if (isset($cell_odd['all']['3']['middle1'])) {
    $ouUp1 = \App\Http\Controllers\PC\CommonTool::float2Decimal($cell_odd['all']['3']['up1']);
    $ouDown1 = \App\Http\Controllers\PC\CommonTool::float2Decimal($cell_odd['all']['3']['down1']);
    $ouMiddle1 = \App\Http\Controllers\PC\CommonTool::float2Decimal($cell_odd['all']['3']['middle1']);
}
if (isset($cell_odd['all']['3']['middle2'])) {
    $ouUp2 = \App\Http\Controllers\PC\CommonTool::float2Decimal($cell_odd['all']['3']['up2']);
    $ouDown2 = \App\Http\Controllers\PC\CommonTool::float2Decimal($cell_odd['all']['3']['down2']);
    $ouMiddle2 = \App\Http\Controllers\PC\CommonTool::float2Decimal($cell_odd['all']['3']['middle2']);
}
?>
<colgroup>
    <col num="o1" width="45px">
    <col num="o2" width="65px">
    <col num="o3" width="150px">
    <col num="o4" width="110px">
    <col num="o5" width="110px">
</colgroup>
<thead>
<tr>
    <th></th>
    <th></th>
    <th>亚</th>
    <th>欧</th>
    <th>大</th>
</tr>
</thead>
<tbody>
<tr>
    <td rowspan="2">全场</td>
    <td>初盘指数</td>
    <td><p>{{$asiaUp1}}</p><p class="od">{{$asiaMiddle1}}</p><p>{{$asiaDown1}}</p></td>
    <td><p>{{$ouUp1}}</p><p>{{$ouMiddle1}}</p><p>{{$ouDown1}}</p></td>
    <td><p>{{$goalUp1}}</p><p>{{$goalMiddle1}}</p><p>{{$goalDown1}}</p></td>
</tr>
<tr>
    <td>即时指数</td>
    <td><p>{{$asiaUp2}}</p><p class="od">{{$asiaMiddle2}}</p><p>{{$asiaDown2}}</p></td>
    <td><p>{{$ouUp2}}</p><p>{{$ouMiddle2}}</p><p>{{$ouDown2}}</p></td>
    <td><p>{{$goalUp2}}</p><p>{{$goalMiddle2}}</p><p>{{$goalDown2}}</p></td>
</tr>
<tr>
    <td colspan="5"></td>
</tr>
</tbody>
<tbody>
<tr>
    <td rowspan="2">半场</td>
    <td>初盘指数</td>
    <td><p>0.91</p><p class="od">平手</p><p>0.85</p></td>
    <td><p>3.20</p><p>3.75</p><p>1.85</p></td>
    <td><p>0.91</p><p>3</p><p>0.85</p></td>
</tr>
<tr>
    <td>即时指数</td>
    <td><p>0.91</p><p class="od">平手/半球</p><p>0.85</p></td>
    <td><p>3.20</p><p>3.75</p><p>1.85</p></td>
    <td><p>0.91</p><p>3</p><p>0.85</p></td>
</tr>
</tbody>