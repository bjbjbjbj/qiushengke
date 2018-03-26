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
            @if(isset($tech['tech']))
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
                            <span style="width: {{(1 - $item['a_p'])*100}}%;"></span>
                        </div>
                    </li>
                @endforeach
            <!--li的数量必须是3的倍数，不够使用空li-->
            @endif
        </ul>
    </div>
@endsection
@section('live_content')
    <div id="Event">
        <?php
        $events = isset($tech['event'])?$tech['event']:null;
        ?>
        <?php
        //获取足球比赛的即时时间
        $time = isset($match['timehalf'])? $match['timehalf'] : $match['time'];
        $timehalf = $match['timehalf'];
        $now = time();
        $status = $match['status'];
        $matchTime = 0;
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

        $lastTime = isset($events) ? $events['last_event_time'] : 0;
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
            <p id="event_total_time" class="end">{{($lastTime > 90 || $matchTime > 90)?'120':'90'}}'</p>
            <img class="host" src="{{$hicon}}">
            <img class="away" src="{{$aicon}}">
            <dl class="line"><!--当前是90分钟样式，120分钟要重新计算宽度和位置，每15分钟一个节点是一定会有的，其他分钟有事件才添加就行-->
                @if($status == -1)
                    <dt id="event_time" style="width: 100%"><p>{{$matchTime}}'</p></dt>
                @else
                    <dt id="event_time" style="width: {{($lastTime > 90 ?(100/120):(100/90))*($matchTime - 1) + $width/2}}%"><p>{{$matchTime}}'</p></dt>
                @endif
                <dd id="event_15"
                        @if($matchTime > 15)
                        class="after"
                        @endif
                        minute="15" style="left: {{($lastTime > 90 ?(100/120):(100/90))*(15-1)}}%; width: {{$width}}%;">
                    <p class="minute">15'</p>
                </dd>
                <dd id="event_30"
                        @if($matchTime > 30)
                        class="after"
                        @endif
                        minute="30" style="left: {{($lastTime > 90 ?(100/120):(100/90))*(30-1)}}%; width: {{$width}}%;">
                    <p class="minute">30'</p>
                </dd>
                <dd id="event_45"
                        @if($matchTime > 45)
                        class="after"
                        @endif
                        minute="45" style="left: {{($lastTime > 90 ?(100/120):(100/90))*(45-1)}}%; width: {{$width}}%;">
                    <p class="minute">45'</p>
                </dd>
                <dd id="event_60"
                        @if($matchTime > 60)
                        class="after"
                        @endif
                        minute="60" style="left: {{($lastTime > 90 ?(100/120):(100/90))*(60-1)}}%; width: {{$width}}%;">
                    <p class="minute">60'</p>
                </dd>
                <dd id="event_75"
                        @if($matchTime > 75)
                        class="after"
                        @endif
                        minute="75" style="left:{{($lastTime > 90 ?(100/120):(100/90))*(75-1)}}%; width: {{$width}}%;">
                    <p class="minute">75'</p>
                </dd>
                @if($lastTime > 90)
                    <dd id="event_90"
                            @if($matchTime > 90)
                            class="after"
                            @endif
                            minute="90" style="left: {{(100/120)*(90-1)}}%; width: {{$width}}%;">
                        <p class="minute">90'</p>
                    </dd>
                    <dd id="event_105"
                            @if($matchTime > 105)
                            class="after"
                            @endif
                            minute="105" style="left: {{(100/120)*(105-1)}}%; width: {{$width}}%;">
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

                <div id="away_event">
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
        </div>

                <div id="host_event">
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
                </div>
            </dl>
        </div>
    </div>
@endsection

@section('live_js')
    <script type="text/javascript">
        var matchTime = '{{$matchTime}}';
        var lastTime = '{{$lastTime}}';
        var status = '{{$match['status']}}';
        //刷新比赛
        function refreshMatch() {
            var mid = '{{$match['mid']}}';
            var first = mid.substr(0,2);
            var second = mid.substr(2,2);
            var url = '/static/terminal/1/'+ first +'/'+ second +'/'+mid+'/match.json';
            url = '/test?url=' + '{{env('MATCH_URL')}}' + url;
            $.ajax({
                'url': url,
                'success': function (json) {
                    //比分
                    if (json['status'] > 0 || json['status'] == -1) {
                        $('p.score span.host').html(json['hscore']);
                        $('p.score span.away').html(json['ascore']);
                        if(json['sport'] == 1)
                            $('div.mbox p.time').html(json['current_time']);
                        else
                            $('div.mbox p.time').html(json['live_time_str']);
                    }
                    if (json['status'] == -1){
                        $('div.mbox p.time').html('已结束');
                    }
                    //刷新比赛时间,之后时间轴用到
                    var status = json['status'];
                    var time = json['timehalf']? json['timehalf'] : json['time'];
                    var timehalf = json['timehalf'];
                    var now = Date.parse(new Date());
                    if (status == 4){
                        matchTime = 90;
                    }
                    else if (status == 2) {
                        matchTime = 45;
                    }else if (status == 1) {
                        var diff = (now - time) > 0 ? (now - time) : 0;
                        matchTime = (floor((diff) % 86400 / 60)) > 45 ? 45 : floor((diff) % 86400 / 60);
                    } else if (status == 3) {
                        var diff = (now - timehalf) > 0 ? (now - timehalf) : 0;
                        matchTime = (floor((diff) % 86400 / 60)) > 45 ? 90 : (floor((diff) % 86400 / 60) + 45);
                    }

                    if (status == -1){
                        matchTime = 90;
                    }
                    console.log(matchTime);
                }
            });
        }

        //比赛结束不定时刷新
        if ('{{$match['status']}}' >= 0){
            window.setInterval('refreshMatch()', 5000);
        }

        refreshMatch();

        function refreshMatchTech(ID){
            ID = ID + '';
            var first = ID.substr(0,2);
            var second = ID.substr(2,2);
            var url = "/static/terminal/1/"+first+"/"+second+"/"+ID+"/tech.json";
            url = '/test?url=' + '{{env('MATCH_URL')}}' + url;
            $.ajax({
                "url": url,
                "dataType": "json",
                "success": function (json) {
                    //刷新基础ui,例如时间轴,比赛时间
                    $('p#event_total_time').html((matchTime > 90 || lastTime > 90) ? '120\'':'90\'');
                    if (lastTime > 90 || matchTime > 90){
                        var width = Math.round(100/120,2);
                    }
                    else{
                        var width = Math.round(100/90,2);
                    }
                    if(status == -1) {
                        $('dt#event_time').html('<p>' + matchTime + '\'</p>');
                        $('dt#event_time')[0].style.width='100%';
                    }
                    else{
                        $('dt#event_time').html('<p>' + matchTime + '\'</p>');
                        $('dt#event_time')[0].style.width = (parseInt(lastTime > 90 ?(100/120):(100/90))*(matchTime - 1) + parseInt(width/2)) + "%";
                    }
                    if (lastTime > 15 || matchTime > 15){
                        $('#event_15')[0].className = 'after';
                    }
                    if (lastTime > 30 || matchTime > 30){
                        $('#event_30')[0].className = 'after';
                    }
                    if (lastTime > 45 || matchTime > 45){
                        $('#event_45')[0].className = 'after';
                    }
                    if (lastTime > 60 || matchTime > 60){
                        $('#event_60')[0].className = 'after';
                    }
                    if (lastTime > 75 || matchTime > 75){
                        $('#event_75')[0].className = 'after';
                    }
                    if ($('#event_90')){
                        if (lastTime > 90 || matchTime > 90){
                            $('#event_90')[0].className = 'after';
                        }
                        if (lastTime > 105 || matchTime > 105){
                            $('#event_105')[0].className = 'after';
                        }
                    }

                    //事件
                    //处理事件数据格式
                    var host_events = new Object();//格式：[time=>[], time=>[], ...];
                    var away_events = new Object();//格式：[time=>[], time=>[], ...];
                    var host_temp_time = 0;
                    var away_temp_time = 0;
                    var last_event_time = 0;
                    var events = json['event'];
                    if (events && events['events'] && events['events'].length > 0) {
                        for (var i = 0 ; i < events['events'].length ; i++) {
                            var event = events['events'][i];
                            if (event['is_home'] == 1) {//主队事件
                                if (event['happen_time'] != host_temp_time) {
                                    host_temp_time = event['happen_time'];
                                }
                                if (null == host_events[host_temp_time]) {
                                    host_events[host_temp_time] = new Array();
                                    host_events[host_temp_time].push(event);
                                } else {
                                    host_events[host_temp_time].push(event);
                                }
                            } else {//客队事件
                                if (event['happen_time'] != away_temp_time) {
                                    away_temp_time = event['happen_time'];
                                }
                                if (null == away_events[away_temp_time]) {
                                    away_events[away_temp_time] = new Array();
                                    away_events[away_temp_time].push(event);
                                } else {
                                    away_events[away_temp_time].push(event);
                                }
                            }
                        }
                    }
                    //构建html
                    var html = _createEventHtml('host',host_events,width);
                    $('div#host_event').html(html);
                    var html = _createEventHtml('away',away_events,width);
                    $('div#away_event').html(html);

                    return;

                    //统计
                    var event = document.getElementById(ID+'_tboxCon');
                    if (typeof(event) != 'undefined') {
                        var events = json['tech'];
                        var content = '';
                        for (var i = 0 ; i < events.length ; i++) {
                            var item = events[i];
                            if (!(item.h_p == 0 && item.a_p == 0)){
                                content = content +
                                        '<dd class="total">' +
                                        '<p class="num host">' + item.h + '</p>' +
                                        '<p class="percent"><span class="host" width="' + item.h_p * 100 + '%"></span></p>' +
                                        '<p class="item">' + item.name + '</p>' +
                                        '<p class="percent"><span class="away" width="' + item.a_p * 100 + '%"></span></p>' +
                                        '<p class="num away">' + item.a + '</p>' +
                                        '</dd>';
                            }
                        }
                        var html = '<dl class="tbox"> <dt><p>统计</p></dt>' +
                                content +
                                '</dl>';
                        event.innerHTML = html;
                    }
                },
                "error": function () {

                }
            });
        }

        function _createEventHtml(key,events,width) {
            var html = '';
            for (var time in events) {
                var left = (lastTime > 90 ? (100 / 120) : (100 / 90)) * (parseInt(time) - 1);
                html = html + '<dd minute="' + time + '" style="left: ' + left + '%; width: ' + width + '%;">' +
                        '<ul class="' + key + '">';
                //处理数据
                for (var index in events[time]) {
                    var event = events[time][index];
                    var icon = '';
                    var cn = "";
                    switch (parseInt(event.kind)) {
                        case 1:
                            cn = event['player_name_j'] + "（进球）";
                            icon = "/pc/img/icon_goal.png";
                            break;
                        case 7:
                            cn = event['player_name_j'] + "（点球）";
                            icon = "/pc/img/icon_goal.png";
                            break;
                        case 8:
                            cn = event['player_name_j'] + "（乌龙球）";
                            icon = "/pc/img/icon_goal.png";
                            break;
                        case 2:
                            cn = event['player_name_j'] + "（红牌）";
                            icon = "/pc/img/icon_red.png";
                            break;
                        case 9:
                            cn = event['player_name_j'] + "（两黄一红）";
                            icon = "/pc/img/icon_red.png";
                            break;
                        case 3:
                            cn = event['player_name_j'] + "（黄牌）";
                            icon = "/pc/img/icon_yellow.png";
                            break;
                        case 11:
                            cn = event['player_name_j'] + "（换上） " + event['player_name_j2'] + "（换下）";
                            icon = "/pc/img/icon_exchange.png";
                            break;
                    }
                    html = html +
                            '<li>' +
                            '<img src="' + icon + '">' +
                            '<p><span><i>' + time + '\'</i>' + cn + '</span></p>' +
                            '</li>';
                }
                html = html + '</ul>' +
                        '</dd>';

            }
            return html;
        }

        refreshMatchTech({{$match['mid']}});
    </script>
@endsection

@section('navContent')
    @component('pc.layout.nav_content',['type'=>0])
    @endcomponent
@endsection