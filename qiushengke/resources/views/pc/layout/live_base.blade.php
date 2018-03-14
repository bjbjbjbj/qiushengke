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
@endsection

@section('content')
    <?php
    $hicon = strlen($match['hicon']) > 0 ? $match['hicon'] : '/pc/img/icon_teamDefault.png';
    $aicon = strlen($match['aicon']) > 0 ? $match['aicon'] : '/pc/img/icon_teamDefault.png';
    $matchTime = \App\Http\Controllers\PC\CommonTool::getMatchCurrentTime($match['time'],$match['timehalf'],$match['status']);
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
                        <td class="green">{{$roll['half']['3']['up']}}</td>
                        <td class="green">{{$roll['half']['3']['middle']}}</td>
                        <td class="green">{{$roll['half']['3']['down']}}</td>
                        <td class="green">{{$roll['half']['1']['up']}}</td>
                        <td class="green">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['half']['1']['middle'])}}</td>
                        <td class="green">{{$roll['half']['1']['down']}}</td>
                        <td class="green">{{$roll['half']['2']['up']}}</td>
                        <td class="green">{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($roll['half']['2']['middle'],'-',\App\Http\Controllers\PC\CommonTool::k_odd_type_ou)}}</td>
                        <td class="green">{{$roll['half']['2']['down']}}</td>
                    </tr>
                    <tr>
                        <td>全场滚球</td>
                        <td class="green">{{$roll['all']['3']['up']}}</td>
                        <td class="green">{{$roll['all']['3']['middle']}}</td>
                        <td class="green">{{$roll['all']['3']['down']}}</td>
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
                        <td class="green">{{$roll['all']['3']['middle1']}}</td>
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
                        <td class="green">{{$roll['all']['3']['middle2']}}</td>
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
            <div id="Anchor" style="display: none;">
                <p><img src="img/icon_teamDefault.png">王者表弟解说々...</p>
                <a href="">更多主播 >></a>
            </div>
            <div class="line">
                <a style="width: 25%;" class="on">动画直播</a>
                <a href="javascript:void(0)" style="width: 25%;" img="img/icon_teamDefault.png"><span>王者表弟解说々</span>的直播间</a>
                <a href="javascript:void(0)" style="width: 25%;">高清直播</a>
                <a href="javascript:void(0)" class="last">CCTV5</a><!--最后一个不用设置宽度-->
            </div>
            <div id="Player">
                <!-- <iframe src=""></iframe> -->
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