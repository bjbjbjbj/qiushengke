<?php
//计算结果
$sortData = [
        'asia'=>
                [
                        '1'=>[],
                        '2'=>[]
                ],
        'goal'=>
                [
                        '1'=>[],
                        '2'=>[]
                ],
        'result'=>
                [
                        '1'=>[],
                        '2'=>[]
                ],
];
//逻辑没做优惠,先完成功能
$keys = ['asia','goal','result'];
$middles = ['1','2'];
for ($i = 0 ; $i < min(10,count($matches));$i++){
    $match = $matches[$i];
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
            $sortData[$key][$middle][] = $result;
        }
    }
}
?>
<div class="con {{$className}}" ma="{{$ma}}" ha="{{$ha}}" @if($show == 0) style="display: none;" @endif>
    <div class="svgBox" num="10">
        @component('pc.match_detail.foot_cell.data_history_item_dl',['key'=>'result','key2'=>'1','className'=>'europe start','sortData'=>$sortData,'fill_key'=>$fill_key])
        @endcomponent
        @component('pc.match_detail.foot_cell.data_history_item_dl',['key'=>'result','key2'=>'2','show'=>0,'className'=>'europe end','sortData'=>$sortData,'fill_key'=>$fill_key])
        @endcomponent
        @component('pc.match_detail.foot_cell.data_history_item_dl',['key'=>'asia','key2'=>'1','className'=>'asia start','sortData'=>$sortData,'fill_key'=>$fill_key])
        @endcomponent
        @component('pc.match_detail.foot_cell.data_history_item_dl',['key'=>'asia','key2'=>'2','show'=>0,'className'=>'asia end','sortData'=>$sortData,'fill_key'=>$fill_key])
        @endcomponent
        @component('pc.match_detail.foot_cell.data_history_item_dl',['key'=>'goal','key2'=>'1','className'=>'goal start','sortData'=>$sortData,'fill_key'=>$fill_key])
        @endcomponent
        @component('pc.match_detail.foot_cell.data_history_item_dl',['key'=>'goal','key2'=>'2','show'=>0,'className'=>'goal end','sortData'=>$sortData,'fill_key'=>$fill_key])
        @endcomponent
    </div>
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
                <td class="{{$resulto == 3?'red':($resulto == 1 ? 'blue' :'green')}}">{{$resulto == 3?'胜':($resulto == 1 ? '平' :'负')}}</td>
                <td class="asia">
                    @if(isset($match['asiamiddle1']))
                        <p class="start {{$resulta1 == 3?'red':($resulta1 == 1 ? 'blue' :'green')}}">{{$resulta1 == 3?'赢':($resulta1 == 1 ? '走' :'输')}}</p>
                    @else
                        <p class="start"></p>
                    @endif
                    @if(isset($match['asiamiddle2']))
                        <p class="end {{$resulta2 == 3?'red':($resulta2 == 1 ? 'blue' :'green')}}" style="display: none;">{{$resulta2 == 3?'赢':($resulta2 == 1 ? '走' :'输')}}</p>
                    @else
                        <p class="endstart"></p>
                    @endif
                </td>
                <td class="goal">
                    @if(isset($match['goalmiddle1']))
                        <p class="start {{$resultg1 == 3?'red':($resultg1 == 1 ? 'blue' :'green')}}">{{$resultg1 == 3?'大':($resultg1 == 1 ? '走' :'小')}}</p>
                    @else
                        <p class="start"></p>
                    @endif
                    @if(isset($match['goalmiddle2']))
                        <p class="end {{$resultg2 == 3?'red':($resultg2 == 1 ? 'blue' :'green')}}" style="display: none;">{{$resultg2 == 3?'大':($resultg2 == 1 ? '走' :'小')}}</p>
                    @else
                        <p class="end"></p>
                    @endif
                </td>
            </tr>
        @endfor
        </tbody>
    </table>
</div>