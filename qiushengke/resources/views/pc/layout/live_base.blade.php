@extends('pc.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="/pc/css/video.css">
@endsection
@section('js')
    <script type="text/javascript" src="/pc/js/video.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            setPage();
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
            if(message.length == 0)
                alert('请输入内容');
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
//                            var ul = $('div#Chatroom ul');
//                            var li = '<li>'+
//                                    '<p class="time">'+current+'</p>'+
//                                    '<p class="name">'+user+'</p>'+
//                                    '<p class="con">'+message+'</p>'+
//                                    '</li>'
//                            ul.append(li);
                            addChat(user,message,current);
                        }
                    }
            );
        }

        var current_time = 0;

        //获取聊天数据(增量
        function getChat() {
            var url = '/chat/json/{{$sport}}/{{substr($match['mid'],0,2)}}/{{substr($match['mid'],2,2)}}/{{$match['mid']}}_t.json';
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
                                    //10秒前不出
                                    if (data['time'] < current - 10){
//                                        console.log(data['user']+' ' + data['content']+' ' + time);
                                        continue;
                                    }
                                    if (user && user == data['user']){
//                                        console.log('bj ' + data['user']+' ' + data['content']+' ' + time);
                                        continue;
                                    }
                                    var time = format(data['time']);
//                                    var li = '<li>'+
//                                            '<p class="time">'+time+'</p>'+
//                                            '<p class="name">'+data['user']+'</p>'+
//                                            '<p class="con">'+data['content']+'</p>'+
//                                            '</li>'
//                                    ul.append(li);
                                    addChat(data['user'],data['content'],time);
                                    current_time = data['time'];
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
//        getChat();
//        window.setInterval('getChat()', 10000);
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
                <button>(隐藏比分)</button>
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
                        <tr>
                            <td>半场滚球</td>
                            @if(isset($roll['half']['3']) && isset($roll['half']['3']['up']))
                                <td class="green">{{$roll['half']['3']['up']}}</td>
                                @if($sport == 1)
                                    <td class="green">{{$roll['half']['3']['middle']}}</td>
                                @else
                                    <td class="green">-</td>
                                @endif
                                <td class="green">{{$roll['half']['3']['down']}}</td>
                            @else
                                <td class="green">-</td>
                                <td class="green">-</td>
                                <td class="green">-</td>
                            @endif
                            @if(isset($roll['half']['1']) && isset($roll['half']['1']['up']))
                                <td class="green">{{$roll['half']['1']['up']}}</td>
                                <td class="green">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['half']['1']['middle'])}}</td>
                                <td class="green">{{$roll['half']['1']['down']}}</td>
                            @else
                                <td class="green">-</td>
                                <td class="green">-</td>
                                <td class="green">-</td>
                            @endif
                            @if(isset($roll['half']['2']) && isset($roll['half']['2']['up']))
                                <td class="green">{{$roll['half']['2']['up']}}</td>
                                <td class="green">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['half']['2']['middle'],'-',\App\Http\Controllers\PC\CommonTool::k_odd_type_ou)}}</td>
                                <td class="green">{{$roll['half']['2']['down']}}</td>
                            @else
                                <td class="green">-</td>
                                <td class="green">-</td>
                                <td class="green">-</td>
                            @endif
                        </tr>
                    @else
                        <tr>
                            <td>半场滚球</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                        </tr>
                    @endif
                    @if(isset($roll['all']))
                    <tr>
                        <td>全场滚球</td>
                        @if(isset($roll['all']['3']) && isset($roll['all']['3']['up']))
                            <td class="green">{{$roll['all']['3']['up']}}</td>
                            @if($sport == 1)
                                <td class="green">{{$roll['all']['3']['middle']}}</td>
                            @else
                                <td class="green">-</td>
                            @endif
                            <td class="green">{{$roll['all']['3']['down']}}</td>
                        @else
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                        @endif
                        @if(isset($roll['all']['1']) && isset($roll['all']['1']['up']))
                            <td class="green">{{$roll['all']['1']['up']}}</td>
                            <td class="green">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['all']['1']['middle'])}}</td>
                            <td class="green">{{$roll['all']['1']['down']}}</td>
                        @else
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                        @endif
                        @if(isset($roll['all']['2']) && isset($roll['all']['2']['up']))
                            <td class="green">{{$roll['all']['2']['up']}}</td>
                            <td class="green">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['all']['2']['middle'],'-',\App\Http\Controllers\PC\CommonTool::k_odd_type_ou)}}</td>
                            <td class="green">{{$roll['all']['2']['down']}}</td>
                        @else
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                        @endif
                    </tr>
                        @endif
                @else
                    @if(isset($roll['all']))
                        <tr>
                            <td>初盘</td>
                            @if(isset($roll['all']['3']) && isset($roll['all']['3']['up1']))
                                <td class="green">{{$roll['all']['3']['up1']}}</td>
                                @if($sport == 1)
                                    <td class="green">{{$roll['all']['3']['middle1']}}</td>
                                @else
                                    <td class="green">-</td>
                                @endif
                                <td class="green">{{$roll['all']['3']['down1']}}</td>
                            @else
                                <td class="green">-</td>
                                <td class="green">-</td>
                                <td class="green">-</td>
                            @endif
                            @if(isset($roll['all']['1']) && isset($roll['all']['1']['up1']))
                                <td class="green">{{$roll['all']['1']['up1']}}</td>
                                <td class="green">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['all']['1']['middle1'])}}</td>
                                <td class="green">{{$roll['all']['1']['down1']}}</td>
                            @else
                                <td class="green">-</td>
                                <td class="green">-</td>
                                <td class="green">-</td>
                            @endif
                            @if(isset($roll['all']['2']) && isset($roll['all']['2']['up1']))
                                <td class="green">{{$roll['all']['2']['up1']}}</td>
                                <td class="green">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['all']['2']['middle1'],'-',\App\Http\Controllers\PC\CommonTool::k_odd_type_ou)}}</td>
                                <td class="green">{{$roll['all']['2']['down1']}}</td>
                            @else
                                <td class="green">-</td>
                                <td class="green">-</td>
                                <td class="green">-</td>
                            @endif
                        </tr>
                        <tr>
                            <td>即盘</td>
                            @if(isset($roll['all']['3']) && isset($roll['all']['3']['up1']))
                                <td class="green">{{$roll['all']['3']['up2']}}</td>
                                @if($sport == 1)
                                    <td class="green">{{$roll['all']['3']['middle2']}}</td>
                                @else
                                    <td class="green">-</td>
                                @endif
                                <td class="green">{{$roll['all']['3']['down2']}}</td>
                            @else
                                <td class="green">-</td>
                                <td class="green">-</td>
                                <td class="green">-</td>
                            @endif
                            @if(isset($roll['all']['1']) && isset($roll['all']['1']['up1']))
                                <td class="green">{{$roll['all']['1']['up2']}}</td>
                                <td class="green">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['all']['1']['middle2'])}}</td>
                                <td class="green">{{$roll['all']['1']['down2']}}</td>
                            @else
                                <td class="green">-</td>
                                <td class="green">-</td>
                                <td class="green">-</td>
                            @endif
                            @if(isset($roll['all']['2']) && isset($roll['all']['2']['up1']))
                                <td class="green">{{$roll['all']['2']['up2']}}</td>
                                <td class="green">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['all']['2']['middle2'],'-',\App\Http\Controllers\PC\CommonTool::k_odd_type_ou)}}</td>
                                <td class="green">{{$roll['all']['2']['down2']}}</td>
                            @else
                                <td class="green">-</td>
                                <td class="green">-</td>
                                <td class="green">-</td>
                            @endif
                        </tr>
                    @else
                        <tr>
                            <td>初盘</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                        </tr>
                        <tr>
                            <td>即盘</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                            <td class="green">-</td>
                        </tr>
                    @endif
                @endif
                </tbody>
            </table>
        </div>
        <div id="Live">
            <div class="line">
                @for($i = 0 ; $i < count($lives); $i++)
                    <?php
                    $channel = $lives[$i];
                    ?>

                    <?php
                    if ($channel['type'] == 3 || $channel['type'] == 1 || $channel['type'] == 2 || $channel['type'] == 7)
                        $preUrl = str_replace("https://","http://",env('AKQ_URL'));
                    else if($channel['type'] == 99){
                        if ($channel['player'] == 11){
                            $preUrl = str_replace("https://","http://",env('AKQ_URL'));
                        }
                        else{
                            if (stristr($channel['link'],'player.pptv.com')){
                                $preUrl = str_replace("https://","http://",env('AKQ_URL'));
                            }
                            else{
                                $preUrl = str_replace("http://","https://",env('AKQ_URL'));
                            }
                        }
                    } else {
                        $preUrl = str_replace("http://","https://",env('AKQ_URL'));
                    }
                    $link = $preUrl.'/live/player/player-'.$channel['id'].'-'.$channel['type'].'.html';
                    ?>
                    @if($i == count($lives) - 1)
                        <a onclick="changeChannel('{{$link}}',this)" style="width: 25%;" class="last">{{$channel['name']}}</a>
                    @else
                        <a onclick="changeChannel('{{$link}}',this)" style="width: 25%;">{{$channel['name']}}</a>
                    @endif
                @endfor
            </div>
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