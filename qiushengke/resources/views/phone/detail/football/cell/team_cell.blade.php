<div id="Team" class="content" style="display: @if(!isset($show)) none @endif;">
    @component('phone.detail.football.cell.team_trait_cell',
        ['match'=>$match, 'attribute'=>(isset($analyse['attribute']) ? $analyse['attribute'] : null)
            , 'ws'=> (isset($analyse['ws']) ? $analyse['ws'] : null)
        ]) @endcomponent
    <div id="Corner" class="childNode" style="display: none;">
        <div class="odd default">
            <div class="title">角球指数<button class="close"></button></div>
            <table>
                <thead>
                <tr>
                    <th></th>
                    <th>大球</th>
                    <th>盘口</th>
                    <th>小球</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>初盘</td>
                    <td>{{isset($match['cornerup1']) ? $match['cornerup1'] : '-'}}</td>
                    <td>{{isset($match['cornermiddle1']) ? $match['cornermiddle1'] . '球' : '-'}}</td>
                    <td>{{isset($match['cornerdown1']) ? $match['cornerdown1'] : '-'}}</td>
                </tr>
                <tr>
                    <td>即时</td>
                    <td>{{isset($match['cornerup2']) ? $match['cornerup2'] : '-'}}</td>
                    <td>{{isset($match['cornermiddle2']) ? $match['cornermiddle2'] . '球' : '-'}}</td>
                    <td>{{isset($match['cornerdown2']) ? $match['cornerdown2'] : '-'}}</td>
                </tr>
                </tbody>
            </table>
        </div>
        @if(isset($analyse['cornerAnalyse']))
        <?php
            $ca = $analyse['cornerAnalyse'];
            $hca = isset($analyse['cornerAnalyse']['home']) ? $analyse['cornerAnalyse']['home'] : null;
            $aca = isset($analyse['cornerAnalyse']['away']) ? $analyse['cornerAnalyse']['away'] : null;

            $hca = isset($hca['30']) ? $hca['30'] : (isset($hca['20']) ? $hca['20'] : (isset($hca['10']) ?  $hca['10'] : null) );
            $aca = isset($aca['30']) ? $aca['30'] : (isset($aca['20']) ? $aca['20'] : (isset($aca['10']) ?  $aca['10'] : null) );
        ?>
        <div class="total default" ha="0" le="0">
            <div class="title">
                数据统计<button class="close"></button>
            </div>
            <table ha="0" le="0">
                <thead>
                <tr>
                    <th>球队</th>
                    <th>得球</th>
                    <th>失球</th>
                    <th>净胜</th>
                    <th>总数</th>
                    <th>大球率</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{$match['hname']}}</td>
                    <td>{{round($hca['get'],0)}}</td>
                    <td>{{round($hca['lose'],0)}}</td>
                    <td>{{round($hca['leave'],0)}}</td>
                    <td>{{$hca['lose'] + $hca['get']}}</td>
                    <td>{{round($hca['big'],0)}}%</td>
                </tr>
                <tr>
                    <td>{{$match['aname']}}</td>
                    <td>{{round($aca['get'],0)}}</td>
                    <td>{{round($aca['lose'],0)}}</td>
                    <td>{{round($aca['leave'],0)}}</td>
                    <td>{{$aca['lose'] + $aca['get']}}</td>
                    <td>{{round($aca['big'],0)}}%</td>
                </tr>
                <tr>
                    <td colspan="6">* 备注：数值为角球场均值</td>
                </tr>
                </tbody>
            </table>
        </div>
        @endif
        @if(isset($analyse['cornerHistoryBattle']))
            @component('phone.detail.football.cell.team_corner_history_cell', ['history'=>$analyse['cornerHistoryBattle'],
                'match'=>$match])
            @endcomponent
        @endif
        @if(isset($analyse['cornerRecentBattle']))
        @component('phone.detail.football.cell.team_corner_recent_cell', ['match'=>$match, 'recent'=>$analyse['cornerRecentBattle']])
        @endcomponent
        @endif
    </div>
    {{--<div class="bottom">--}}
        {{--<div class="btn">--}}
            {{--<input type="radio" name="Team" id="Team_Trait" value="Trait" checked>--}}
            {{--<label for="Team_Trait">特点</label>--}}
            {{--<input type="radio" name="Team" id="Team_Corner" value="Corner">--}}
            {{--<label for="Team_Corner">角球</label>--}}
        {{--</div>--}}
    {{--</div>--}}
</div>