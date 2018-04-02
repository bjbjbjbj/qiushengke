@extends('pc.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/pc/css/video.css">
    <style>
        .hide{
            display: none;
        }
        .show{
            display: block;
        }
    </style>
@endsection
@section('js')
    <script type="text/javascript" src="{{$cdn}}/pc/js/video.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            setPage();
            var lis = $('div#Live div.data ul li');
            if (lis.length == 0){
                $('div#Live div.data button.open')[0].className = 'open hide';
            }
            else {
                $('div#Live div.data button.open')[0].className = 'open';
            }
        }
        function clickHideScore(button) {
            if (button.innerHTML == '(隐藏比分)'){
                $('div.mbox p.score')[0].className = 'score hide';
                button.innerHTML = '(显示比分)'
            }
            else{
                $('div.mbox p.score')[0].className = 'score';
                button.innerHTML = '(隐藏比分)'
            }
        }

    </script>
    <script type="text/javascript">
        //获取是S还是非S
        function GetHttp () {
            if (location.href.indexOf('https://') != -1) {
                return 'https://';
            }else{
                return 'http://';
            }
        }
        //通过播放地址判断使用http头
        function CheckHttp (Link) {
            if (Link.indexOf('.flv') != -1 || Link.indexOf('rtmp://') == 0 || Link.indexOf('.m3u8') != -1) { //播放方式为播放器播放
                return 'https://';
            }else{
                return 'http://';
            }
        }

        function changeChannel(Link,obj) {
            if($('div.line a.on').length > 0)
                $('div.line a.on')[0].className = '';
            obj.className = 'on';
            document.getElementById('MyFrame').src = Link;
        }

        //选第一个
        $("div#Live a:first").trigger("click");


        //时间格式化
        function add0(m){return m<10?'0'+m:m }
        function format(string)
        {
            string = string*1000;
            var time = new Date(string);
            var y = time.getFullYear();
            var m = time.getMonth()+1;
            var d = time.getDate();
            var h = time.getHours();
            var mm = time.getMinutes();
            var s = time.getSeconds();
            return add0(h)+':'+add0(mm);
        }

        var user = '';

        //聊天室相关
        //发送
        function postChat() {
            user = $('#charUser')[0].value;
            var message = $('#charContent')[0].value;
            if(message.length == 0) {
                alert('请输入内容');
                return;
            }
            if(user.length == 0) {
                alert('请输入昵称');
                return;
            }
            var url = '/chat/post';
            var current = new Date();
            current = current.getTime()/1000;
            current = format(current);
            $.post({
                        'url': url,
                        'data':{
                            "sport":'{{$sport}}',
                            "mid":'{{$match['mid']}}',
                            "message":message,
                            "user":user,
                        },
                        'success': function (json) {
                            $('#charContent')[0].value = ''
                            addChat(user,message,current);
                        }
                    }
            );
        }

        var current_time = 0;

        //获取聊天数据(增量
        function getChat() {
            var url = '/chat/json/{{$sport}}/{{substr($match['mid'],0,2)}}/{{substr($match['mid'],2,2)}}/{{$match['mid']}}_t.json';
            if ($('div#Chatroom ul li').length == 0){
                url = '/chat/json/{{$sport}}/{{substr($match['mid'],0,2)}}/{{substr($match['mid'],2,2)}}/{{$match['mid']}}.json';
            }
            $.ajax({
                        'url': url,
                        'success': function (json) {
                            if (json){
                                if (json.length > 0 && current_time >= json[json.length - 1]['time']){
                                    return;
                                }
                                var ul = $('div#Chatroom ul');
                                for (var i = 0 ; i < json.length ; i++){
                                    var current = new Date();
                                    var data = json[i];
                                    current = current.getTime()/1000;
                                    current_time = data['time'];
                                    if($('div#Chatroom ul li').length > 0) {
                                        //10秒前不出
                                        if (data['time'] < current - 10) {
                                            continue;
                                        }
                                    }
                                    if (user && user == data['user']) {
                                        continue;
                                    }
                                    var time = format(data['time']);
                                    addChat(data['user'],data['content'],time);
                                }
                            }
                        }
                    }
            );
        }
        if ('{{$match['status']}}' == -1){
            getChat();
        }
        else{
            getChat();
            window.setInterval('getChat()', 1000);
        }

        if ($("div.line a:first").length > 0) {
            $("div.line a:first")[0].className = 'on';
        }

        //赔率更新
        function refreshOddByMid(){
            ID = '{{$match['mid']}}';
            ID = ID + '';
            var first = ID.substr(0,2);
            var second = ID.substr(2,2);
            var url = "/static/terminal/"+"{{$sport}}"+"/"+first+"/"+second+"/"+ID+"/roll.json";
            url = '{{env('MATCH_URL')}}' + url;
            $.ajax({
                "url":url,
                dataType: "jsonp",
                "success": function (json) {
                    //全场/半场
                    _updateOddBody('all',json);
                    _updateOddBody('half',json);
                },
                "error": function () {
                }
            });
        }

        function _updateOddBody(key,json) {
            var tbody = $('.' + 'odd_'+key);
            if (json[key]) {
                _updateOdd(tbody, 'a', '1', json[key]['1'], '1');
                _updateOdd(tbody, 'o', '1', json[key]['3'], '1');
                _updateOdd(tbody, 'g', '1', json[key]['2'], '1');
                if(json[key]['1'])
                    _updateOdd(tbody, 'a', '2', json[key]['1'], json[key]['1']['middle'] == null ? '2' : '');
                if(json[key]['2'])
                    _updateOdd(tbody, 'o', '2', json[key]['3'], json[key]['2']['middle'] == null ? '2' : '');
                if(json[key]['3'])
                    _updateOdd(tbody, 'g', '2', json[key]['2'], json[key]['3']['middle'] == null ? '2' : '');
            }
        }

        //更新赔率小框 tbody全场半场 key类型a亚盘o欧赔g大小球 key21初盘2即时 data数据 key3数据拿那个
        function _updateOdd (tbody,key,key2,data,key3) {
            if (data == null || data == undefined){
                return;
            }
            var middle = data['middle'+key3];
            if (middle) {
                var p = tbody.find('td.' + key + 'up' + key2)[0];
                if (p) {
                    p.innerHTML = data['up' + key3];
                }
                var p = tbody.find('td.' + key + 'mid' + key2)[0];
                if ('a' == key) {
                    middle = getHandicapCn(middle, '', 1, 1, true);
                }
                else if ('g' == key) {
                    middle = getHandicapCn(middle, '', 2, 1, true);
                }
                if (p) {
                    p.innerHTML = middle;
                }
                var p = tbody.find('td.' + key + 'down' + key2)[0];
                if (p) {
                    p.innerHTML = data['down' + key3];
                }
            }
        }

        refreshOddByMid();
    </script>
    @yield('live_js')
@endsection

@section('content')
    <?php
    $hicon = strlen($match['hicon']) > 0 ? $match['hicon'] : '/pc/img/icon_teamDefault.png';
    $aicon = strlen($match['aicon']) > 0 ? $match['aicon'] : '/pc/img/icon_teamDefault.png';
    if ($sport == 1)
        $matchTime = \App\Http\Controllers\PC\CommonTool::getMatchCurrentTime($match['time'],$match['timehalf'],$match['status']);
    else
        $matchTime = \App\Http\Controllers\PC\CommonTool::getBasketCurrentTime($match['time'],$match['live_time_str']);
    ?>
    <div id="Match">
        <div class="mbox">
            <dl>
                <dd class="host">
                    <img src="{{$hicon}}">
                    <p>{{$match['hname']}}</p>
                </dd>
                <dt>
                <p class="time">{!! $matchTime !!}</p>
                <p class="score"><span class="host">{{$match['hscore']}}</span><span class="away">{{$match['ascore']}}</span></p><!--隐藏时增加hid-->
                <button onclick="clickHideScore(this)">(隐藏比分)</button>
                </dt>
                <dd class="away">
                    <img src="{{$aicon}}">
                    <p>{{$match['aname']}}</p>
                </dd>
            </dl>
            <div class="adorn away"></div>
            <div class="adorn host"></div>
        </div>
    </div>
    <div id="Con">
        <div id="Odd">
            <table>
                <thead>
                <tr>
                    <th></th>
                    <th colspan="3">欧赔</th>
                    <th colspan="3">亚盘</th>
                    <th colspan="3">大小球</th>
                </tr>
                </thead>
                <tbody>
                @if($match['status'] > 0)
                    @if(isset($roll['half']))
                        <tr class="odd_half">
                            <td>半场滚球</td>
                            @if(isset($roll['half']['3']) && isset($roll['half']['3']['up']))
                                <td class="green oup2">{{$roll['half']['3']['up']}}</td>
                                @if($sport == 1)
                                    <td class="green omid2">{{$roll['half']['3']['middle']}}</td>
                                @else
                                    <td class="green omid2">-</td>
                                @endif
                                <td class="green odown2">{{$roll['half']['3']['down']}}</td>
                            @else
                                <td class="green oup2">-</td>
                                <td class="green omid2">-</td>
                                <td class="green odown2">-</td>
                            @endif
                            @if(isset($roll['half']['1']) && isset($roll['half']['1']['up']))
                                <td class="green aup2">{{$roll['half']['1']['up']}}</td>
                                <td class="green amid2">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['half']['1']['middle'])}}</td>
                                <td class="green adown2">{{$roll['half']['1']['down']}}</td>
                            @else
                                <td class="green aup2">-</td>
                                <td class="green amid2">-</td>
                                <td class="green adown2">-</td>
                            @endif
                            @if(isset($roll['half']['2']) && isset($roll['half']['2']['up']))
                                <td class="green gup2">{{$roll['half']['2']['up']}}</td>
                                <td class="green gmid2">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['half']['2']['middle'],'-',\App\Http\Controllers\PC\CommonTool::k_odd_type_ou)}}</td>
                                <td class="green gdown2">{{$roll['half']['2']['down']}}</td>
                            @else
                                <td class="green gup2">-</td>
                                <td class="green gmid2">-</td>
                                <td class="green gdown2">-</td>
                            @endif
                        </tr>
                    @else
                        <tr class="odd_half">
                            <td>半场滚球</td>
                            <td class="green oup2">-</td>
                            <td class="green omid2">-</td>
                            <td class="green odown2">-</td>
                            <td class="green aup2">-</td>
                            <td class="green amid2">-</td>
                            <td class="green adown2">-</td>
                            <td class="green gup2">-</td>
                            <td class="green gmid2">-</td>
                            <td class="green gdown2">-</td>
                        </tr>
                    @endif
                    @if(isset($roll['all']))
                        <tr class="odd_all">
                            <td>全场滚球</td>
                            @if(isset($roll['all']['3']) && isset($roll['all']['3']['up']))
                                <td class="green oup2">{{$roll['all']['3']['up']}}</td>
                                @if($sport == 1)
                                    <td class="green omid2">{{$roll['all']['3']['middle']}}</td>
                                @else
                                    <td class="green odown2">-</td>
                                @endif
                                <td class="green">{{$roll['all']['3']['down']}}</td>
                            @else
                                <td class="green oup2">-</td>
                                <td class="green omid2">-</td>
                                <td class="green odown2">-</td>
                            @endif
                            @if(isset($roll['all']['1']) && isset($roll['all']['1']['up']))
                                <td class="green aup2">{{$roll['all']['1']['up']}}</td>
                                <td class="green amid2">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['all']['1']['middle'])}}</td>
                                <td class="green adown2">{{$roll['all']['1']['down']}}</td>
                            @else
                                <td class="green aup2">-</td>
                                <td class="green amid2">-</td>
                                <td class="green adown2">-</td>
                            @endif
                            @if(isset($roll['all']['2']) && isset($roll['all']['2']['up']))
                                <td class="green gup2">{{$roll['all']['2']['up']}}</td>
                                <td class="green gmid2">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['all']['2']['middle'],'-',\App\Http\Controllers\PC\CommonTool::k_odd_type_ou)}}</td>
                                <td class="green gdown2">{{$roll['all']['2']['down']}}</td>
                            @else
                                <td class="green gup2">-</td>
                                <td class="green gmid2">-</td>
                                <td class="green gdown2">-</td>
                            @endif
                        </tr>
                    @else
                        <tr class="odd_all">
                            <td>全场滚球</td>
                            <td class="green oup2">-</td>
                            <td class="green omid2">-</td>
                            <td class="green odown2">-</td>
                            <td class="green aup2">-</td>
                            <td class="green amid2">-</td>
                            <td class="green adown2">-</td>
                            <td class="green gup2">-</td>
                            <td class="green gmid2">-</td>
                            <td class="green gdown2">-</td>
                        </tr>
                    @endif
                @else
                    @if(isset($roll['all']))
                        <tr class="odd_all">
                            <td>初盘</td>
                            @if(isset($roll['all']['3']) && isset($roll['all']['3']['up1']))
                                <td class="green oup1">{{$roll['all']['3']['up1']}}</td>
                                @if($sport == 1)
                                    <td class="green omid1">{{$roll['all']['3']['middle1']}}</td>
                                @else
                                    <td class="green omid1">-</td>
                                @endif
                                <td class="green odown1">{{$roll['all']['3']['down1']}}</td>
                            @else
                                <td class="green oup1">-</td>
                                <td class="green omid1">-</td>
                                <td class="green odown1">-</td>
                            @endif
                            @if(isset($roll['all']['1']) && isset($roll['all']['1']['up1']))
                                <td class="green aup1">{{$roll['all']['1']['up1']}}</td>
                                <td class="green amid1">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['all']['1']['middle1'])}}</td>
                                <td class="green adown1">{{$roll['all']['1']['down1']}}</td>
                            @else
                                <td class="green aup1">-</td>
                                <td class="green amid1">-</td>
                                <td class="green adown1">-</td>
                            @endif
                            @if(isset($roll['all']['2']) && isset($roll['all']['2']['up1']))
                                <td class="green gup1">{{$roll['all']['2']['up1']}}</td>
                                <td class="green gmid1">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['all']['2']['middle1'],'-',\App\Http\Controllers\PC\CommonTool::k_odd_type_ou)}}</td>
                                <td class="green gdown1">{{$roll['all']['2']['down1']}}</td>
                            @else
                                <td class="green gup1">-</td>
                                <td class="green gmid1">-</td>
                                <td class="green gdown1">-</td>
                            @endif
                        </tr>
                        <tr class="odd_all">
                            <td>即盘</td>
                            @if(isset($roll['all']['3']) && isset($roll['all']['3']['up1']))
                                <td class="green oup2">{{$roll['all']['3']['up2']}}</td>
                                @if($sport == 1)
                                    <td class="green omid2">{{$roll['all']['3']['middle2']}}</td>
                                @else
                                    <td class="green omid2">-</td>
                                @endif
                                <td class="green odown2">{{$roll['all']['3']['down2']}}</td>
                            @else
                                <td class="green oup2">-</td>
                                <td class="green omid2">-</td>
                                <td class="green odown2">-</td>
                            @endif
                            @if(isset($roll['all']['1']) && isset($roll['all']['1']['up1']))
                                <td class="green aup2">{{$roll['all']['1']['up2']}}</td>
                                <td class="green amid2">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['all']['1']['middle2'])}}</td>
                                <td class="green adown2">{{$roll['all']['1']['down2']}}</td>
                            @else
                                <td class="green aup2">-</td>
                                <td class="green amid1">-</td>
                                <td class="green adown2">-</td>
                            @endif
                            @if(isset($roll['all']['2']) && isset($roll['all']['2']['up1']))
                                <td class="green gup2">{{$roll['all']['2']['up2']}}</td>
                                <td class="green gmid2">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['all']['2']['middle2'],'-',\App\Http\Controllers\PC\CommonTool::k_odd_type_ou)}}</td>
                                <td class="green gdown2">{{$roll['all']['2']['down2']}}</td>
                            @else
                                <td class="green gup2">-</td>
                                <td class="green gmid2">-</td>
                                <td class="green gdown2">-</td>
                            @endif
                        </tr>
                    @elseif(isset($match) &&
                    (
                    isset($match['asiamiddle1']) ||
                    isset($match['asiamiddle2']) ||
                    isset($match['ouup1']) ||
                    isset($match['ouup2']) ||
                    isset($match['goalmiddle1']) ||
                    isset($match['goalmiddle2']))
                    )
                        <tr class="odd_all">
                            <td>初盘</td>
                            @if(isset($match['ouup1']))
                                <td class="green oup1">{{$match['ouup1']}}</td>
                                @if($sport == 1)
                                    <td class="green omid1">{{$match['oumiddle1']}}</td>
                                @else
                                    <td class="green omid1">-</td>
                                @endif
                                <td class="green odown1">{{$match['oudown1']}}</td>
                            @else
                                <td class="green oup1">-</td>
                                <td class="green omid1">-</td>
                                <td class="green odown1">-</td>
                            @endif
                            @if(isset($match['asiaup1']))
                                <td class="green aup1">{{$match['asiaup1']}}</td>
                                <td class="green amid1">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($match['asiamiddle1'])}}</td>
                                <td class="green adown1">{{$match['asiadown1']}}</td>
                            @else
                                <td class="green aup1">-</td>
                                <td class="green amid1">-</td>
                                <td class="green adown1">-</td>
                            @endif
                            @if(isset($match['goalup1']))
                                <td class="green gup1">{{$match['goalup1']}}</td>
                                <td class="green gmid1">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($match['goalmiddle1'],'-',\App\Http\Controllers\PC\CommonTool::k_odd_type_ou)}}</td>
                                <td class="green gdown1">{{$match['goaldown1']}}</td>
                            @else
                                <td class="green gup1">-</td>
                                <td class="green gmid1">-</td>
                                <td class="green gdown1">-</td>
                            @endif
                        </tr>
                        <tr class="odd_all">
                            <td>即盘</td>
                            @if(isset($match['ouup2']))
                                <td class="green oup2">{{$match['ouup2']}}</td>
                                @if($sport == 1)
                                    <td class="green omid2">{{$match['oumiddle2']}}</td>
                                @else
                                    <td class="green omid2">-</td>
                                @endif
                                <td class="green odown2">{{$match['oudown2']}}</td>
                            @else
                                <td class="green oup2">-</td>
                                <td class="green omid2">-</td>
                                <td class="green odown2">-</td>
                            @endif
                            @if(isset($match['asiaup2']))
                                <td class="green aup2">{{$match['asiaup2']}}</td>
                                <td class="green amid2">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($match['asiamiddle2'])}}</td>
                                <td class="green adown2">{{$match['asiadown2']}}</td>
                            @else
                                <td class="green aup2">-</td>
                                <td class="green amid2">-</td>
                                <td class="green adown2">-</td>
                            @endif
                            @if(isset($match['goalup2']))
                                <td class="green gup2">{{$match['goalup2']}}</td>
                                <td class="green gmid2">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($match['goalmiddle2'],'-',\App\Http\Controllers\PC\CommonTool::k_odd_type_ou)}}</td>
                                <td class="green gdown2">{{$match['goaldown2']}}</td>
                            @else
                                <td class="green gup2">-</td>
                                <td class="green gmid2">-</td>
                                <td class="green gdown2">-</td>
                            @endif
                        </tr>
                    @else
                        <tr class="odd_all">
                            <td>初盘</td>
                            <td class="green oup1">-</td>
                            <td class="green omid1">-</td>
                            <td class="green odown1">-</td>
                            <td class="green aup1">-</td>
                            <td class="green amid1">-</td>
                            <td class="green adown1">-</td>
                            <td class="green gup1">-</td>
                            <td class="green gmid1">-</td>
                            <td class="green gdown1">-</td>
                        </tr>
                        <tr class="odd_all">
                            <td>即盘</td>
                            <td class="green oup2">-</td>
                            <td class="green omid2">-</td>
                            <td class="green odown2">-</td>
                            <td class="green aup2">-</td>
                            <td class="green amid1">-</td>
                            <td class="green adown2">-</td>
                            <td class="green gup2">-</td>
                            <td class="green gmid2">-</td>
                            <td class="green gdown2">-</td>
                        </tr>
                    @endif
                @endif
                </tbody>
            </table>
        </div>
        <div id="Live">
            @if(isset($lives))
                <div class="line">
                    @for($i = 0 ; $i < count($lives); $i++)
                        <?php
                        $channel = $lives[$i];
                        ?>
                        <?php
                        $preUrl = str_replace("http://","http://",env('APP_URL'));
                        $link = $preUrl.'/live/player/player-'.$channel['id'].'.html';
                        ?>
                        @if($i == count($lives) - 1)
                            <a onclick="changeChannel('{{$link}}',this)" style="width: 25%;">{{$channel['name']}}</a>
                        @else
                            <a onclick="changeChannel('{{$link}}',this)" style="width: 25%;">{{$channel['name']}}</a>
                        @endif
                    @endfor
                </div>
            @endif
            <div id="Player">
                <iframe id="MyFrame" src=""></iframe>
                <div class="flash"></div>
            </div>
            @yield('live_data')
            <div id="Chatroom">
                <p class="title">聊天室<button>清除</button></p>
                <ul>

                </ul>
            </div>
            <div id="Chat">
                <p class="name">
                    <span>昵称：</span>
                    <input id="charUser" type="text" name="name" placeholder="大侠请留名">
                </p>
                <textarea id="charContent" placeholder="输入信息"></textarea>
                <button class="push" onclick="postChat()">发送</button>
            </div>
        </div>
        @yield('live_content')
    </div>
@endsection