<div id="Event" class="childNode" style="display: none;">
    <div class="score default">
        <div class="title">比分统计
            <button class="close"></button>
        </div>
        <table>
            <thead>
            <tr>
                <th>球队</th>
                <th>1st</th>
                <th>2nd</th>
                <th>3rd</th>
                <th>4th</th>
                @if((isset($match['h_ot']) && count($match['h_ot']) > 0)||(isset($match['a_ot']) && count($match['a_ot']) > 0))
                    @if(count($match['h_ot']) == 1)
                        <th>OT</th>
                    @else
                        @foreach($match['h_ot'] as $key=>$ot)
                            <th>OT{{$key+1}}</th>
                        @endforeach
                    @endif
                @endif
                <th>总分</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><img src="{{$match['hicon']}}" onerror="this.src='{{$cdn}}/pc/img/icon_teamDefault.png'"></td>
                <td
                        @if($match['status'] == 1)
                        class="now"
                        @endif
                >{{$match['hscore_1st'] or '/'}}</td>
                <td
                        @if($match['status'] == 2)
                        class="now"
                        @endif
                >{{$match['hscore_2nd'] or '/'}}</td>
                <td
                        @if($match['status'] == 3)
                        class="now"
                        @endif
                >{{$match['hscore_3rd'] or '/'}}</td>
                <td
                        @if($match['status'] == 4)
                        class="now"
                        @endif
                >{{$match['hscore_4th'] or '/'}}</td>
                @if(isset($match['h_ot']) && count($match['h_ot']) > 0)
                    @foreach($match['h_ot'] as $ot)
                        <td>{{$ot or '/'}}</td>
                    @endforeach
                @endif
                <td>{{$match['hscore'] or '/'}}</td>
            </tr>
            <tr>
                <td><img src="{{$match['aicon']}}" onerror="this.src='{{$cdn}}/pc/img/icon_teamDefault.png'"></td>
                <td
                        @if($match['status'] == 1)
                        class="now"
                        @endif
                >{{$match['ascore_1st'] or '/'}}</td>
                <td
                        @if($match['status'] == 2)
                        class="now"
                        @endif
                >{{$match['ascore_2nd'] or '/'}}</td>
                <td
                        @if($match['status'] == 3)
                        class="now"
                        @endif
                >{{$match['ascore_3rd'] or '/'}}</td>
                <td
                        @if($match['status'] == 4)
                        class="now"
                        @endif
                >{{$match['ascore_4th'] or '/'}}</td>
                @if(isset($match['a_ot']) && count($match['a_ot']) > 0)
                    @foreach($match['a_ot'] as $ot)
                        <td>{{$ot or '/'}}</td>
                    @endforeach
                @endif
                <td>{{$match['ascore'] or '/'}}</td>
            </tr>
            </tbody>
        </table>
    </div>
    @if(isset($tech) && count($tech) > 0)
        <div class="technology default">
            <div class="title">技术统计
                <button class="close"></button>
            </div>
            <ul>
                <li>
                    <dl class="team">
                        <dd class="host"><p class="img"><img src="{{$match['hicon']}}" onerror="this.src='{{$cdn}}/pc/img/icon_teamDefault.png'">
                            </p></dd>
                        <dt>VS</dt>
                        <dd class="away"><p class="img"><img src="{{$match['aicon']}}" onerror="this.src='{{$cdn}}/pc/img/icon_teamDefault.png'">
                            </p></dd>
                    </dl>

                    @foreach($tech as $item)
                        @if($item['h_p'] != 0 || $item['a_p'] != 0)
                            <?php
                            $hname = empty($item['h']) ? 0 : $item['h'];
                            if (str_contains($hname, "(")) {
                                $hname = explode("(",$hname)[0];
                            }
                            $aname = empty($item['a']) ? 0 : $item['a'];
                            if (str_contains($aname, "(")) {
                                $aname = explode("(",$aname)[0];
                            }
                            ?>
                            <dl>
                                <dd class="host"><p>{{$hname}}</p><span
                                            style="width: {{108 * $item['h_p']}}px;"></span></dd><!--span的值为108*百分比-->
                                <dt>{{$item['name']}}</dt>
                                <dd class="away"><p>{{$aname}}</p><span
                                            style="width: {{108 * $item['a_p']}}px;"></span></dd>
                            </dl>
                        @endif
                    @endforeach
                </li>
            </ul>
        </div>
    @endif
</div>
@if(isset($players) && count($players) > 0)
    <div id="Player" class="childNode default" style="display: ;">
        <div class="title">球员数据 - {{$match['hname']}}</div>
        <div class="score default">
            <table>
                <thead>
                <tr>
                    <th>球员</th>
                    <th>位置</th>
                    <th>上场</th>
                    <th>得分</th>
                    <th>投篮</th>
                    <th>3分</th>
                    <th>罚球</th>
                    <th>助攻</th>
                    <th>篮板</th>
                    <th>攻板</th>
                    <th>防板</th>
                    <th>抢断</th>
                    <th>盖帽</th>
                    <th>失误</th>
                    <th>犯规</th>
                </tr>
                </thead>
                <tbody>
                @foreach($players['home'] as $player)
                    @if($player['type'] == 'player')
                        <tr>
                            <td>{{$player['name']}}</td>
                            <td>{{\App\Http\Controllers\PC\Match\MatchDetailController::getPlayerLocationCn($player['location'])}}</td>
                            <td>{{$player['min']}}</td>
                            <td>{{$player['pts']}}</td>
                            <td>{{str_replace("-","/",$player['fg'])}}</td>
                            <td>{{str_replace("-","/",$player['3pt'])}}</td>
                            <td>{{str_replace("-","/",$player['ft'])}}</td>
                            <td>{{$player['ast']}}</td>
                            <td>{{$player['tot']}}</td>
                            <td>{{$player['off']}}</td>
                            <td>{{$player['def']}}</td>
                            <td>{{$player['stl']}}</td>
                            <td>{{$player['blk']}}</td>
                            <td>{{$player['to']}}</td>
                            <td>{{$player['pf']}}</td>
                        </tr>
                    @elseif($player['type'] == 'total')
                        <tr>
                            <td>总计</td>
                            <td></td>
                            <td>{{$player['min']}}</td>
                            <td>{{$player['pts']}}</td>
                            <td>{{str_replace("-","/",$player['fg'])}}</td>
                            <td>{{str_replace("-","/",$player['3pt'])}}</td>
                            <td>{{str_replace("-","/",$player['ft'])}}</td>
                            <td>{{$player['ast']}}</td>
                            <td>{{$player['tot']}}</td>
                            <td>{{$player['off']}}</td>
                            <td>{{$player['def']}}</td>
                            <td>{{$player['stl']}}</td>
                            <td>{{$player['blk']}}</td>
                            <td>{{$player['to']}}</td>
                            <td>{{$player['pf']}}</td>
                        </tr>
                    @elseif($player['type'] == 'ratio')
                        <tr>
                            <td>命中率</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{$player['fg_p']}}%</td>
                            <td>{{$player['3pt_p']}}%</td>
                            <td>{{$player['ft_p']}}%</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="title">球员数据 - {{$match['aname']}}<button class="right"></button><button class="left"></button></div>
        <div class="score default">
            <table>
                <thead>
                <tr>
                    <th>球员</th>
                    <th>位置</th>
                    <th>上场</th>
                    <th>得分</th>
                    <th>投篮</th>
                    <th>3分</th>
                    <th>罚球</th>
                    <th>助攻</th>
                    <th>篮板</th>
                    <th>攻板</th>
                    <th>防板</th>
                    <th>抢断</th>
                    <th>盖帽</th>
                    <th>失误</th>
                    <th>犯规</th>
                </tr>
                </thead>
                <tbody>
                @foreach($players['away'] as $player)
                    @if($player['type'] == 'player')
                        <tr>
                            <td>{{$player['name']}}</td>
                            <td>{{\App\Http\Controllers\PC\Match\MatchDetailController::getPlayerLocationCn($player['location'])}}</td>
                            <td>{{$player['min']}}</td>
                            <td>{{$player['pts']}}</td>
                            <td>{{str_replace("-","/",$player['fg'])}}</td>
                            <td>{{str_replace("-","/",$player['3pt'])}}</td>
                            <td>{{str_replace("-","/",$player['ft'])}}</td>
                            <td>{{$player['ast']}}</td>
                            <td>{{$player['tot']}}</td>
                            <td>{{$player['off']}}</td>
                            <td>{{$player['def']}}</td>
                            <td>{{$player['stl']}}</td>
                            <td>{{$player['blk']}}</td>
                            <td>{{$player['to']}}</td>
                            <td>{{$player['pf']}}</td>
                        </tr>
                    @elseif($player['type'] == 'total')
                        <tr>
                            <td>总计</td>
                            <td></td>
                            <td>{{$player['min']}}</td>
                            <td>{{$player['pts']}}</td>
                            <td>{{str_replace("-","/",$player['fg'])}}</td>
                            <td>{{str_replace("-","/",$player['3pt'])}}</td>
                            <td>{{str_replace("-","/",$player['ft'])}}</td>
                            <td>{{$player['ast']}}</td>
                            <td>{{$player['tot']}}</td>
                            <td>{{$player['off']}}</td>
                            <td>{{$player['def']}}</td>
                            <td>{{$player['stl']}}</td>
                            <td>{{$player['blk']}}</td>
                            <td>{{$player['to']}}</td>
                            <td>{{$player['pf']}}</td>
                        </tr>
                    @elseif($player['type'] == 'ratio')
                        <tr>
                            <td>命中率</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{$player['fg_p']}}%</td>
                            <td>{{$player['3pt_p']}}%</td>
                            <td>{{$player['ft_p']}}%</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="bottom">
        <div class="btn">
            <input type="radio" name="Match" id="Match_Player" value="Player" checked>
            <label for="Match_Player">球员数据</label>
            <input type="radio" name="Match" id="Match_Event" value="Event">
            <label for="Match_Event">比分统计</label>
        </div>
    </div>
@endif