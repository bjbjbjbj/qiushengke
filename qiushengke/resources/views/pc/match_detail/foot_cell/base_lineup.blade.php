@if((isset($lineup['home']) || isset($lineup['away'])) && (count($lineup['home']['first']) > 0 || count($lineup['away']['first']) > 0))
    <div class="first">
        <div class="part host">
            <p class="name">{{$match['hname']}}
                @if($rank['leagueRank']['hLeagueRank'] > 0)
                    <span>[{{$rank['leagueRank']['hLeagueName']}}{{$rank['leagueRank']['hLeagueRank']}}]</span>
                @endif
            </p>
            <div class="percent">
                @if(isset($lineup['h_lineup_per']))
                    <p>本场比赛有<b>{{number_format($lineup['h_lineup_per']*0.01*11,0)}}</b>名主力首发</p>
                    <div class="line"><span style="width: {{round($lineup['h_lineup_per'],2)}}%;"></span><p class="num">{{round($lineup['h_lineup_per'],2)}}%</p></div>
                @endif
            </div>
            <table>
                <thead>
                <tr>
                    <th>首发</th>
                    <th>后备</th>
                </tr>
                </thead>
                <tbody>
                @for($i = 0 ; $i < max(count($lineup['home']['back']),count($lineup['home']['first']));$i++)
                    <?php
                    $home = count($lineup['home']['first']) > $i ? $lineup['home']['first'][$i]:null;
                    $back = count($lineup['home']['back']) > $i ? $lineup['home']['back'][$i]:null;
                    ?>
                    <tr>
                        @if(!is_null($home))
                            <td><p>{{$home['num']}}</p>{{$home['name'] . (in_array($home['num'], $lineup['home']['h_first']) ? '[主]' : '')}}</td>
                        @else
                            <td></td>
                        @endif
                        @if(!is_null($back))
                            <td><p>{{$back['num']}}</p>{{$back['name']}}</td>
                        @else
                            <td></td>
                        @endif
                    </tr>
                @endfor
                </tbody>
            </table>
        </div>
        <div class="part away">
            <p class="name">
                @if($rank['leagueRank']['aLeagueRank'] > 0)
                    <span>[{{$rank['leagueRank']['aLeagueName']}}{{$rank['leagueRank']['aLeagueRank']}}]</span>
                @endif
                {{$match['aname']}}</p>
            <div class="percent">
                @if(isset($lineup['a_lineup_per']))
                    <p>本场比赛有<b>{{number_format($lineup['a_lineup_per']*0.01*11,0)}}</b>名主力首发</p>
                    <div class="line"><span style="width: {{round($lineup['a_lineup_per'],2)}}%;"></span><p class="num">{{round($lineup['a_lineup_per'],2)}}%</p></div>
                @endif
            </div>
            <table>
                <thead>
                <tr>
                    <th>首发</th>
                    <th>后备</th>
                </tr>
                </thead>
                <tbody>
                @for($i = 0 ; $i < max(count($lineup['away']['back']),count($lineup['away']['first']));$i++)
                    <?php
                    $home = count($lineup['away']['first']) > $i ? $lineup['away']['first'][$i]:null;
                    $back = count($lineup['away']['back']) > $i ? $lineup['away']['back'][$i]:null;
                    ?>
                    <tr>
                        @if(!is_null($home))
                            <td><p>{{$home['num']}}</p>{{$home['name'] . (in_array($home['num'], $lineup['away']['h_first']) ? '[主]' : '')}}</td>
                        @else
                            <td></td>
                        @endif
                        @if(!is_null($back))
                            <td><p>{{$back['num']}}</p>{{$back['name']}}</td>
                        @else
                            <td></td>
                        @endif
                    </tr>
                @endfor
                </tbody>
            </table>
        </div>
        <div class="noList"></div>
    </div>
@endif