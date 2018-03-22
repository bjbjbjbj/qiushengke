<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>料狗数据后台</title>
    <!-- Bootstrap -->
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/toastr.min.css" rel="stylesheet">
    @yield('css')
</head>
<body>
<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">

    </button>
    <div class="navbar-header">
        <a class="navbar-brand" href="/">爬虫管理后台</a>
    </div>
</nav>

<div class="col-sm-3 col-md-2 sidebar">
    <ul class="nav nav-sidebar nav-public">
        <li><a href="/admin/spider/manager">爬虫管理</a></li>
        <li><a href="/admin/spider/lotteryManager">竞彩爬虫管理</a></li>
    </ul>
</div>
@yield('content')
@yield('extra_content')
</body>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="//cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="/js/bootstrap.min.js"></script>
<script src="/js/toastr.js"></script>
<script>
    $(function () {
        var path = location.pathname;
        var currA = $("a[href='" + path + "']");
        if (currA) {
            currA.parent().css({"background-color": "#EEEEEE"});
        }
    });
</script>
@yield('js')
</html>
