<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta charset="utf-8" />
    <meta content="telephone=no,email=no" name="format-detection" />
    <meta name="viewport" content="width=device-width, initial-scale=0.5, maximum-scale=0.5, minimum-scale=0.5, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/phone/css/style.css">
    @yield('css')
    <link rel="Shortcut Icon" data-ng-href="{{$cdn}}/phone/img/ico.ico" href="{{$cdn}}/phone/img/ico.ico">
    <link href="img/icon_face.png" sizes="100x100" rel="apple-touch-icon-precomposed">
    <style>
        .hide {
            display: none;
        }
        .show {
            display: block;
        }
    </style>
    <script type="text/javascript" src="{{$cdn}}/phone/js/jquery.js"></script>
    <script type="text/javascript" src="{{$cdn}}/phone/js/public.js"></script>
    <title>球胜客</title>
</head>
<body>
@yield('content')
</body>
<script type="text/javascript">
    window.onload = function () {
        setPage();
    }
</script>
@yield('js')
</html>

