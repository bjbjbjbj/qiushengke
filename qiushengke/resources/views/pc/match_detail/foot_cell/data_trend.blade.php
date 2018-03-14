<?php
$rank = $analyse['rank'];
$oddResult = $analyse['oddResult'];
?>
<div class="trend">
    <p class="title">赛事盘路</p>
    <div class="part host">
        <p class="name">{{$match['hname']}}
            @if($rank['leagueRank']['hLeagueRank'] > 0)
                <span>【{{$rank['leagueRank']['hLeagueName']}}{{$rank['leagueRank']['hLeagueRank']}}】</span>
            @endif</p>
        <table>
            <colgroup>
                <col num="1" width="40px">
                <col num="2" width="">
                <col num="3" width="">
                <col num="4" width="">
                <col num="5" width="">
                <col num="6" width="60px">
                <col num="7" width="45px">
                <col num="8" width="60px">
                <col num="9" width="45px">
                <col num="10" width="60px">
            </colgroup>
            <thead>
            <tr>
                <th></th>
                <th>赛</th>
                <th>赢</th>
                <th>走</th>
                <th>输</th>
                <th>赢盘率</th>
                <th>大球</th>
                <th>大球率</th>
                <th>小球</th>
                <th>小球率</th>
            </tr>
            </thead>
            @component('pc.match_detail.foot_cell.data_trend_body',['data'=>$oddResult['home']])
            @endcomponent
        </table>
    </div>
    <div class="part away">
        <p class="name">{{$match['aname']}}
            @if($rank['leagueRank']['aLeagueRank'] > 0)
                <span>【{{$rank['leagueRank']['aLeagueName']}}{{$rank['leagueRank']['aLeagueRank']}}】</span>
            @endif
        </p>
        <table>
            <colgroup>
                <col num="1" width="40px">
                <col num="2" width="">
                <col num="3" width="">
                <col num="4" width="">
                <col num="5" width="">
                <col num="6" width="60px">
                <col num="7" width="45px">
                <col num="8" width="60px">
                <col num="9" width="45px">
                <col num="10" width="60px">
            </colgroup>
            <thead>
            <tr>
                <th></th>
                <th>赛</th>
                <th>赢</th>
                <th>走</th>
                <th>输</th>
                <th>赢盘率</th>
                <th>大球</th>
                <th>大球率</th>
                <th>小球</th>
                <th>小球率</th>
            </tr>
            </thead>
            @component('pc.match_detail.foot_cell.data_trend_body',['data'=>$oddResult['away']])
            @endcomponent
        </table>
    </div>
</div>