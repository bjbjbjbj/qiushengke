<?php
$schedule = $analyse['schedule'];
$rank = $analyse['rank'];
?>
@if(count($schedule['home']) > 0 || count($schedule['away']) > 0)
    <div class="future">
        <p class="title">未来赛程</p>
        <div class="part host">
            <p class="name">{{$currmatch['hname']}}
                @if($rank['leagueRank']['hLeagueRank'] > 0)
                    <span>【{{$rank['leagueRank']['hLeagueName']}}{{$rank['leagueRank']['hLeagueRank']}}】</span>
                @endif</p>
            <table>
                <colgroup>
                    <col num="1" width="16%">
                    <col num="2" width="18%">
                    <col num="3" width="14%">
                    <col num="4" width="10%">
                    <col num="5" width="32%">
                    <col num="6" width="10%">
                </colgroup>
                <thead>
                <tr>
                    <th>赛事</th>
                    <th>日期</th>
                    <th></th>
                    <th>对阵</th>
                    <th></th>
                    <th>相隔</th>
                </tr>
                </thead>
                <tbody>
                @foreach($schedule['home'] as $match)
                    <?php
                    $time = strtotime($match['time']);
                    $time = date('Y.m.d',$time);
                    $time = substr($time,2);
                            $isHome = $match['hid'] == $currmatch['hid'];
                    ?>
                    <tr>
                        <td><p class="line" style="background: #ea512d;"></p>{{$match['league']}}</td>
                        <td>{{$time}}</td>
                        <td class="green">{{$isHome?'主':'客'}}</td>
                        <td>VS</td>
                        <td>{{$isHome?$match['aname']:$match['hname']}}</td>
                        <td>{{$match['day']}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="part away">
            <p class="name">{{$currmatch['aname']}}
                @if($rank['leagueRank']['aLeagueRank'] > 0)
                    <span>【{{$rank['leagueRank']['aLeagueName']}}{{$rank['leagueRank']['aLeagueRank']}}】</span>
                @endif</p>
            <table>
                <colgroup>
                    <col num="1" width="16%">
                    <col num="2" width="18%">
                    <col num="3" width="14%">
                    <col num="4" width="10%">
                    <col num="5" width="32%">
                    <col num="6" width="10%">
                </colgroup>
                <thead>
                <tr>
                    <th>赛事</th>
                    <th>日期</th>
                    <th></th>
                    <th>对阵</th>
                    <th></th>
                    <th>相隔</th>
                </tr>
                </thead>
                <tbody>
                @foreach($schedule['away'] as $match)
                    <?php
                    $time = strtotime($match['time']);
                    $time = date('Y.m.d',$time);
                    $time = substr($time,2);
                    $isHome = $match['hid'] == $currmatch['aid'];
                    ?>
                    <tr>
                        <td><p class="line" style="background: #ea512d;"></p>{{$match['league']}}</td>
                        <td>{{$time}}</td>
                        <td class="green">{{$isHome?'主':'客'}}</td>
                        <td>VS</td>
                        <td>{{$isHome?$match['aname']:$match['hname']}}</td>
                        <td>{{$match['day']}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif