@extends('phone.layout.live_base')
@section('live_tab')
    <div class="tab">
        <input type="radio" name="tab" id="Tab_Info" value="Info" checked><label for="Tab_Info">比赛信息</label>
        <input type="radio" name="tab" id="Tab_Event" value="Event"><label for="Tab_Event">比赛事件</label>
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
                @if((isset($item['h_p']) && $item['h_p'] != 0) || (isset($item['a_p']) && $item['a_p'] != 0))
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
    <div id="Event" style="display: none;">
        <div class="info">
            <p class="team"><img src="{{$match['hicon']}}" onerror="this.src='{{$cdn}}/pc/img/icon_teamDefault.png'"></p>
            <p class="team"><img src="{{$match['aicon']}}" onerror="this.src='{{$cdn}}/pc/img/icon_teamDefault.png'"></p>
        </div>
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
            <dt>比赛开始</dt>
        </dl>
    </div>
@endsection