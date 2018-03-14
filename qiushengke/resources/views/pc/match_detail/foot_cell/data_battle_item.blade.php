<?php
//先处理数据
$matches = $data;
$count = count($matches);
$sortData = [
        'asia'=>
                [
                        '1'=>[
                                'win10'=>0,'draw10'=>0,'lose10'=>0,
                                'win5'=>0,'draw5'=>0,'lose5'=>0,
                        ],
                        '2'=>[
                                'win10'=>0,'draw10'=>0,'lose10'=>0,
                                'win5'=>0,'draw5'=>0,'lose5'=>0
                        ]
                ],
        'goal'=>
                [
                        '1'=>[
                                'win10'=>0,'draw10'=>0,'lose10'=>0,
                                'win5'=>0,'draw5'=>0,'lose5'=>0
                        ],
                        '2'=>[
                                'win10'=>0,'draw10'=>0,'lose10'=>0,
                                'win5'=>0,'draw5'=>0,'lose5'=>0
                        ]
                ],
        'result'=>
                [
                        '1'=>[
                                'win10'=>0,'draw10'=>0,'lose10'=>0,
                                'win5'=>0,'draw5'=>0,'lose5'=>0
                        ],
                        '2'=>[
                                'win10'=>0,'draw10'=>0,'lose10'=>0,
                                'win5'=>0,'draw5'=>0,'lose5'=>0
                        ]
                ],
];
//逻辑没做优惠,先完成功能
for ($i = 0 ; $i < min(10,count($matches));$i++){
    $match = $matches[$i];
    $keys = ['asia','goal','result'];
    $middles = ['1','2'];
    foreach ($keys as $key){
        foreach ($middles as $middle){
            if ($key == 'asia'){
                $result = \App\Http\Controllers\PC\OddCalculateTool::getMatchAsiaOddResult($match['hscore'],$match['ascore'],($middle == '1'?$match['asiamiddle1']:$match['asiamiddle2']),$match['hid'] == $tid);
            }
            elseif ($key == 'goal'){
                $result = \App\Http\Controllers\PC\OddCalculateTool::getMatchSizeOddResult($match['hscore'],$match['ascore'],($middle == '1'?$match['goalmiddle1']:$match['goalmiddle2']),$match['hid'] == $tid);
            }
            else{
                $result = \App\Http\Controllers\PC\OddCalculateTool::getMatchResult($match['hscore'],$match['ascore'],$match['hid'] == $tid);
            }
            if ($result == 3){
                $sortData[$key][$middle]['win10'] = $sortData[$key][$middle]['win10'] + 1;
                if ($i < 5){
                    $sortData[$key][$middle]['win5'] = $sortData[$key][$middle]['win5'] + 1;
                }
            }
            elseif ($result == 1){
                $sortData[$key][$middle]['draw10'] = $sortData[$key][$middle]['draw10'] + 1;
                if ($i < 5){
                    $sortData[$key][$middle]['draw5'] = $sortData[$key][$middle]['draw5'] + 1;
                }
            }
            else{
                $sortData[$key][$middle]['lose10'] = $sortData[$key][$middle]['lose10'] + 1;
                if ($i < 5){
                    $sortData[$key][$middle]['lose5'] = $sortData[$key][$middle]['lose5'] + 1;
                }
            }
        }
    }
}
//计算结果
$keys = ['asia','goal','result'];
$middles = ['1','2'];
foreach ($keys as $key){
    foreach ($middles as $middle){
        if (($sortData[$key][$middle]['win5'] + $sortData[$key][$middle]['draw5'] + $sortData[$key][$middle]['lose5']) > 0){
            $sortData[$key][$middle]['win5_p'] = round(100*$sortData[$key][$middle]['win5']/($sortData[$key][$middle]['win5'] + $sortData[$key][$middle]['draw5'] + $sortData[$key][$middle]['lose5']),0);
            $sortData[$key][$middle]['win10_p'] = round(100*$sortData[$key][$middle]['win10']/($sortData[$key][$middle]['win10'] + $sortData[$key][$middle]['draw10'] + $sortData[$key][$middle]['lose10']),0);
            $sortData[$key][$middle]['draw5_p'] = round(100*$sortData[$key][$middle]['draw5']/($sortData[$key][$middle]['win5'] + $sortData[$key][$middle]['draw5'] + $sortData[$key][$middle]['lose5']),0);
            $sortData[$key][$middle]['draw10_p'] = round(100*$sortData[$key][$middle]['draw10']/($sortData[$key][$middle]['win10'] + $sortData[$key][$middle]['draw10'] + $sortData[$key][$middle]['lose10']),0);
            $sortData[$key][$middle]['lose5_p'] = round(100*$sortData[$key][$middle]['lose5']/($sortData[$key][$middle]['win5'] + $sortData[$key][$middle]['draw5'] + $sortData[$key][$middle]['lose5']),0);
            $sortData[$key][$middle]['lose10_p'] = round(100*$sortData[$key][$middle]['lose10']/($sortData[$key][$middle]['win10'] + $sortData[$key][$middle]['draw10'] + $sortData[$key][$middle]['lose10']),0);
        }
        else{
            $sortData[$key][$middle]['win5_p'] = 0;
            $sortData[$key][$middle]['win10_p'] = 0;
            $sortData[$key][$middle]['draw5_p'] = 0;
            $sortData[$key][$middle]['draw10_p'] = 0;
            $sortData[$key][$middle]['lose5_p'] = 0;
            $sortData[$key][$middle]['lose10_p'] = 0;

        }
    }
}
?>
<div class="con" ma="{{$ma}}" ha="{{$ha}}">
    @component('pc.match_detail.foot_cell.data_battle_item_canbox',['key'=>10,'sortData'=>$sortData])
    @endcomponent
    @component('pc.match_detail.foot_cell.data_battle_item_canbox',['key'=>5,'sortData'=>$sortData,'show'=>0])
    @endcomponent
    <table>
        <colgroup>
            <col num="1" width="90px">
            <col num="2" width="70px">
            <col num="3" width="">
            <col num="4" width="50px">
            <col num="3" width="">
            <col num="5" width="4.5%">
            <col num="6" width="4.5%">
            <col num="7" width="4.5%">
            <col num="8" width="4.5%">
            <col num="9" width="100px">
            <col num="10" width="4.5%">
            <col num="11" width="4.5%">
            <col num="12" width="4.5%">
            <col num="13" width="4.5%">
            <col num="14" width="4.7%">
            <col num="15" width="4.7%">
            <col num="16" width="4.7%">
        </colgroup>
        <thead>
        <tr>
            <th rowspan="2">赛事</th>
            <th rowspan="2">日期</th>
            <th rowspan="2">主队</th>
            <th rowspan="2">比分<br/>（半场）</th>
            <th rowspan="2">客队</th>
            <th colspan="3">
                <select class="europe">
                    <option value="start">初盘</option>
                    <option value="end">终盘</option>
                </select>
            </th>
            <th colspan="3">
                <select class="asia">
                    <option value="start">初盘</option>
                    <option value="end">终盘</option>
                </select>
            </th>
            <th colspan="3">
                <select class="goal">
                    <option value="start">初盘</option>
                    <option value="end">终盘</option>
                </select>
            </th>
            <th rowspan="2">胜负</th>
            <th rowspan="2">让球</th>
            <th rowspan="2">大小</th>
        </tr>
        <tr>
            <th class="yellow">胜</th>
            <th class="yellow">平</th>
            <th class="yellow">负</th>
            <th class="green">主赢</th>
            <th class="green">盘口</th>
            <th class="green">主输</th>
            <th class="yellow">大球</th>
            <th class="yellow">盘口</th>
            <th class="yellow">小球</th>
        </tr>
        </thead>
        <tbody>
        @for($i = 0 ; $i < min(10,count($matches)); $i++)
            <?php
            $match = $matches[$i];
            //赛事背景色
            $bgRgb = \App\Http\Controllers\PC\CommonTool::getLeagueBgRgb($match['lid']);
            $r = $bgRgb['r'];
            $g = $bgRgb['g'];
            $b = $bgRgb['b'];
            $time = strtotime($match['time']);
            $time = date('Y.m.d',$time);
            $time = substr($time,2);
            ?>
            <tr>
                <td><p class="line" style="background: rgb({{$r}}, {{$g}}, {{$b}});"></p>{{$match['league']}}</td>
                <td>{{$time}}</td>
                <td>{{$match['hname']}}</td>
                <td>{{$match['hscore']}}-{{$match['ascore']}}
                    @if(isset($match['hscorehalf']))
                        <p class="half">({{$match['hscorehalf']}}-{{$match['ascorehalf']}})</p>
                    @endif
                </td>
                <td>{{$match['aname']}}</td>
                <td class="europe">
                    <p class="start">{{$match['ouup1']}}</p>
                    <p class="end" style="display: none;">{{$match['ouup2']}}</p>
                </td>
                <td class="europe">
                    <p class="start">{{$match['oumiddle1']}}</p>
                    <p class="end" style="display: none;">{{$match['oumiddle2']}}</p>
                </td>
                <td class="europe">
                    <p class="start">{{$match['oudown1']}}</p>
                    <p class="end" style="display: none;">{{$match['oudown2']}}</p>
                </td>
                <td class="asia">
                    <p class="start">{{$match['asiaup1']}}</p>
                    <p class="end" style="display: none;">{{$match['asiaup2']}}</p>
                </td>
                <td class="asia">
                    <p class="start">{{\App\Http\Controllers\PC\CommonTool::panKouText($match['asiamiddle1'])}}</p>
                    <p class="end" style="display: none;">{{\App\Http\Controllers\PC\CommonTool::panKouText($match['asiamiddle2'])}}</p>
                </td>
                <td class="asia">
                    <p class="start">{{$match['asiadown1']}}</p>
                    <p class="end" style="display: none;">{{$match['asiadown2']}}</p>
                </td>
                <td class="goal">
                    <p class="start">{{$match['goalup1']}}</p>
                    <p class="end" style="display: none;">{{$match['goalup2']}}</p>
                </td>
                <td class="goal">
                    <p class="start">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($match['goalmiddle1'], "-", \App\Http\Controllers\PC\CommonTool::k_odd_type_ou)}}</p>
                    <p class="end" style="display: none;">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($match['goalmiddle2'], "-", \App\Http\Controllers\PC\CommonTool::k_odd_type_ou)}}</p>
                </td>
                <td class="goal">
                    <p class="start">{{$match['goaldown1']}}</p>
                    <p class="end" style="display: none;">{{$match['goaldown2']}}</p>
                </td>
                <?php
                //计算结果
                $resulta1 = \App\Http\Controllers\PC\OddCalculateTool::getMatchAsiaOddResult($match['hscore'],$match['ascore'],$match['asiamiddle1'],$match['hid'] == $tid);
                $resultg1 = \App\Http\Controllers\PC\OddCalculateTool::getMatchSizeOddResult($match['hscore'],$match['ascore'],$match['goalmiddle1'],$match['hid'] == $tid);
                $resulta2 = \App\Http\Controllers\PC\OddCalculateTool::getMatchAsiaOddResult($match['hscore'],$match['ascore'],$match['asiamiddle2'],$match['hid'] == $tid);
                $resultg2 = \App\Http\Controllers\PC\OddCalculateTool::getMatchSizeOddResult($match['hscore'],$match['ascore'],$match['goalmiddle2'],$match['hid'] == $tid);
                $resulto = \App\Http\Controllers\PC\OddCalculateTool::getMatchResult($match['hscore'],$match['ascore'],$match['hid'] == $tid);
                ?>
                <td class="{{$resulto == 3?'green':($resulto == 1 ? 'gray' :'blue')}}">{{$resulto == 3?'胜':($resulto == 1 ? '平' :'负')}}</td>
                <td class="asia">
                    <p class="start {{$resulta1 == 3?'green':($resulta1 == 1 ? 'gray' :'blue')}}">{{$resulta1 == 3?'赢':($resulta1 == 1 ? '走' :'输')}}</p>
                    <p class="end {{$resulta2 == 3?'green':($resulta2 == 1 ? 'gray' :'blue')}}" style="display: none;">{{$resulta2 == 3?'赢':($resulta2 == 1 ? '走' :'输')}}</p>
                </td>
                <td class="goal">
                    <p class="start {{$resultg1 == 3?'green':($resultg1 == 1 ? 'gray' :'blue')}}">{{$resultg1 == 3?'大':($resultg1 == 1 ? '走' :'小')}}</p>
                    <p class="end {{$resultg2 == 3?'green':($resultg2 == 1 ? 'gray' :'blue')}}" style="display: none;">{{$resultg2 == 3?'大':($resultg2 == 1 ? '走' :'小')}}</p>
                </td>
            </tr>
        @endfor
        </tbody>
    </table>
</div>