<?php
$ws = $analyse['ws'];
$rank = $analyse['rank'];
$sameOdd = $analyse['sameOdd'];
?>
<div id="Character" style="display: none;">
    @if(isset($ws) && (isset($ws['home']) || isset($ws['away'])))
        <div class="strength">
            <?php
            //强弱项总行数
            $items = $ws['home'];
            $strengthCount = isset($items['strengths']) ? count($items['strengths']) : 0;
            $weaknessCount = isset($items['weaknesses']) ? count($items['weaknesses']) : 0;
            $stylesCount = isset($items['styles']) ? count($items['styles']) : 0;
            $items2 = $ws['away'];
            $strengthCount2 = isset($items2['strengths']) ? count($items2['strengths']) : 0;
            $weaknessCount2 = isset($items2['weaknesses']) ? count($items2['weaknesses']) : 0;
            $stylesCount2 = isset($items2['styles']) ? count($items2['styles']) : 0;
            $scount = max($stylesCount2,$stylesCount);
            $count = max($strengthCount + $weaknessCount,$strengthCount2 + $weaknessCount2);
            ?>
            @component('pc.match_detail.foot_cell.character_item',['scount'=>$scount,'count'=>$count,'items'=>$ws['home'],'key'=>'host','tname'=>$match['hname'],'rank'=>$rank['leagueRank']['hLeagueRank'],'rankLeague'=>$rank['leagueRank']['hLeagueName']])
            @endcomponent
            @component('pc.match_detail.foot_cell.character_item',['scount'=>$scount,'count'=>$count,'items'=>$ws['away'],'key'=>'away','tname'=>$match['hname'],'rank'=>$rank['leagueRank']['hLeagueRank'],'rankLeague'=>$rank['leagueRank']['hLeagueName']])
            @endcomponent
        </div>
    @endif
    @if(isset($ws) && isset($ws['case']))
        <div class="prediction">
            <p class="title">场面预测</p>
            <table>
                <tr>
                    <th>预测项</th>
                    <th>可能性</th>
                </tr>
                @foreach($ws['case'] as $item)
                    <tr>
                        <td>{{$item['sentence']}}</td>
                        @if(3 <= $item['score'])
                            <td level="3">
                        @elseif(2 == $item['score'])
                            <td level="2">
                        @elseif(1 >= $item['score'])
                            <td level="1">
                                @endif
                                <span level="1"></span><span level="2"></span><span level="3"></span>
                            </td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif
    @if(isset($analyse['referee']))
        <?php
        $referee = $analyse['referee'];
        ?>
        <div class="referee">
            <p class="title">裁判</p>
            <table>
                <tr>
                    <th>姓名</th>
                    <th>{{$referee['hname']}}</th>
                    <th>{{$referee['aname']}}</th>
                    <th>近10场执法赛事</th>
                    <th>近10场均出示黄牌</th>
                </tr>
                <tr>
                    <td>{{$referee['name']}}</td>
                    <td>{{$referee['h_wdl']}}</td>
                    <td>{{$referee['a_wdl']}}</td>
                    <td>上盘率{{$referee['win_percent']}}%</td>
                    <td>{{$referee['yellow_avg']}}张</td>
                </tr>
            </table>
        </div>
    @endif
    @if(isset($sameOdd) && (isset($sameOdd['asia']) || isset($sameOdd['goal']) || isset($sameOdd['ou'])))
        <div class="sameOdd">
            <p class="title">历史同赔</p>
            <div class="tabBox">
                <?php
                $index = 0;
                if (!array_key_exists('asia',$sameOdd)){
                    $index = 1;
                    if (!array_key_exists('goal',$sameOdd)){
                        $index = 2;
                    }
                }
                $html = '';
                foreach($sameOdd as $key=>$odds){
                    if($key == 'asia')
                        $html = '<button class="on" value="asia">亚盘</button>';
                    if($key == 'goal')
                        if($index == 1)
                            $html = $html .'<button class="on" value="goal">大小</button>';
                        else
                            $html = $html .'<button value="goal">大小</button>';
                    if($key == 'ou')
                        if($index == 2)
                            $html = $html .'<button class="on" value="europe">欧盘</button>';
                        else
                            $html = $html .'<button value="europe">欧盘</button>';
                }
                ?>
                {!! $html !!}
            </div>
            @foreach($sameOdd as $key=>$odds)
                <?php
                $show = false;
                if ($key == 'asia' && $index == 0)
                    $show = true;
                else if ($key == 'goal' && $index == 1)
                    $show = true;
                else if ($key == 'ou' && $index == 2)
                    $show = true;
                ?>
                @component('pc.match_detail.foot_cell.character_sameodd',['show'=>$show,'key'=>$key,'odds'=>$odds])
                @endcomponent
            @endforeach
        </div>
    @endif
</div>