<div class="event">
    <p class="title">比赛事件</p>
    @if(isset($events['event']))
        <?php
        $events = $events['event'];
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
    @endif
    <div class="noList"></div>
</div>