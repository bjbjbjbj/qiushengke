@extends('pc.layout.live_base')

@section('live_data')
    <?php
    $hicon = strlen($match['hicon']) > 0 ? $match['hicon'] : '/pc/img/icon_teamDefault.png';
    $aicon = strlen($match['aicon']) > 0 ? $match['aicon'] : '/pc/img/icon_teamDefault.png';
    $isShowScore = $match['status'] > 0 || $match['status'] == -1;
    ?>
    <div class="data">
        <dl>
            <dt>VS</dt>
            <dd class="host">
                @if($isShowScore)
                    @if(isset($match['h_ot']))
                        @foreach($match['h_ot'] as $key=>$ot)
                            <p class="item bk on">{{$ot}}</p>
                        @endforeach
                    @endif
                        <p class="item bk @if(!isset($match['h_ot'])) on @endif">{{isset($match['hscore_4th']) ? $match['hscore_4th'] : "-"}}</p>
                        <p class="item bk @if(!isset($match['hscore_4th'])) on @endif">{{isset($match['hscore_3rd']) ? $match['hscore_3rd'] : "-"}}</p>
                        <p class="item bk @if(!isset($match['hscore_3rd'])) on @endif">{{isset($match['hscore_2nd']) ? $match['hscore_2nd'] : "-"}}</p>
                    <p class="item bk @if(!isset($match['hscore_2nd'])) on @endif">{{isset($match['hscore_1st']) ? $match['hscore_1st'] : "-"}}</p>
                @else
                    <p class="item bk">-</p>
                    <p class="item bk">-</p>
                    <p class="item bk">-</p>
                    <p class="item bk">-</p>
                @endif
                <p class="team"><img src="{{$hicon}}">{{$match['hname']}}</p>
            </dd>
            <dd class="away">
                @if($isShowScore)
                    <p class="item bk @if(!isset($match['hscore_2nd'])) on @endif">{{isset($match['ascore_1st']) ? $match['ascore_1st'] : "-"}}</p>
                    <p class="item bk @if(!isset($match['hscore_3rd'])) on @endif">{{isset($match['ascore_2nd']) ? $match['ascore_2nd'] : "-"}}</p>
                    <p class="item bk @if(!isset($match['hscore_4th'])) on @endif">{{isset($match['ascore_3rd']) ? $match['ascore_3rd'] : "-"}}</p>
                    <p class="item bk @if(!isset($match['h_ot'])) on @endif">{{isset($match['ascore_4th']) ? $match['ascore_4th'] : "-"}}</p>
                    @if(isset($match['a_ot']))
                        @foreach($match['a_ot'] as $key=>$ot)
                            <p class="item bk on">{{$ot}}</p>
                        @endforeach
                    @endif
                @else
                    <p class="item bk">-</p>
                    <p class="item bk">-</p>
                    <p class="item bk">-</p>
                    <p class="item bk">-</p>
                @endif
                <p class="team"><img src="{{$aicon}}">{{$match['aname']}}</p>
            </dd>
        </dl>
        <ul>
            <button class="open"></button>
            @foreach($tech as $t)
                @if((strlen($t['name']) < 10) && ($t['h'] > 0 || $t['a'] > 0))
                    <li>
                        <p class="host">
                            <b>{{$t['h']}}</b>
                            <span><em></em></span>
                        </p>
                        <p class="item">{{$t['name']}}</p>
                        <p class="away">
                            <b>{{$t['a']}}</b>
                            <span><em></em></span>
                        </p>
                    </li>
            @endif
        @endforeach
        <!--li的数量必须是3的倍数，不够使用空li-->
        </ul>
    </div>
@endsection

@section('live_content')
    <div id="Score">
        <p class="title">比分统计</p>
        <div class="con">
            <table>
                <thead>
                <tr>
                    <th></th>
                    <th>第一节</th>
                    <th>第二节</th>
                    <th>第三节</th>
                    <th>第四节</th>
                    @if(isset($match['h_ot']))
                        @foreach($match['h_ot'] as $key=>$ot)
                            <th>加时{{$key}}</th>
                        @endforeach
                    @endif
                    <th>总分</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{$match['hname']}}</td>
                    @if($isShowScore)
                        <td>{{isset($match['hscore_1st']) ? $match['hscore_1st'] : "-"}}</td>
                        <td>{{isset($match['hscore_2nd']) ? $match['hscore_2nd'] : "-"}}</td>
                        <td>{{isset($match['hscore_3rd']) ? $match['hscore_3rd'] : "-"}}</td>
                        <td>{{isset($match['hscore_4th']) ? $match['hscore_4th'] : "-"}}</td>
                        @if(isset($match['h_ot']))
                            @foreach($match['h_ot'] as $key=>$ot)
                                <td>{{$ot}}</td>
                            @endforeach
                        @endif
                        <td>{{isset($match['hscore']) ? $match['hscore'] : "-"}}</td>
                    @else
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    @endif
                </tr>
                <tr>
                    <td>{{$match['aname']}}</td>
                    @if($isShowScore)
                        <td>{{isset($match['ascore_1st']) ? $match['ascore_1st'] : "-"}}</td>
                        <td>{{isset($match['ascore_2nd']) ? $match['ascore_2nd'] : "-"}}</td>
                        <td>{{isset($match['ascore_3rd']) ? $match['ascore_3rd'] : "-"}}</td>
                        <td>{{isset($match['ascore_4th']) ? $match['ascore_4th'] : "-"}}</td>
                        @if(isset($match['a_ot']))
                            @foreach($match['a_ot'] as $key=>$ot)
                                <td>{{$ot}}</td>
                            @endforeach
                        @endif
                        <td>{{isset($match['ascore']) ? $match['ascore'] : "-"}}</td>
                    @else
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    @endif
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div id="BKPlayer">
        <p class="title">球员数据</p>
        <div class="con">
            <div class="tab">
                <p class="host"><button class="on">{{$match['hname']}}</button></p>
                <p class="away"><button>{{$match['aname']}}</button></p>
            </div>
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
            <table class="away" style="display: none">
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
    </div>
@endsection

@section('navContent')
    <div class="home"><p class="abox"><a href="index.html"><img src="/pc/img/logo_image_n.png"></a></p></div>
    <div class="Column">
        <a href="/match/foot/immediate.html">足球</a>
        <a class="on" href="/match/basket/immediate_t.html">篮球</a>
        <a href="">主播</a>
        <a href="">手机APP</a>
    </div>
@endsection