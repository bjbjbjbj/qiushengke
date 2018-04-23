<div id="Data" class="content" style="display: @if(!isset($show)) none @endif;">
    @if(isset($odds) && count($odds) > 0)
        <div class="odd default">
            @component('phone.detail.football.cell.data_odd_cell', ['odds'=>$odds])
            @endcomponent
        </div>
    @endif
    <?php $rank = isset($analyse['rank']) ? $analyse['rank'] : null; ?>
    @if(isset($rank) && count($rank) > 0)
        <div class="rank default">
            <div class="title">积分排名<button class="close"></button></div>
            <?php $homeRank = isset($rank['host']) ? $rank['host'] : null; ?>
            @if(isset($homeRank))
                <p class="teamName"><span>{{$match['hname']}}</span></p>
                <table>
                    <thead>
                    <tr>
                        <th>全场</th>
                        <th>赛</th>
                        <th>胜/负</th>
                        <th>得/失</th>
                        <th>净</th>
                        <th>排名</th>
                        <th>胜率</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($homeRank['all']))
                        <?php $rankItem = $homeRank['all']; ?>
                        <tr>
                            <td>总</td>
                            <td>{{$rankItem['count']}}</td>
                            <td>{{$rankItem['win']}}/{{$rankItem['lose']}}</td>
                            <td>{{number_format($rankItem['goal'],1)}}/{{number_format($rankItem['fumble'],1)}}</td>
                            <td>{{number_format($rankItem['goal'] - $rankItem['fumble'], 1)}}</td>
                            <td>{{$rankItem['rank']}}</td>
                            <td>{{$rankItem['count'] > 0 ? number_format($rankItem['win']*100/$rankItem['count'], 1) : 0}}%</td>
                        </tr>
                    @endif
                    @if(isset($homeRank['home']))
                        <?php $rankItem = $homeRank['home']; ?>
                        <tr>
                            <td>主</td>
                            <td>{{$rankItem['count']}}</td>
                            <td>{{$rankItem['win']}}/{{$rankItem['lose']}}</td>
                            <td>{{number_format($rankItem['goal'],1)}}/{{number_format($rankItem['fumble'],1)}}</td>
                            <td>{{number_format($rankItem['goal'] - $rankItem['fumble'], 1)}}</td>
                            <td>{{$rankItem['rank']}}</td>
                            <td>{{$rankItem['count'] > 0 ? number_format($rankItem['win']*100/$rankItem['count'], 1) : 0}}%</td>
                        </tr>
                    @endif
                    @if(isset($homeRank['guest']))
                        <?php $rankItem = $homeRank['guest']; ?>
                        <tr>
                            <td>客</td>
                            <td>{{$rankItem['count']}}</td>
                            <td>{{$rankItem['win']}}/{{$rankItem['lose']}}</td>
                            <td>{{number_format($rankItem['goal'],1)}}/{{number_format($rankItem['fumble'],1)}}</td>
                            <td>{{number_format($rankItem['goal'] - $rankItem['fumble'], 1)}}</td>
                            <td>{{$rankItem['rank']}}</td>
                            <td>{{$rankItem['count'] > 0 ? number_format($rankItem['win']*100/$rankItem['count'], 1) : 0}}%</td>
                        </tr>
                    @endif
                    @if(isset($homeRank['ten']))
                        <?php $rankItem = $homeRank['ten']; ?>
                        <tr>
                            <td>近10</td>
                            <td>{{$rankItem['count']}}</td>
                            <td>{{$rankItem['win']}}/{{$rankItem['lose']}}</td>
                            <td>{{number_format($rankItem['goal'],1)}}/{{number_format($rankItem['fumble'],1)}}</td>
                            <td>{{number_format($rankItem['goal'] - $rankItem['fumble'], 1)}}</td>
                            <td>{{$rankItem['rank']}}</td>
                            <td>{{$rankItem['count'] > 0 ? number_format($rankItem['win']*100/$rankItem['count'], 1) : 0}}%</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            @endif
            <?php $awayRank = isset($rank['away']) ? $rank['away'] : null; ?>
            @if(isset($awayRank))
                <p class="teamName"><span>{{$match['aname']}}</span></p>
                <table>
                    <thead>
                    <tr>
                        <th>全场</th>
                        <th>赛</th>
                        <th>胜/负</th>
                        <th>得/失</th>
                        <th>净</th>
                        <th>排名</th>
                        <th>胜率</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($awayRank['all']))
                        <?php $rankItem = $awayRank['all']; ?>
                        <tr>
                            <td>总</td>
                            <td>{{$rankItem['count']}}</td>
                            <td>{{$rankItem['win']}}/{{$rankItem['lose']}}</td>
                            <td>{{number_format($rankItem['goal'],1)}}/{{number_format($rankItem['fumble'],1)}}</td>
                            <td>{{number_format($rankItem['goal'] - $rankItem['fumble'], 1)}}</td>
                            <td>{{$rankItem['rank']}}</td>
                            <td>{{$rankItem['count'] > 0 ? number_format($rankItem['win']*100/$rankItem['count'], 1) : 0}}%</td>
                        </tr>
                    @endif
                    @if(isset($awayRank['home']))
                        <?php $rankItem = $awayRank['home']; ?>
                        <tr>
                            <td>主</td>
                            <td>{{$rankItem['count']}}</td>
                            <td>{{$rankItem['win']}}/{{$rankItem['lose']}}</td>
                            <td>{{number_format($rankItem['goal'],1)}}/{{number_format($rankItem['fumble'],1)}}</td>
                            <td>{{number_format($rankItem['goal'] - $rankItem['fumble'], 1)}}</td>
                            <td>{{$rankItem['rank']}}</td>
                            <td>{{$rankItem['count'] > 0 ? number_format($rankItem['win']*100/$rankItem['count'], 1) : 0}}%</td>
                        </tr>
                    @endif
                    @if(isset($awayRank['guest']))
                        <?php $rankItem = $awayRank['guest']; ?>
                        <tr>
                            <td>客</td>
                            <td>{{$rankItem['count']}}</td>
                            <td>{{$rankItem['win']}}/{{$rankItem['lose']}}</td>
                            <td>{{number_format($rankItem['goal'],1)}}/{{number_format($rankItem['fumble'],1)}}</td>
                            <td>{{number_format($rankItem['goal'] - $rankItem['fumble'], 1)}}</td>
                            <td>{{$rankItem['rank']}}</td>
                            <td>{{$rankItem['count'] > 0 ? number_format($rankItem['win']*100/$rankItem['count'], 1) : 0}}%</td>
                        </tr>
                    @endif
                    @if(isset($awayRank['ten']))
                        <?php $rankItem = $awayRank['ten']; ?>
                        <tr>
                            <td>近10</td>
                            <td>{{$rankItem['count']}}</td>
                            <td>{{$rankItem['win']}}/{{$rankItem['lose']}}</td>
                            <td>{{number_format($rankItem['goal'],1)}}/{{number_format($rankItem['fumble'],1)}}</td>
                            <td>{{number_format($rankItem['goal'] - $rankItem['fumble'], 1)}}</td>
                            <td>{{$rankItem['rank']}}</td>
                            <td>{{$rankItem['count'] > 0 ? number_format($rankItem['win']*100/$rankItem['count'], 1) : 0}}%</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            @endif
        </div>
    @endif
    @component('phone.detail.basketball.cell.analyse_battle_cell', ['cdn'=>$cdn,'base'=>$analyse, 'match'=>$match])
    @endcomponent
</div>