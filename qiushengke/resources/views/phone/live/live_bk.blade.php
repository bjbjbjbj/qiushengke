@extends('phone.layout.live_base')
@section('live_tab')
    <div class="tab">
        <input type="radio" name="tab" id="Tab_Info" value="Info" checked><label for="Tab_Info">比赛信息</label>
        <input type="radio" name="tab" id="Tab_Player" value="Player"><label for="Tab_Player">球员统计</label><!--篮球-->
    </div>
    <div id="Info">
        <div class="info">
            <div class="team">
                <div class="imgbox"><img src="{{$match['hicon']}}" onerror="this.src='{{$cdn}}/pc/img/icon_teamDefault.png'"></div>
                <p>{{$match['hname']}}</p>
            </div>
            @if($match['status'] == 0)
                <div class="score"><p>vs</p></div>
            @else
                <div class="score"><p>{{$match['hscore']}} - {{$match['ascore']}}</p><button onclick="clickHideScore(this)">隐藏比分</button></div>
            @endif
            <div class="team">
                <div class="imgbox"><img src="{{$match['aicon']}}" onerror="this.src='{{$cdn}}/pc/img/icon_teamDefault.png'"></div>
                <p>{{$match['aname']}}</p>
            </div>
        </div>
        <ul>
            @if(isset($tech))
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
                    <li>
                        <p class="val">{{$hname}}</p>
                        <p class="line"><span style="width: {{108 * $item['h_p']}}%;"></span></p>
                        <p class="item">{{$item['name']}}</p>
                        <p class="line"><span style="width: {{108 * $item['a_p']}}%;"></span></p>
                        <p class="val">{{$aname}}</p>
                    </li>
                @endif
            @endforeach
                @endif
        </ul>
    </div>
    @if(isset($players))
    <div id="Player" style="display: none;">
        <h2>{{$match['hname']}}</h2>
        @if(isset($players['home']))
        <div class="list">
            <dl>
                <dt>球员</dt>
                @foreach($players['home'] as $player)
                    @if($player['type'] == 'player')
                        <dd>{{$player['name']}}</dd>
                    @endif
                @endforeach
            </dl>
            <div class="item">
                <table>
                    <thead>
                    <tr>
                        <th>首发</th>
                        <th>得分</th>
                        <th>投篮</th>
                        <th>三分</th>
                        <th>罚球</th>
                        <th>篮板</th>
                        <th>助攻</th>
                        <th>犯规</th>
                        <th>抢断</th>
                        <th>失误</th>
                        <th>盖帽</th>
                        <th>时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($players['home'] as $player)
                        @if($player['type'] == 'player')
                            <tr>
                                <td>{{\App\Http\Controllers\PC\Match\MatchDetailController::getPlayerLocationCn($player['location'])}}</td>
                                <td>{{$player['pts']}}</td>
                                <td>{{str_replace("-","/",$player['fg'])}}</td>
                                <td>{{str_replace("-","/",$player['3pt'])}}</td>
                                <td>{{str_replace("-","/",$player['ft'])}}</td>
                                <td>{{$player['tot']}}</td>
                                <td>{{$player['ast']}}</td>
                                <td>{{$player['pf']}}</td>
                                <td>{{$player['stl']}}</td>
                                <td>{{$player['to']}}</td>
                                <td>{{$player['blk']}}</td>
                                <td>{{$player['min']}}</td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>

        </div>
            @else
            <div class="noList"></div>
        @endif
        <h2>{{$match['aname']}}</h2>
        @if(isset($players['away']))
        <div class="list">
            <dl>
                <dt>球员</dt>
                @foreach($players['away'] as $player)
                    @if($player['type'] == 'player')
                        <dd>{{$player['name']}}</dd>
                    @endif
                @endforeach
            </dl>
            <div class="item">
                <table>
                    <thead>
                    <tr>
                        <th>首发</th>
                        <th>得分</th>
                        <th>投篮</th>
                        <th>三分</th>
                        <th>罚球</th>
                        <th>篮板</th>
                        <th>助攻</th>
                        <th>犯规</th>
                        <th>抢断</th>
                        <th>失误</th>
                        <th>盖帽</th>
                        <th>时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($players['away'] as $player)
                        @if($player['type'] == 'player')
                            <tr>
                                <td>{{\App\Http\Controllers\PC\Match\MatchDetailController::getPlayerLocationCn($player['location'])}}</td>
                                <td>{{$player['pts']}}</td>
                                <td>{{str_replace("-","/",$player['fg'])}}</td>
                                <td>{{str_replace("-","/",$player['3pt'])}}</td>
                                <td>{{str_replace("-","/",$player['ft'])}}</td>
                                <td>{{$player['tot']}}</td>
                                <td>{{$player['ast']}}</td>
                                <td>{{$player['pf']}}</td>
                                <td>{{$player['stl']}}</td>
                                <td>{{$player['to']}}</td>
                                <td>{{$player['blk']}}</td>
                                <td>{{$player['min']}}</td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>

        </div>
        @else
            <div class="noList"></div>
            @endif
    </div>
    @endif
@endsection