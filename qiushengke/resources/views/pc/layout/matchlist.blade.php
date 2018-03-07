@extends('pc.layout.base')
@section('navContent')
    <div class="Link">
        <a href="league.html">中超</a>
        <a href="league.html">英超</a>
        <a href="league.html">西甲</a>
        <a href="league.html">意甲</a>
        <a href="league.html">法甲</a>
        <a href="league.html">德甲</a>
        <a href="league.html">亚冠</a>
        <a href="league.html">欧冠</a>
        <a href="league.html">世界杯</a>
    </div>
@endsection
@section('css')
    @yield('css_match_list')
    <style>
        .hide {
            display: none;
        }
        .show {

        }
    </style>
    @endsection
@section('js')
    @yield('js_match_list')
    <script type="text/javascript">
        var htmlPathType = 'immediate';
        if (window.location.pathname.indexOf('result') != -1){
            var str = window.location.pathname;
            var index = str .lastIndexOf("\/");
            str  = str .substring(index + 1, str .length);
            str = str.replace('.html','');
            var params = str.split("_")[1];
            htmlPathType = params;
        }
        if (window.location.pathname.indexOf('schedule') != -1){
            var str = window.location.pathname;
            var index = str .lastIndexOf("\/");
            str  = str .substring(index + 1, str .length);
            str = str.replace('.html','');
            var params = str.split("_")[1];
            htmlPathType = params;
        }

        $('#hideMatchCount').html($('table#Table tr.hide').length);

        updateMatch();

        //初始化比赛列表,哪些显示,哪些不显示
        function updateMatch() {
            //隐藏全部
            var bodys = $('table#Table tbody[name=match]');
            for (var i = 0 ; i < bodys.length ; i++){
                bodys[i].className = 'hide';
            }
            //精选 竞彩 直播 全部
            var filter = getCookie(htmlPathType + '_' + 'filter');
            if (filter != null){
                _matchFilter(filter);
            }
            else{
                _matchFilter('first');
            }
            //选择赛事
            filter = getCookie(htmlPathType + '_' + 'filter_league');
            if (filter != null){
                _updateMatchFilterBtn('null');
                _updateConfirmFilter('league',filter,false);
            }
            //选择盘口
            filter = getCookie(htmlPathType + '_' + 'filter_odd');
            if (filter != null){
                _updateMatchFilterBtn('null');
                _updateConfirmFilter('odd',filter,false);
            }
            //选择保留删除
            _resetFilterUser('match',filter);
        }

        //设置cookies
        function setCookie(name,value)
        {
            var exp = new Date();
            exp.setTime(exp.getTime() + 2*60*60*1000);
            document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
        }

        //读取cookies
        function getCookie(name)
        {
            var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");

            if(arr=document.cookie.match(reg))

                return unescape(arr[2]);
            else
                return null;
        }

        //删除cookies
        function delCookie(name)
        {
            var exp = new Date();
            exp.setTime(exp.getTime() - 1);
            var cval=getCookie(name);
            if(cval!=null)
                document.cookie= name + "="+cval+";expires="+exp.toGMTString();
        }

        //比赛类型 竞彩 直播 完整
        function matchFilter(type) {
            //清空
            delCookie(htmlPathType + '_' + 'filter');
            delCookie(htmlPathType + '_' + 'filter_odd');
            delCookie(htmlPathType + '_' + 'filter_league');
            delCookie(htmlPathType + '_' + 'filter_hide');
            delCookie(htmlPathType + '_' + 'filter_show');
            setCookie(htmlPathType + '_' + 'filter',type);
            _matchFilter(type);
        }

        //更新点击filter后的UI,初始化用版
        function _matchFilter(type) {
            _updateMatchFilterBtn(type);
            var matches = $('table#Table tr[isMatch=1]');
            for (var j = 0; j < matches.length; j++) {
                //跳过广告
                if (matches[j].getAttribute('match') == null) continue;
                if (type == "all") {
                    matches[j].className = 'show';
                } else {
                    var trAttr = matches[j].getAttribute(type);
                    if (trAttr == type) {
                        matches[j].className = 'show';
                    } else {
                        matches[j].className = 'hide';
                    }
                }
            }
            $('#hideMatchCount').html($('table#Table tr.hide').length);
//            $('#totalMatchCount').html(totalMatchCount);

            //还原列表类型 进行中 稍后等显示方式
            _updateSection();
        }

        //更新比赛类型按钮
        function _updateMatchFilterBtn(type) {
            var buttons = $('p.column button');
            for (var i = 0 ; i < buttons.length ; i++){
                var button = buttons[i];
                button.className = '';
            }
            var button = $('p.column button#column_'+type)[0];
            if (button)
                button.className = 'on';
        }

        //还原列表类型 进行中 稍后等显示方式
        function _updateSection() {
            _updateTbody('Top');
            _updateTbody('Live');
            _updateTbody('After');
            _updateTbody('End');
        }

        //更新比赛列表section是否显示
        function _updateTbody(className) {
            //还原列表类型 进行中 稍后等显示方式
            if ($('tbody#'+className)[0]) {
                var matches = $('tbody#'+className)[0].getElementsByTagName('tr');
                $('tbody#'+className)[0].className = 'hide';
                for (var j = 0; j < matches.length; j++) {
                    //跳过广告
                    if (matches[j].getAttribute('match') == null) continue;
                    if (matches[j].className == 'show') {
                        $('tbody#'+className)[0].className = 'show';
                    }
                }
            }
        }

        //赛事筛选
        function leagueFilter(leagueClass) {
            var tabButtons = $("#LeagueFilter").find('.topBar .league');
            var selectTab;
            switch (leagueClass) {
                case 'first':
                    selectTab = 0;
                    break;
                case 'five':
                    selectTab = 1;
                    break;
                default:
                    selectTab = 2;
                    break;
            }
            for (var i = 0; i < tabButtons.length; i++) {
                if (i == selectTab) {
                    tabButtons[i].value = 1;
                } else {
                    tabButtons[i].value = 0;
                }
            }

            var itemButtons = $("#LeagueFilter").find('.item');
            for (var i = 0; i < itemButtons.length; i++) {
                if (leagueClass == 'all') {
                    itemButtons[i].value = 1;
                } else {
                    var leagueAttr = itemButtons[i].getAttribute('league');
                    if (leagueAttr.indexOf(leagueClass) == -1) {
                        itemButtons[i].value = 0;
                    } else {
                        itemButtons[i].value = 1;
                    }
                }
            }
        }

        //根据选中赛事 赔率大小 保留筛选还原
        function _updateConfirmFilter(type,valueStrs,isDelete) {
            var matches = $('table#Table tr[isMatch=1]');
            for (var j = 0; j < matches.length; j++) {
                var trAttr = '';

                if (type == "odd") {
                    trAttr = type + "_" + matches[j].getAttribute('asiaOdd');
                    var tempAttr = type + "_" + matches[j].getAttribute('ouOdd');

                    if (valueStrs.indexOf('odd_asia') != -1 && valueStrs.indexOf('odd_ou') != -1) {
                        //大小球 亚盘 交集
                        if (valueStrs.indexOf(trAttr) != -1 && valueStrs.indexOf(tempAttr) != -1) {
                            matchItemShow(matches[j], !isDelete)
                        } else {
                            matchItemShow(matches[j], isDelete);
                        }
                    }
                    else {
                        //独立
                        if (valueStrs.indexOf(trAttr) != -1 || valueStrs.indexOf(tempAttr) != -1) {
                            matchItemShow(matches[j], !isDelete);
                        } else {
                            matchItemShow(matches[j], isDelete);
                        }
                    }
                } else if('league' == type) {
                    trAttr = type + "_" + matches[j].getAttribute(type) + ',';
                    if (valueStrs.indexOf(trAttr) == -1) {
                        matchItemShow(matches[j], isDelete);
                    } else {
                        matchItemShow(matches[j], !isDelete);
                    }
                }
            }

            //还原赛事筛选状态
            if ('league' == type){
                var inputs = $("#LeagueFilter div.inner ul li button");
                for (var i = 0 ; i < inputs.length ; i++){
                    var key = 'league_'+inputs[i].getAttribute('mid')+',';
                    if (valueStrs.indexOf(key) != -1) {
                        inputs[i].value = 1;
                    }
                }
            }

            //还原盘口筛选状态
            if ('odd' == type){
                var inputs = $("#OddFilter div.inner ul li button");
                for (var i = 0 ; i < inputs.length ; i++){
                    var key = inputs[i].getAttribute('mid')+',';
                    if (valueStrs.indexOf(key) != -1) {
                        inputs[i].value = 1;
                    }
                }
            }
        }

        //还原保留删除
        function _resetFilterUser() {
            var type = 'match';
            var hideIds = getCookie(htmlPathType + '_' + 'filter_hide');
            if (null == hideIds){
                hideIds = '';
            }
            var showIds = getCookie(htmlPathType + '_' + 'filter_show');
            if (null == showIds){
                showIds = '';
            }

            //没有保留删除返回
            if (showIds == '' && hideIds == ''){
                return;
            }

            _updateMatchFilterBtn('null');

            var matches = $('table#Table tr[isMatch=1]');
            for (var j = 0; j < matches.length; j++) {
                ///先算保留
                var trAttr = type + "_" + matches[j].getAttribute(type) + ',';
                if (showIds.length > 0 && showIds.indexOf(trAttr) != -1) {
                    matchItemShow(matches[j], true);
                }else{
                    matchItemShow(matches[j], false);
                }
                //再算删除
                if (hideIds.length > 0 && hideIds.indexOf(trAttr) != -1) {
                    matchItemShow(matches[j], false);
                }
            }
        }

        //保留/删除:match 筛选赔率:odd赛事:league确认
        function confirmFilter(type, isDelete) {
            //选中的条件
            var inputs;
            if (type == 'match') {
                inputs = $("table#Table tr[isMatch=1] td button[name=match][value=1]");
            } else if (type == 'league'){
                inputs = $("#LeagueFilter div.inner ul li button[value=1]");
                delCookie(htmlPathType + '_' + 'filter_league');
                delCookie(htmlPathType + '_' + 'filter_odd');
            } else if (type == 'odd'){
                inputs = $("#OddFilter div.inner div.item button[value=1]");
                delCookie(htmlPathType + '_' + 'filter_league');
                delCookie(htmlPathType + '_' + 'filter_odd');
            }

            //没有返回
            if (inputs.length == 0) return;

            var valueStrs = '';
            var hideIds = getCookie(htmlPathType + '_' + 'filter_hide');
            if (null == hideIds){
                hideIds = '';
            }
            var showIds = getCookie(htmlPathType + '_' + 'filter_show');
            if (null == showIds){
                showIds = '';
            }

            //把输出转换成cookies保存字符串
            for (var i = 0; i < inputs.length; i++) {
                valueStrs += type + "_" + inputs[i].getAttribute('mid') + ",";
                //记录保留 删除
                if ('match' == type){
                    //删除
                    if (isDelete){
                        hideIds += type + "_" + inputs[i].getAttribute('mid') + ",";
                    }
                    else{
                        showIds += type + "_" + inputs[i].getAttribute('mid') + ",";
                    }
                }
            }

            //记录保留 删除
            if ('match' == type){
                //删除
                if (isDelete){
                    setCookie(htmlPathType + '_' + 'filter_hide',hideIds);
                }
                else{
                    setCookie(htmlPathType + '_' + 'filter_show',showIds);
                }
            }
            var matches;
            //非保留删除的,先重置所有比赛是显示
            if (type != 'match' && valueStrs == '') {
                //重置
                matches = $('table#Table tr[isMatch=1]');
                for (var j = 0; j < matches.length; j++) {
                    matches[j].className = 'show';
                }
            }

            setCookie(htmlPathType + '_' + 'filter_' + type,valueStrs);
            matches = $('table#Table tr[isMatch=1]');
            for (var j = 0; j < matches.length; j++) {
                var trAttr = '';

                if (type == "odd") {
                    trAttr = type + "_" + matches[j].getAttribute('asiaOdd');
                    var tempAttr = type + "_" + matches[j].getAttribute('ouOdd');

                    if (valueStrs.indexOf('odd_asia') != -1 && valueStrs.indexOf('odd_ou') != -1) {
                        //大小球 亚盘 交集
                        if (valueStrs.indexOf(trAttr) != -1 && valueStrs.indexOf(tempAttr) != -1) {
                            matchItemShow(matches[j], !isDelete)
                        } else {
                            matchItemShow(matches[j], isDelete);
                        }
                    }
                    else {
                        //独立
                        if (valueStrs.indexOf(trAttr) != -1 || valueStrs.indexOf(tempAttr) != -1) {
                            matchItemShow(matches[j], !isDelete);
                        } else {
                            matchItemShow(matches[j], isDelete);
                        }
                    }
                } else if('match' != type) {
                    trAttr = type + "_" + matches[j].getAttribute(type) + ',';
                    if (valueStrs.indexOf(trAttr) == -1) {
                        matchItemShow(matches[j], isDelete);
                    } else {
                        matchItemShow(matches[j], !isDelete);
                    }
                }

                //保留删除是继承现有的继续处理
                if ('match' == type){
                    ///先算保留
                    trAttr = type + "_" + matches[j].getAttribute(type) + ',';
                    if (showIds.length > 0 && showIds.indexOf(trAttr) != -1) {
                        matchItemShow(matches[j], true);
                    }else{
                        matchItemShow(matches[j], false);
                    }
                    //再算删除
                    if (hideIds.length > 0 && hideIds.indexOf(trAttr) != -1) {
                        matchItemShow(matches[j], false);
                    }
                }
                else{
                    delCookie(htmlPathType + '_' + 'filter_show');
                    delCookie(htmlPathType + '_' + 'filter_hide');
                }
            }
            $('#hideMatchCount').html($('table#Table tr[isMatch=1].hide').length);
            _closeFilter();
            _updateSection();
            //精简的按钮不能选中
            _updateMatchFilterBtn('league');
        }

        //是否显示比赛
        function matchItemShow(matchItem, isShow) {
            matchItem.className = isShow?'show':'hide';
        }

        //关闭筛选框
        function _closeFilter() {
            $('#LeagueFilter').css('display','none');
            $('#OddFilter').css('display','none');
        }

        //点击比赛前面的√
        function clickMatchBtn(btn) {
            btn.value = btn.value == 1 ? 0 :1;
        }

        //全选
        function clickAll(button) {
            if (button.value == 0){
                button.value = 1;
            }
            else{
                button.value = 0;
            }
            var buttons = $('button[name=match]');
            for (var j = 0; j < buttons.length; j++) {
                buttons[j].value = button.value;
            }
        }

        //声音开关
        function SoundControl () {
            var SoundBtn = $('div#Control .sound button')[0];

            var checked = false;
            if (SoundBtn.innerHTML == '进球声') {
                checked = true;
            }else{

            }
            setCookie('sound',checked);
        }

    </script>
    {{--动态加载--}}
    <script type="text/javascript">
        var ct;
        //动态比赛统计
        function refreshMatchTech(ID){
            window.clearInterval(ct);
            ID = ID + '';
            var first = ID.substr(0,2);
            var second = ID.substr(2,2);
            $.ajax({
                "url": '{{env('MATCH_URL')}}' + "/static/terminal/1/"+first+"/"+second+"/"+ID+"/tech.json",
//                "url":"/static/terminal/1/"+first+"/"+second+"/"+ID+"/tech.json",
                "dataType": "json",
                "success": function (json) {
                    window.clearInterval(ct);
                    //事件
                    var event = document.getElementById(ID+'_eboxCon');
                    if (typeof(event) != 'undefined') {
                        var content = '';
                        var events = json['event']['events'];
                        for (var i = 0 ; i < events.length ; i++){
                            var item = events[i];
                            var icon = '';
                            switch (item.kind){
                                case 1:
                                case 7:
                                    icon = '/pc/img/icon_goal_n.png';
                                    break;
                                case 2:
                                    icon = '/pc/img/icon_redcard_n.png';
                                    break;
                                case 3:
                                    icon = '/pc/img/icon_yellowcard_n.png';
                                    break;
                                case 8:
                                    icon = '/pc/img/icon_goal_n.png';
                                    break;
                                case 11:
                                    icon = '/pc/img/icon_xchange_n.png';
                                    break;
                            }
                            if(item.kind == 11){
                                content = content +
                                        '<dd class="'+ (item['is_home'] == 1 ? 'host' : 'away') +'">'+
                                        '<p class="time">'+item['happen_time']+'\'</p>'+
                                        '<p class="img"><img src="' + icon + '"></p>'+
                                        '<p class="name exchange">'+item['player_name_j'] + '</br>' + item['player_name_j2']+'</p>'+
                                        '</dd>';
                            }
                            else{
                                content = content +
                                        '<dd class="'+ (item['is_home'] == 1 ? 'host' : 'away') +'">'+
                                        '<p class="time">'+item['happen_time']+'\'</p>'+
                                        '<p class="img"><img src="' + icon + '"></p>'+
                                        '<p class="name">'+item['player_name_j']+'</p>'+
                                        '</dd>';
                            }
                        }
                        var html = '<dl class="ebox"><dt><p>事件</p></dt>' +
                                content +
                                '</dl>';
                        event.innerHTML = html;
                    }
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
                    window.clearInterval(ct);
                }
            });
        }

        function refresh() {
            if (htmlPathType !='immediate')
                return;
            $.ajax({
                "url": "http://localhost:8000/static/score.json?" + (new Date().getTime()),
                "dataType": "json",
                "success": function (json) {
                    var ups = $('span.up');
                    for (var i = 0 ; i < ups.length; i++){
                        var up = ups[i];
                        up.setAttribute('class','');
                    }
                    var downs = $('span.down');
                    for (var i = 0 ; i < downs.length; i++){
                        var down = downs[i]
                        down.setAttribute('class','');
                    }

                    for (var ID in json) {
                        var dataItem = json[ID];
                        var timeItem = $('#time_' + ID);
                        var scoreItem = $('#score_' + ID);
                        var halfScoreItem = $('#half_score_' + ID);
                        var chScoreItem = $('#ch_score_' + ID);
                        var liveItem = $('#live_' + ID);

                        //红黄牌
                        var hrItem = $('#'+ID+'_h_red')[0];
                        var hyItem = $('#'+ID+'_h_yellow')[0];
                        var arItem = $('#'+ID+'_a_red')[0];
                        var ayItem = $('#'+ID+'_a_yellow')[0];

                        if (hrItem && dataItem.h_red > 0){
                            hrItem.innerHTML = dataItem.h_red;
                            hrItem.className = 'redCard';
                        }
                        if (hyItem && dataItem.h_yellow > 0){
                            hyItem.innerHTML = dataItem.h_yellow;
                            hyItem.className = 'yellowCard';
                        }
                        if (arItem && dataItem.a_red > 0){
                            arItem.innerHTML = dataItem.a_red;
                            arItem.className = 'redCard';
                        }
                        if (ayItem && dataItem.a_yellow > 0){
                            ayItem.innerHTML = dataItem.a_yellow;
                            ayItem.className = 'yellowCard';
                        }

                        if (timeItem) {
                            if(dataItem.status > 0)
                                timeItem.html('<p class=\'time\'>' + dataItem.time + '</p>');
                            else
                                timeItem.html(dataItem.time);
                        }
                        if (scoreItem) {
                            var lastScore = scoreItem.html();
                            var currentScore = dataItem.hscore + ' - ' + dataItem.ascore;
                            var isHost = true;
                            if (lastScore.indexOf(' - ') != -1) {
                                var lh = lastScore.split(' - ')[0];
                                var ah = lastScore.split(' - ')[1];
                                if (lh == dataItem.hscore){
                                    isHost = false;
                                }
                            }
                            var icon = isHost ? $('#'+ID+'_h_icon')[0].src : $('#'+ID+'_a_icon')[0].src;
                            if (currentScore != lastScore) {
                                Goal(dataItem.hname, dataItem.aname, dataItem.hscore, dataItem.ascore, icon, isHost?'host':'away');
                            }
                            scoreItem.html(currentScore);
                        }
                        if (chScoreItem) {
                            chScoreItem.html(dataItem.h_corner + ' - ' + dataItem.a_corner);
                        }
                        if (halfScoreItem) {
                            halfScoreItem.html(dataItem.hscorehalf + ' - ' + dataItem.ascorehalf);
                        }
                        if (liveItem && liveItem.length > 0){
                            if(liveItem[0].src == "{{env('CDN_URL')}}/pc/img/icon_living.png") {
                                liveItem[0].src = "{{env('CDN_URL')}}/pc/img/icon_living.gif";
                            }
                            if (timeItem == '已结束'){
                                liveItem[0].src = "{{env('CDN_URL')}}/pc/img/icon_lived.png";
                            }
                        }
                    }
                },
                "error": function () {

                }
            });
        }
//        window.setInterval('refresh()',5000);
        refresh();
    </script>
    @endsection
@section('content')
    <div id="Con">
        @yield('match_list_content')
    </div>
    <div id="Date">
        @yield('match_list_date')
    </div>
    <div id="Totop">
        <div class="abox">
            <a class="totop" href="javascript:void(0)"></a>
        </div>
    </div>
    <div id="LeagueFilter" class="filterBox" style="display: none;">
        <div class="inner">
            <ul>
                @foreach($filter as $key=>$leagues)
                    <li>
                        @foreach($leagues as $league)
                            <?php $leagueClass = ($league["isFive"] ? "five " : "").($league["isFirst"] ? "first" : "") ?>
                                <button mid="{{$league['id']}}" league="{{$leagueClass}}" class="item">{{$league["name"]}}({{$league["count"]}})</button>
                        @endforeach
                        <b class="letter">{{$key}}</b>
                    </li>
                @endforeach
            </ul>
            <div class="topBar">
                选择赛事
                <button class="league" value="0" onclick="leagueFilter('first')">一级联赛</button><!--套界面时添加这里的事件-->
                <button class="league" value="0" onclick="leagueFilter('five')">五大联赛</button><!--套界面时添加这里的事件-->
                <button class="close"></button>
            </div>
            <div class="bottomBar">
                <button class="all">全选</button>
                <button class="opposite">反选</button>
                <button class="comfirm" onclick="confirmFilter('league', false)">确认</button><!--选项为空时有disabled效果--><!--套界面时添加这里的事件-->
            </div>
        </div>
    </div>
    <div id="OddFilter" class="filterBox" style="display: none;">
        <div class="inner">
            <div class="item" type="asia">
                @if(count($odd['asiaOdds']['up']) > 0 || count($odd['asiaOdds']['middle'])> 0 || count($odd['asiaOdds']['down']) > 0)
                    <ul class="asia">
                        @if(count($odd['asiaOdds']['middle']) > 0)
                            @foreach($odd['asiaOdds']['middle'] as $item)
                                <li><button mid="{{'asiaMiddle_'.$item['sort']}}">{{$item['typeCn']}}({{$item['count']}})</button></li>
                            @endforeach
                            <p class="clear"></p>
                        @endif
                        @if(count($odd['asiaOdds']['up']) > 0)
                            @foreach($odd['asiaOdds']['up'] as $item)
                                <li><button mid="{{'asiaUp_'.$item['sort']}}">{{$item['typeCn']}}({{$item['count']}})</button></li>
                            @endforeach
                            <p class="clear"></p>
                        @endif
                        @if(count($odd['asiaOdds']['down']) > 0)
                            @foreach($odd['asiaOdds']['down'] as $item)
                                <li><button mid="{{'asiaDown_'.$item['sort']}}">{{$item['typeCn']}}({{$item['count']}})</button></li>
                            @endforeach
                            <p class="clear"></p>
                        @endif
                    </ul>
                @endif
                @if(count($odd['ouOdds']) > 0)
                    <ul class="goal">
                        @if(isset($odd['ouOdds']['-1']))
                            <?php
                            $item = $odd['ouOdds']['-1'];
                            ?>
                            <li><button mid="{{'ou_'.$item['sort']}}">{{$item['typeCn']}}({{$item['count']}})</button></li>
                            <p class="clear"></p>
                        @endif
                        @foreach($odd['ouOdds'] as $key=>$item)
                            @if($key != '-1')
                                <li><button mid="{{'ou_'.$item['sort']}}">{{$item['typeCn']}}({{$item['count']}})</button></li>
                            @endif
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="topBar">
                选择盘路
                <button class="odd goal" value="0">大小球</button>
                <button class="odd asia" value="1">让球</button>
                <button class="close"></button>
            </div>
            <div class="bottomBar">
                <button class="all">全选</button>
                <button class="opposite">反选</button>
                <button class="comfirm" onclick="confirmFilter('odd', false)">确认</button><!--选项为空时有disabled效果-->
            </div>
        </div>
    </div>
@endsection
