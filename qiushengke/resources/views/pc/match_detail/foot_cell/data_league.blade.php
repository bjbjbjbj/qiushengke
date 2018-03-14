<?php
$rank = $analyse['rank'];
?>
<div class="league">
    <p class="title">联赛排名积分</p>
    <div class="part host">
        <p class="name">{{$match['hname']}}
            @if($rank['leagueRank']['hLeagueRank'] > 0)
                <span>【{{$rank['leagueRank']['hLeagueName']}}{{$rank['leagueRank']['hLeagueRank']}}】</span>
                @endif</p>
        <table>
            <thead>
            <tr>
                <th></th>
                <th>赛</th>
                <th>胜</th>
                <th>平</th>
                <th>负</th>
                <th>得</th>
                <th>失</th>
                <th>净</th>
                <th>积分</th>
                <th>排名</th>
                <th>胜率</th>
            </tr>
            </thead>
            <tbody>
            @foreach($rank['rank']['host'] as $key=>$value)
                <?php
                $tmp = '';
                switch ($key){
                    case 'all':
                        $tmp = '总';
                        break;
                    case 'home':
                        $tmp = '主';
                        break;
                    case 'guest':
                        $tmp = '客';
                        break;
                    case 'six':
                        $tmp = '近6';
                        break;
                }
                ?>
                <tr>
                    <td>{{$tmp}}</td>
                    <td>{{$value['count']}}</td>
                    <td>{{$value['win']}}</td>
                    <td>{{$value['draw']}}</td>
                    <td>{{$value['lose']}}</td>
                    <td>{{$value['goal']}}</td>
                    <td>{{$value['fumble']}}</td>
                    <td>{{$value['goal'] - $value['fumble']}}</td>
                    <td>{{$value['score']}}</td>
                    <td>{{isset($value['rank'])?$value['rank']:'-'}}</td>
                    @if($value['count'] > 0)
                        <td>{{100*round($value['win']/$value['count'],2)}}%</td>
                    @else
                        <td>-</td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="part away">
        <p class="name">{{$match['aname']}}
            @if($rank['leagueRank']['aLeagueRank'] > 0)
                <span>【{{$rank['leagueRank']['aLeagueName']}}{{$rank['leagueRank']['aLeagueRank']}}】</span>
            @endif</p>
        <table>
            <thead>
            <tr>
                <th></th>
                <th>赛</th>
                <th>胜</th>
                <th>平</th>
                <th>负</th>
                <th>得</th>
                <th>失</th>
                <th>净</th>
                <th>积分</th>
                <th>排名</th>
                <th>胜率</th>
            </tr>
            </thead>
            <tbody>
            @foreach($rank['rank']['away'] as $key=>$value)
                <?php
                $tmp = '';
                switch ($key){
                    case 'all':
                        $tmp = '总';
                        break;
                    case 'home':
                        $tmp = '主';
                        break;
                    case 'guest':
                        $tmp = '客';
                        break;
                    case 'six':
                        $tmp = '近6';
                        break;
                }
                ?>
                <tr>
                    <td>{{$tmp}}</td>
                    <td>{{$value['count']}}</td>
                    <td>{{$value['win']}}</td>
                    <td>{{$value['draw']}}</td>
                    <td>{{$value['lose']}}</td>
                    <td>{{$value['goal']}}</td>
                    <td>{{$value['fumble']}}</td>
                    <td>{{$value['goal'] - $value['fumble']}}</td>
                    <td>{{$value['score']}}</td>
                    <td>{{isset($value['rank'])?$value['rank']:'-'}}</td>
                    @if($value['count'] > 0)
                        <td>{{100*round($value['win']/$value['count'],2)}}%</td>
                    @else
                        <td>-</td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>