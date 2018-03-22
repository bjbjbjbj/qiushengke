@extends('pc.layout.base')
@section('navContent')
    <div class="home"><p class="abox"><a href="index.html"><img src="/pc/img/logo_image_n.png"></a></p></div>
    <div class="Column">
        <a href="/match/foot/immediate.html">足球</a>
        <a href="/match/basket/immediate_t.html">篮球</a>
        <a class="on">主播</a>
        <a href="">手机APP</a>
    </div>
@endsection
@section('content')
    <div id="Con">
        <div class="right">
            <div class="rbox" id="Match">
                <p class="title">赛事推荐</p>
                <div class="tabLine">
                    <button class="on">全部</button>
                    <button>英超</button>
                    <button>西甲</button>
                    <button>德甲</button>
                    <button>法甲</button>
                    <button>意甲</button>
                </div>
                <ul>
                    <li>
                        <p class="time"><span>意甲第24轮</span>02.10 03:45</p>
                        <div class="team">
                            <a href="video.html">直播中</a>
                            <p class="host"><img src="img/icon_teamDefault.png"><span>佛罗伦萨</span></p>
                            <p class="away"><img src="img/icon_teamDefault.png"><span>尤文图斯</span></p>
                        </div>
                        <p class="anchor">
                            <a href="video.html">WE黄炎发哈咖啡</a>
                            <a href="video.html">WE黄炎</a>
                        </p>
                    </li>
                    <li>
                        <p class="time"><span>意甲第24轮</span>02.10 03:45</p>
                        <div class="team">
                            <a href="video.html">直播中</a>
                            <p class="host"><img src="img/icon_teamDefault.png"><span>佛罗伦萨</span></p>
                            <p class="away"><img src="img/icon_teamDefault.png"><span>尤文图斯</span></p>
                        </div>
                        <p class="anchor">
                            <a href="video.html">WE黄炎发哈咖啡</a>
                            <a href="video.html">WE黄炎</a>
                            <a href="video.html">WE黄炎发哈咖啡老好人吖</a>
                        </p>
                    </li>
                    <li>
                        <p class="time"><span>意甲第24轮</span>02.10 03:45</p>
                        <div class="team">
                            <a href="video.html">直播中</a>
                            <p class="host"><img src="img/icon_teamDefault.png"><span>佛罗伦萨</span></p>
                            <p class="away"><img src="img/icon_teamDefault.png"><span>尤文图斯</span></p>
                        </div>
                        <p class="anchor">
                            <a href="video.html">WE黄炎发哈咖啡</a>
                            <a href="video.html">WE黄炎</a>
                        </p>
                    </li>
                    <li>
                        <p class="time"><span>意甲第24轮</span>02.10 03:45</p>
                        <div class="team">
                            <a href="video.html">直播中</a>
                            <p class="host"><img src="img/icon_teamDefault.png"><span>佛罗伦萨</span></p>
                            <p class="away"><img src="img/icon_teamDefault.png"><span>尤文图斯</span></p>
                        </div>
                        <p class="anchor">
                            <a href="video.html">WE黄炎发哈咖啡</a>
                            <a href="video.html">WE黄炎</a>
                        </p>
                    </li>
                    <li>
                        <p class="time"><span>意甲第24轮</span>02.10 03:45</p>
                        <div class="team">
                            <a href="video.html">直播中</a>
                            <p class="host"><img src="img/icon_teamDefault.png"><span>佛罗伦萨</span></p>
                            <p class="away"><img src="img/icon_teamDefault.png"><span>尤文图斯</span></p>
                        </div>
                        <p class="anchor">
                            <a href="video.html">WE黄炎发哈咖啡</a>
                            <a href="video.html">WE黄炎</a>
                        </p>
                    </li>
                </ul>
            </div>
        </div>
        @if(isset($anchors) && count($anchors) > 0)
            <div class="lbox" id="Live">
                <div class="title"><p>正在直播</p></div>
                <ul>
                    @foreach($livings as $living)
                        <?php
                        $anchor = $living->anchor;
                        ?>
                        <a class="li" target="_blank" href="{{$living['link']}}">
                            <div class="img"><img src="{{isset($living['cover']) ? $living['cover'] : '/pc/img/img_demo.png'}}"></div>
                            <img alt="{{$anchor['name']}}" src="{{isset($anchor['icon'])?$anchor['icon']:'/pc/img/icon_teamDefault.png'}}" class="face">
                            <p>{{$living['icon']}}</p>
                        </a>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(isset($anchors) && count($anchors) > 0)
            <div class="lbox" id="Anchor">
                <div class="title"><p>主播推荐</p></div>
                <ul>
                    @foreach($anchors as $anchor)
                        <a href="" class="li">
                            <img src="{{$anchor['icon']}}">
                            <p><span>{{$anchor['name']}}</span></p>
                        </a>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    <div id="Totop">
        <div class="abox">
            <a class="totop" href="javascript:void(0)"></a>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="/pc/css/anchor.css">
@endsection

@section('js')
    <script type="text/javascript">
        window.onload = function () {
            // setPage();
        }
    </script>
@endsection