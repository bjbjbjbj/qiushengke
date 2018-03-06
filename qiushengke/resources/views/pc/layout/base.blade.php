<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta charset="UTF-8">
    <title>球胜客</title>
    <meta name="Keywords" content="">
    <meta name="Description" content="">
    <meta http-equiv="X-UA-Compatible" content="edge" />
    <meta name="renderer" content="webkit|ie-stand|ie-comp">
    <meta name="baidu-site-verification" content="nEdUlBWvbw">
    <link rel="Shortcut Icon" data-ng-href="/pc/img/ico.ico" href="/pc/img/ico.ico">
    <link rel="stylesheet" type="text/css" href="/pc/css/style.css">
    @yield('css')
</head>
<body>
<div id="Nav">
    <div class="home"><p class="abox"><a href="index.html"><img src="img/logo_image_n.png"></a></p></div>
    <div class="Column">
        <a class="on">足球</a>
        <a href="immediate_bk.html">篮球</a>
        <a href="">主播</a>
        <a href="">手机APP</a>
    </div>
    @yield('navContent')
</div>
@yield('content')
<div id="Bottom">
    <p>友情链接：<a href="">料狗商城</a><a href="">cctv5在线直播</a><a href="">258直播网</a><a href="">料狗TV</a><a href="">世界杯直播</a><a href="">5播体育</a></p>
    <p>免责声明：本站所有直播和视频链接均由网友提供，如有侵权问题，请及时联系，我们将尽快处理。</p>
</div>
</body>
<script type="text/javascript" src="/pc/js/jquery.js"></script>
<!--[if lte IE 8]>
<script type="text/javascript" src="/pc/js/jquery_191.js"></script>
<![endif]-->
<script type="text/javascript" src="/pc/js/jquery-ui.js"></script>
@yield('js')
</html>