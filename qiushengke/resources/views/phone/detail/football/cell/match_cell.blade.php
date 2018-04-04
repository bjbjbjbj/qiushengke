<div id="Match" class="content" style="display: none;">
    @if(isset($lineup) && count($lineup) > 0)
    <div id="First" class="childNode" style="display: ;">
        @if(isset($lineup['home']))
        @component('phone.detail.football.cell.match_lineup_cell', ['class'=>'host', 'lineup'=>$lineup['home'] ]) @endcomponent
        @endif
        @if($lineup['away'])
        @component('phone.detail.football.cell.match_lineup_cell', ['class'=>'away', 'lineup'=>$lineup['away'] ]) @endcomponent
        @endif
    </div>
        @else
        <div id="First" class="childNode" style="display: ;">
        <div class="nolist"></div>
        </div>
    @endif
    <div id="Event" class="childNode" style="display: none;">
        @if(isset($tech))
            @if(isset($tech['tech']) && count($tech['tech']) > 0)
            <div class="technology default">
                <div class="title">技术统计<button class="close"></button></div>
                <ul>
                    <li>
                        <dl class="team">
                            <dd class="host"><p class="img"><img src="{{$match['hicon']}}" onerror="this.src='{{$cdn}}/pc/img/icon_teamDefault.png'"></p></dd>
                            <dt>VS</dt>
                            <dd class="away"><p class="img"><img src="{{$match['aicon']}}" onerror="this.src='{{$cdn}}/pc/img/icon_teamDefault.png'"></p></dd>
                        </dl>
                        @foreach($tech['tech'] as $t)
                            <dl>
                                <dd class="host"><p>{{$t['h']}}</p><span style="width: {{is_numeric($t['h_p']) ? (108 * $t['h_p']) : 0}}px;"></span></dd><!--span的值为108*百分比-->
                                <dt>{{$t['name']}}</dt>
                                <dd class="away"><p>{{$t['a']}}</p><span style="width: {{is_numeric($t['a_p']) ? (108 * $t['a_p']) : 0}}px;"></span></dd>
                            </dl>
                        @endforeach
                    </li>
                </ul>
            </div>
            @endif
        <div class="event default">
            <div class="title">比赛事件<button class="close"></button></div>
            <dl style="margin-top: 100px;">
                <dt class="end">比赛结束</dt>
                @if(isset($tech['event']) && isset($tech['event']['events']) )
                @for($eIndex = count($tech['event']['events']) - 1; $eIndex > 0; $eIndex--)
                    <?php $event = $tech['event']['events'][$eIndex]; $kind = $event['kind']; $eClass = $event['is_home'] ? 'host' : 'away' ?>
                    @if($kind == 11)
                        <dd class="{{$eClass}}">
                            <p class="minute">{{$event['happen_time']}}<span>'</span></p>
                            <ul>
                                <li><img src="{{$cdn}}/phone/img/icon_video_up.png">{{$event['player_name_j']}}换上</li>
                                <li><img src="{{$cdn}}/phone/img/icon_video_down.png">{{!empty($event['player_name_j2']) ? $event['player_name_j2'] . '换下' : ''}}</li>
                            </ul>
                        </dd>
                    @elseif ($kind == 2)
                        <dd class="{{$eClass}}">
                            <p class="minute">{{$event['happen_time']}}<span>'</span></p>
                            <ul>
                                <li><img src="{{$cdn}}/phone/img/icon_video_red.png">{{$event['player_name_j']}}</li>
                            </ul>
                        </dd>
                    @elseif ($kind == 3)
                        <dd class="{{$eClass}}">
                            <p class="minute">42<span>'</span></p>
                            <ul>
                                <li><img src="{{$cdn}}/phone/img/icon_video_yellow.png">{{$event['player_name_j']}}</li>
                            </ul>
                        </dd>
                    @elseif ($kind == 9)
                        <dd class="{{$eClass}}">
                            <p class="minute">{{$event['happen_time']}}<span>'</span></p>
                            <ul>
                                <li><img src="{{$cdn}}/phone/img/icon_video_red.png">{{$event['player_name_j']}}（两黄一红）</li>
                            </ul>
                        </dd>
                    @elseif ($kind == 1)
                        <dd class="{{$eClass}}">
                            <p class="minute">{{$event['happen_time']}}<span>'</span></p>
                            <ul>
                                <li><img src="{{$cdn}}/phone/img/icon_video_goal.png">{{$event['player_name_j']}}</li>
                            </ul>
                        </dd>
                    @elseif ($kind == 7)
                        <dd class="{{$eClass}}">
                            <p class="minute">{{$event['happen_time']}}<span>'</span></p>
                            <ul>
                                <li><img src="{{$cdn}}/phone/img/icon_video_goal.png">{{$event['player_name_j']}}（点球）</li>
                            </ul>
                        </dd>
                    @elseif ($kind == 8)
                        <dd class="{{$eClass}}">
                            <p class="minute">{{$event['happen_time']}}<span>'</span></p>
                            <ul>
                                <li><img src="{{$cdn}}/phone/img/icon_video_own.png">{{$event['player_name_j']}}（乌龙）</li>
                            </ul>
                        </dd>
                    @endif
                @endfor
                @endif
                {{--<dd class="host">--}}
                    {{--<p class="minute">22<span>'</span></p>--}}
                    {{--<ul>--}}
                        {{--<li><img src="img/icon_video_corner.png">加利亚角球</li>--}}
                    {{--</ul>--}}
                {{--</dd>--}}
                <dt>比赛开始</dt>
            </dl>
        </div>
        @endif
    </div>
    <div class="bottom">
        <div class="btn">
            <input type="radio" name="Match" id="Match_First" value="First" checked>
            <label for="Match_First">首发对比</label>
            <input type="radio" name="Match" id="Match_Event" value="Event">
            <label for="Match_Event">比赛事件</label>
        </div>
    </div>
</div>