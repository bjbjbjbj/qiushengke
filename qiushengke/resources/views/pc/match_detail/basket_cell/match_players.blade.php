@if(isset($players) && count($players) > 0)
    <div class="player">
        <p class="title">球员统计</p>
        <p class="name">{{$match['hname']}}</p>
        <table class="host">
            <colgroup>
                <col num="1">
                <col num="2" width="5%">
                <col num="3" width="5%">
                <col num="4" width="7.5%">
                <col num="5" width="7.5%">
                <col num="6" width="7.5%">
                <col num="7" width="5%">
                <col num="8" width="5%">
                <col num="9" width="5%">
                <col num="10" width="5%">
                <col num="11" width="5%">
                <col num="12" width="5%">
                <col num="13" width="5%">
                <col num="14" width="5%">
                <col num="15" width="5%">
            </colgroup>
            <thead>
            <tr>
                <th>球员</th>
                <th>位置</th>
                <th>得分</th>
                <th>投篮</th>
                <th>三分</th>
                <th>罚球</th>
                <th>篮板</th>
                <th>攻板</th>
                <th>防板</th>
                <th>助攻</th>
                <th>抢断</th>
                <th>盖帽</th>
                <th>失误</th>
                <th>犯规</th>
                <th>时间</th>
            </tr>
            </thead>
            <tbody>
            @foreach($players['home'] as $player)
                @if($player['type'] == 'player')
                    <tr>
                        <td>{{$player['name']}}</td>
                        <td>{{\App\Http\Controllers\PC\Match\MatchDetailController::getPlayerLocationCn($player['location'])}}</td>
                        <td>{{$player['pts']}}</td>
                        <td>{{$player['fg']}}</td>
                        <td>{{$player['3pt']}}</td>
                        <td>{{$player['ft']}}</td>
                        <td>{{$player['tot']}}</td>
                        <td>{{$player['off']}}</td>
                        <td>{{$player['def']}}</td>
                        <td>{{$player['ast']}}</td>
                        <td>{{$player['stl']}}</td>
                        <td>{{$player['blk']}}</td>
                        <td>{{$player['to']}}</td>
                        <td>{{$player['pf']}}</td>
                        <td>{{$player['min']}}'</td>
                    </tr>
                @elseif($player['type'] == 'total')
                    <tr class="total">
                        <td>总计</td>
                        <td></td>
                        <td>{{$player['pts']}}</td>
                        <td>{{$player['fg']}}</td>
                        <td>{{$player['3pt']}}</td>
                        <td>{{$player['ft']}}</td>
                        <td>{{$player['tot']}}</td>
                        <td>{{$player['off']}}</td>
                        <td>{{$player['def']}}</td>
                        <td>{{$player['ast']}}</td>
                        <td>{{$player['stl']}}</td>
                        <td>{{$player['blk']}}</td>
                        <td>{{$player['to']}}</td>
                        <td>{{$player['pf']}}</td>
                        <td>{{$player['min']}}'</td>
                    </tr>
                @elseif($player['type'] == 'ratio')
                    <tr class="total">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{$player['fg_p']}}%</td>
                        <td>{{$player['3pt_p']}}%</td>
                        <td>{{$player['ft_p']}}%</td>
                        <td></td>
                        <td colspan="3"></td>
                        <td colspan="3"></td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
        <p class="name">{{$match['aname']}}</p>
        <table class="away">
            <colgroup>
                <col num="1">
                <col num="2" width="5%">
                <col num="3" width="5%">
                <col num="4" width="7.5%">
                <col num="5" width="7.5%">
                <col num="6" width="7.5%">
                <col num="7" width="5%">
                <col num="8" width="5%">
                <col num="9" width="5%">
                <col num="10" width="5%">
                <col num="11" width="5%">
                <col num="12" width="5%">
                <col num="13" width="5%">
                <col num="14" width="5%">
                <col num="15" width="5%">
            </colgroup>
            <thead>
            <tr>
                <th>球员</th>
                <th>位置</th>
                <th>得分</th>
                <th>投篮</th>
                <th>三分</th>
                <th>罚球</th>
                <th>篮板</th>
                <th>攻板</th>
                <th>防板</th>
                <th>助攻</th>
                <th>抢断</th>
                <th>盖帽</th>
                <th>失误</th>
                <th>犯规</th>
                <th>时间</th>
            </tr>
            </thead>
            <tbody>
            @foreach($players['away'] as $player)
                @if($player['type'] == 'player')
                    <tr>
                        <td>{{$player['name']}}</td>
                        <td>{{\App\Http\Controllers\PC\Match\MatchDetailController::getPlayerLocationCn($player['location'])}}</td>
                        <td>{{$player['pts']}}</td>
                        <td>{{$player['fg']}}</td>
                        <td>{{$player['3pt']}}</td>
                        <td>{{$player['ft']}}</td>
                        <td>{{$player['tot']}}</td>
                        <td>{{$player['off']}}</td>
                        <td>{{$player['def']}}</td>
                        <td>{{$player['ast']}}</td>
                        <td>{{$player['stl']}}</td>
                        <td>{{$player['blk']}}</td>
                        <td>{{$player['to']}}</td>
                        <td>{{$player['pf']}}</td>
                        <td>{{$player['min']}}'</td>
                    </tr>
                @elseif($player['type'] == 'total')
                    <tr class="total">
                        <td>总计</td>
                        <td></td>
                        <td>{{$player['pts']}}</td>
                        <td>{{$player['fg']}}</td>
                        <td>{{$player['3pt']}}</td>
                        <td>{{$player['ft']}}</td>
                        <td>{{$player['tot']}}</td>
                        <td>{{$player['off']}}</td>
                        <td>{{$player['def']}}</td>
                        <td>{{$player['ast']}}</td>
                        <td>{{$player['stl']}}</td>
                        <td>{{$player['blk']}}</td>
                        <td>{{$player['to']}}</td>
                        <td>{{$player['pf']}}</td>
                        <td>{{$player['min']}}'</td>
                    </tr>
                @elseif($player['type'] == 'ratio')
                    <tr class="total">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{$player['fg_p']}}%</td>
                        <td>{{$player['3pt_p']}}%</td>
                        <td>{{$player['ft_p']}}%</td>
                        <td></td>
                        <td colspan="3"></td>
                        <td colspan="3"></td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
@endif