@extends('pc.layout.live_base')
@section('live_data')
    <?php
    $hicon = strlen($match['hicon']) > 0 ? $match['hicon'] : '/pc/img/icon_teamDefault.png';
    $aicon = strlen($match['aicon']) > 0 ? $match['aicon'] : '/pc/img/icon_teamDefault.png';
    ?>
    <div class="data">
        <dl>
            <dt>VS</dt>
            <dd class="host">
                <p class="item corner">{{isset($match['h_corner'])?$match['h_corner']:0}}</p>
                <p class="item yellow">{{isset($match['h_yellow'])?$match['h_yellow']:0}}</p>
                <p class="item red">{{isset($match['h_red'])?$match['h_red']:0}}</p>
                <p class="team"><img src="{{$hicon}}">{{$match['hname']}}</p>
            </dd>
            <dd class="away">
                <p class="item corner">{{isset($match['a_corner'])?$match['a_corner']:0}}</p>
                <p class="item yellow">{{isset($match['a_yellow'])?$match['a_yellow']:0}}</p>
                <p class="item red">{{isset($match['a_red'])?$match['a_red']:0}}</p>
                <p class="team"><img src="{{$aicon}}">{{$match['aname']}}</p>
            </dd>
        </dl>
        <ul>
            <button class="open"></button>
            @foreach($tech['tech'] as $item)
                <?php
                $hname = $item['h'];
                $aname = $item['a'];
                if (str_contains($hname, "(")) {
                    $hname = str_replace(')','',explode("(", $hname)[1]);
                }
                if (str_contains($aname, "(")) {
                    $aname = str_replace(')','',explode("(", $aname)[1]);
                }
                ?>
                <li>
                    {{$item['name']}}
                    <div class="line host">
                        <p>{{$hname}}</p>
                        <span style="width: {{$item['h_p']*100}}%;"></span>
                    </div>
                    <div class="line away">
                        <p>{{$aname}}</p>
                        <span style="width: {{$item['a_p']*100}}%;"></span>
                    </div>
                </li>
        @endforeach
        <!--li的数量必须是3的倍数，不够使用空li-->
        </ul>
    </div>
@endsection
@section('live_content')
    <div id="Event">
        <?php
        $events = $tech['event'];
        ?>
        <?php
        //获取足球比赛的即时时间
        $time = isset($match['timehalf'])? $match['timehalf'] : $match['time'];
        $timehalf = $match['timehalf'];
        $now = time();
        $status = $match['status'];
        $matchTime = 20;
        if ($status == 4){
            $matchTime = 90;
        }
        else if ($status == 2) {
            $matchTime = 45;
        }elseif ($status == 1) {
            $diff = ($now - $time) > 0 ? ($now - $time) : 0;
            $matchTime = (floor(($diff) % 86400 / 60)) > 45 ? 45 : floor(($diff) % 86400 / 60);
        } elseif ($status == 3) {
            $diff = ($now - $timehalf) > 0 ? ($now - $timehalf) : 0;
            $matchTime = (floor(($diff) % 86400 / 60)) > 45 ? 90 : (floor(($diff) % 86400 / 60) + 45);
        }

        $lastTime = $events['last_event_time'];
        if ($lastTime > 90 || $matchTime > 90){
            $width = round(100/120,2);
        }
        else{
            $width = round(100/90,2);
        }
        if ($status == -1){
            if (is_null($matchTime)){
                $matchTime = $lastTime > 90 ? 120:90;
            }
            else{
                $matchTime = 90;
            }
        }

        $hicon = strlen($match['hicon']) > 0 ? $match['hicon'] : '/pc/img/icon_teamDefault.png';
        $aicon = strlen($match['aicon']) > 0 ? $match['aicon'] : '/pc/img/icon_teamDefault.png';
        ?>
        <p class="title">比赛事件</p>
            <div class="con">
                <p class="start">0'</p>
                <p class="end">{{($lastTime > 90 || $matchTime > 90)?'120':'90'}}'</p>
                <img class="host" src="{{$hicon}}">
                <img class="away" src="{{$aicon}}">
                <dl class="line"><!--当前是90分钟样式，120分钟要重新计算宽度和位置，每15分钟一个节点是一定会有的，其他分钟有事件才添加就行-->
                    @if($status == -1)
                        <dt style="width: 100%"><p>{{$matchTime}}'</p></dt>
                    @else
                        <dt style="width: {{($lastTime > 90 ?(100/120):(100/90))*($matchTime - 1) + $width/2}}%"><p>{{$matchTime}}'</p></dt>
                    @endif
                    <dd class="after" minute="15" style="left: {{($lastTime > 90 ?(100/120):(100/90))*(15-1)}}%; width: {{$width}}%;">
                        <p class="minute">15'</p>
                    </dd>
                    <dd class="after" minute="30" style="left: {{($lastTime > 90 ?(100/120):(100/90))*(30-1)}}%; width: {{$width}}%;">
                        <p class="minute">30'</p>
                    </dd>
                    <dd class="after" minute="45" style="left: {{($lastTime > 90 ?(100/120):(100/90))*(45-1)}}%; width: {{$width}}%;">
                        <p class="minute">45'</p>
                    </dd>
                    <dd minute="60" style="left: {{($lastTime > 90 ?(100/120):(100/90))*(60-1)}}%; width: {{$width}}%;">
                        <p class="minute">60'</p>
                    </dd>
                    <dd minute="75" style="left:{{($lastTime > 90 ?(100/120):(100/90))*(75-1)}}%; width: {{$width}}%;">
                        <p class="minute">75'</p>
                    </dd>
                    @if($lastTime > 90)
                        <dd minute="90" style="left: {{(100/120)*(90-1)}}%; width: {{$width}}%;">
                            <p class="minute">90'</p>
                        </dd>
                        <dd minute="105" style="left: {{(100/120)*(105-1)}}%; width: {{$width}}%;">
                            <p class="minute">105'</p>
                        </dd>
                    @endif

                    <?php
                    //处理事件数据格式
                    $host_events = [];//格式：[time=>[], time=>[], ...];
                    $away_events = [];//格式：[time=>[], time=>[], ...];

                    $host_temp_time = 0;
                    $away_temp_time = 0;
                    $last_event_time = 0;
                    if (isset($events['events']) && count($events['events']) > 0) {
                        foreach ($events['events'] as $event) {
                            if ($event['is_home'] == 1) {//主队事件
                                if ($event['happen_time'] != $host_temp_time) {
                                    $host_temp_time = $event['happen_time'];
                                }
                                if (!isset($host_events[$host_temp_time])) {
                                    $host_events[$host_temp_time] = [$event];
                                } else {
                                    $host_events[$host_temp_time][] = $event;
                                }
                            } else {//客队事件
                                if ($event['happen_time'] != $away_temp_time) {
                                    $away_temp_time = $event['happen_time'];
                                }
                                if (!isset($away_events[$away_temp_time])) {
                                    $away_events[$away_temp_time] = [$event];
                                } else {
                                    $away_events[$away_temp_time][] = $event;
                                }
                            }
                        }
                    }
                    ?>

                    @foreach($away_events as $time=>$events)
                        <dd minute="{{$time}}" style="left: {{($lastTime > 90 ?(100/120):(100/90))*($time-1)}}%; width: {{$width}}%;">
                            <ul class="away">
                                @foreach($events as $event)
                                    <?php
                                    $icon = '';
                                    $cn = "";
                                    switch ($event['kind']) {
                                        case 1:
                                            $cn = $event['player_name_j'] . "（进球）";
                                            $icon = "/pc/img/icon_goal.png";
                                            break;
                                        case 7:
                                            $cn = $event['player_name_j'] . "（点球）";
                                            $icon = "/pc/img/icon_goal.png";
                                            break;
                                        case 8:
                                            $cn = $event['player_name_j'] . "（乌龙球）";
                                            $icon = "/pc/img/icon_goal.png";
                                            break;
                                        case 2:
                                            $cn = $event['player_name_j'] . "（红牌）";
                                            $icon = "/pc/img/icon_red.png";
                                            break;
                                        case 9:
                                            $cn = $event['player_name_j'] . "（两黄一红）";
                                            $icon = "/pc/img/icon_red.png";
                                            break;
                                        case 3:
                                            $cn = $event['player_name_j'] . "（黄牌）";
                                            $icon = "/pc/img/icon_yellow.png";
                                            break;
                                        case 11:
                                            $cn = $event['player_name_j'] . "（换上） " . $event['player_name_j2'] . "（换下）";
                                            $icon = "/pc/img/icon_exchange.png";
                                            break;
                                    }
                                    ?>
                                    <li>
                                        <img src="{{$icon}}">
                                        <p><span><i>{{$time}}'</i>{{$cn}}</span></p>
                                    </li>
                                @endforeach
                            </ul>
                        </dd>
                    @endforeach

                    @foreach($host_events as $time=>$events)
                        <dd minute="{{$time}}" style="left: {{($lastTime > 90 ?(100/120):(100/90))*($time-1)}}%; width: {{$width}}%;">
                            <ul class="host">
                                @foreach($events as $event)
                                    <?php
                                    $icon = '';
                                    $cn = "";
                                    switch ($event['kind']) {
                                        case 1:
                                            $cn = $event['player_name_j'] . "（进球）";
                                            $icon = "/pc/img/icon_goal.png";
                                            break;
                                        case 7:
                                            $cn = $event['player_name_j'] . "（点球）";
                                            $icon = "/pc/img/icon_goal.png";
                                            break;
                                        case 8:
                                            $cn = $event['player_name_j'] . "（乌龙球）";
                                            $icon = "/pc/img/icon_goal.png";
                                            break;
                                        case 2:
                                            $cn = $event['player_name_j'] . "（红牌）";
                                            $icon = "/pc/img/icon_red.png";
                                            break;
                                        case 9:
                                            $cn = $event['player_name_j'] . "（两黄一红）";
                                            $icon = "/pc/img/icon_red.png";
                                            break;
                                        case 3:
                                            $cn = $event['player_name_j'] . "（黄牌）";
                                            $icon = "/pc/img/icon_yellow.png";
                                            break;
                                        case 11:
                                            $cn = $event['player_name_j'] . "（换上） " . $event['player_name_j2'] . "（换下）";
                                            $icon = "/pc/img/icon_exchange.png";
                                            break;
                                    }
                                    ?>
                                    <li>
                                        <img src="{{$icon}}">
                                        <p><span><i>{{$time}}'</i>{{$cn}}</span></p>
                                    </li>
                                @endforeach
                            </ul>
                        </dd>
                    @endforeach
                </dl>
            </div>
    </div>
@endsection

@section('live_js')
    <script type="text/javascript">
        //刷新比赛
        function refreshMatch() {
            var mid = '{{$match['mid']}}';
            var first = mid.substr(0,2);
            var second = mid.substr(2,2);
            var url = 'http://match.liaogou168.com/static/terminal/1/'+ first +'/'+ second +'/'+mid+'/match.json';
            url = 'http://localhost:8000/static/terminal/1/10/20/1020697/match.json';
            $.ajax({
                'url': url,
                'success': function (json) {
                    //比分
                    if (json['status'] > 0 || json['status'] == -1) {
                        $('p.score span.host').html(json['hscore']);
                        $('p.score span.away').html(json['ascore']);
                        if(json['sport'] == 1)
                            $('p.time').html(json['current_time']);
                        else
                            $('p.time').html(json['live_time_str']);
                    }
                    if (json['status'] == -1){
                        $('p.time').html('已结束');
                    }
                }
            });
        }
        if ('{{$match['status']}}' == -1){

        }
        else{
            window.setInterval('refreshMatch()', 5000);
        }
    </script>
    @endsection

@section('navContent')
    <div class="home"><p class="abox"><a href="index.html"><img src="/pc/img/logo_image_n.png"></a></p></div>
    <div class="Column">
        <a class="on" href="/match/foot/immediate.html">足球</a>
        <a href="/match/basket/immediate_t.html">篮球</a>
        <a href="">主播</a>
        <a href="">手机APP</a>
    </div>
@endsection