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
                    <tr>
                        <td>半场滚球</td>
                        @if(isset($roll['half']['3']))
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
                        <td class="green">{{$roll['half']['1']['up']}}</td>
                        <td class="green">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['half']['1']['middle'])}}</td>
                        <td class="green">{{$roll['half']['1']['down']}}</td>
                        <td class="green">{{$roll['half']['2']['up']}}</td>
                        <td class="green">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['half']['2']['middle'],'-',\App\Http\Controllers\PC\CommonTool::k_odd_type_ou)}}</td>
                        <td class="green">{{$roll['half']['2']['down']}}</td>
                    </tr>
                    <tr>
                        <td>全场滚球</td>
                        @if(isset($roll['half']['3']))
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
                        <td class="green">{{$roll['all']['1']['up']}}</td>
                        <td class="green">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['all']['1']['middle'])}}</td>
                        <td class="green">{{$roll['all']['1']['down']}}</td>
                        <td class="green">{{$roll['all']['2']['up']}}</td>
                        <td class="green">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['all']['2']['middle'],'-',\App\Http\Controllers\PC\CommonTool::k_odd_type_ou)}}</td>
                        <td class="green">{{$roll['all']['2']['down']}}</td>
                    </tr>
                @else
                    <tr>
                        <td>初盘</td>
                        <td class="green">{{$roll['all']['3']['up1']}}</td>
                        @if($sport == 1)
                            <td class="green">{{$roll['all']['3']['middle1']}}</td>
                        @else
                            <td class="green">-</td>
                        @endif
                        <td class="green">{{$roll['all']['3']['down1']}}</td>
                        <td class="green">{{$roll['all']['1']['up1']}}</td>
                        <td class="green">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['all']['1']['middle1'])}}</td>
                        <td class="green">{{$roll['all']['1']['down1']}}</td>
                        <td class="green">{{$roll['all']['2']['up1']}}</td>
                        <td class="green">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['all']['2']['middle1'],'-',\App\Http\Controllers\PC\CommonTool::k_odd_type_ou)}}</td>
                        <td class="green">{{$roll['all']['2']['down1']}}</td>
                    </tr>
                    <tr>
                        <td>即盘</td>
                        <td class="green">{{$roll['all']['3']['up2']}}</td>
                        @if($sport == 1)
                            <td class="green">{{$roll['all']['3']['middle2']}}</td>
                        @else
                            <td class="green">-</td>
                        @endif
                        <td class="green">{{$roll['all']['3']['down2']}}</td>
                        <td class="green">{{$roll['all']['1']['up2']}}</td>
                        <td class="green">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['all']['1']['middle2'])}}</td>
                        <td class="green">{{$roll['all']['1']['down2']}}</td>
                        <td class="green">{{$roll['all']['2']['up2']}}</td>
                        <td class="green">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['all']['2']['middle2'],'-',\App\Http\Controllers\PC\CommonTool::k_odd_type_ou)}}</td>
                        <td class="green">{{$roll['all']['2']['down2']}}</td>
                    </tr>
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
                    <li>
                        <p class="time">01:52</p>
                        <p class="name">路飞的鞋</p>
                        <p class="con">我猜这场的比分是2：1 上半场小2.5 下半场客队来</p>
                    </li>
                    <li>
                        <p class="time">01:52</p>
                        <p class="name">红剑足球大神来也</p>
                        <p class="con">悉尼这么强我申花怎么办</p>
                    </li>
                    <li>
                        <p class="time">01:52</p>
                        <p class="name">从前从前有个</p>
                        <p class="con">西尼下半场进三个很轻松啊。慌啥啊。</p>
                    </li>
                    <li>
                        <p class="time">01:52</p>
                        <p class="name">路飞的鞋</p>
                        <p class="con">我猜这场的比分是2：1 上半场小2.5 下半场客队来</p>
                    </li>
                    <li>
                        <p class="time">01:52</p>
                        <p class="name">红剑足球大神来也</p>
                        <p class="con">悉尼这么强我申花怎么办</p>
                    </li>
                </ul>
            </div>
            <div id="Chat">
                <p class="name">
                    <span>昵称：</span>
                    <input type="text" name="name" placeholder="大侠请留名">
                </p>
                <textarea placeholder="输入信息"></textarea>
                <button class="push">发送</button>
            </div>
        </div>
        @yield('live_content')
    </div>
@endsection