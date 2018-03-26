@extends('pc.layout.base')
@section('navContent')
    @component('pc.layout.nav_content',['type'=>0])
    @endcomponent
    @component('pc.cell.top_leagues',['links'=>$footLeagues])
    @endcomponent
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
            htmlPathType = str;
        }
        if (window.location.pathname.indexOf('schedule') != -1){
            var str = window.location.pathname;
            var index = str .lastIndexOf("\/");
            str  = str .substring(index + 1, str .length);
            str = str.replace('.html','');
            htmlPathType = str;
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

            setBG();
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
            setBG();
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
            setBG();
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

            setBG();
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
            var url = "/static/terminal/1/"+first+"/"+second+"/"+ID+"/tech.json";
            url = '/test?url=' + '{{env('MATCH_URL')}}' + url;
            $.ajax({
                "url": url,
//                "url":"/static/terminal/1/"+first+"/"+second+"/"+ID+"/tech.json",
                "dataType": "json",
                "success": function (json) {
                    window.clearInterval(ct);
                    //事件
                    var event = document.getElementById(ID+'_eboxCon');
                    if (typeof(event) != 'undefined') {
                        var content = '';
                        if (json['event']) {
                            var events = json['event']['events'];
                            for (var i = 0; i < events.length; i++) {
                                var item = events[i];
                                var icon = '';
                                switch (parseInt(item.kind)) {
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
                                if (item.kind == 11) {
                                    content = content +
                                            '<dd class="' + (item['is_home'] == 1 ? 'host' : 'away') + '">' +
                                            '<p class="time">' + item['happen_time'] + '\'</p>' +
                                            '<p class="img"><img src="' + icon + '"></p>' +
                                            '<p class="name exchange">' + item['player_name_j'] + '</br>' + item['player_name_j2'] + '</p>' +
                                            '</dd>';
                                }
                                else {
                                    content = content +
                                            '<dd class="' + (item['is_home'] == 1 ? 'host' : 'away') + '">' +
                                            '<p class="time">' + item['happen_time'] + '\'</p>' +
                                            '<p class="img"><img src="' + icon + '"></p>' +
                                            '<p class="name">' + item['player_name_j'] + '</p>' +
                                            '</dd>';
                                }
                            }
                            var html = '<dl class="ebox"><dt><p>事件</p></dt>' +
                                    content +
                                    '</dl>';
                            event.innerHTML = html;
                        }
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

        function changeSpanOdd(span,odd,isAsia,isMiddle) {
            if (span == null)
                return;
            if (odd) {
                var isUp = false;
                var isDown = false;
                if (odd > span.getAttribute('value')) {
                    if (isMiddle) {
                        span.setAttribute('class', 'odd up');
                    }
                    else{
                        span.setAttribute('class', 'up');
                    }
                    isUp = true;
                }
                else if (odd < span.getAttribute('value')) {
                    if (isMiddle) {
                        span.setAttribute('class', 'odd down');
                    }
                    else{
                        span.setAttribute('class', 'down');
                    }
                    isDown = true;
                }
                else{
                    if (isMiddle) {
                        span.setAttribute('class', 'odd');
                    }
                    else{
                        span.setAttribute('class', '');
                    }
                }
                var text = odd;
                if (isMiddle) {
                    //亚盘
                    if (isAsia) {
                        text = getHandicapCn(odd, '',1,1,true);
                    }
                    //大小球
                    else {
                        text = getHandicapCn(odd, '',2,1,true);
                    }
                }
                else{
                    if (isUp){
                        text = text;
                    }
                    else if(isDown){
                        text = text;
                    }
                }
                $(span).html(text);
                span.setAttribute('value', odd);
            }
        }

        //赔率刷新
        function refreshRoll() {
            var url = "/static/change/1/roll.json?" + (new Date().getTime());
            url = '/test?url=' + '{{env('MATCH_URL')}}' + url;
            $.ajax({
                "url": url,
                "dataType": "json",
                "success": function (json) {
                    for (var ID in json) {
                        var dataItem = json[ID];
                        var asia = dataItem['all']['1'];
                        var goal = dataItem['all']['2'];
                        var asiaP = $('tr#m_tr_' + ID + ' p.asia')[0];
                        var goalP = $('tr#m_tr_' + ID + ' p.goal')[0];
                        var timeItem = $('#time_' + ID)[0];
                        if (timeItem && (timeItem.innerHTML == '' || timeItem.innerHTML == '已结束' || timeItem.innerHTML == '推迟')){

                        }
                        else {
                            if (asia) {
                                var value = asia['up'];
                                var span = $(asiaP).find('span')[0];
                                changeSpanOdd(span, value,true,false);
                                var value = asia['middle'];
                                var span = $(asiaP).find('span')[1];
                                changeSpanOdd(span, value,true,true);
                                var value = asia['down'];
                                var span = $(asiaP).find('span')[2];
                                changeSpanOdd(span, value,true,false);
                            }
                            if (goal) {
                                var value = goal['up'];
                                var span = $(goalP).find('span')[0];
                                changeSpanOdd(span, value,false,false);
                                var value = goal['middle'];
                                var span = $(goalP).find('span')[1];
                                changeSpanOdd(span, value,false,true);
                                var value = goal['down'];
                                var span = $(goalP).find('span')[2];
                                changeSpanOdd(span, value,false,false);
                            }
                        }
                    }
                },
                "error": function () {

                }
            });
        }

        //比分刷新
        function refresh() {
            if (htmlPathType !='immediate')
                return;
            var url = "/static/change/1/score.json?" + (new Date().getTime());
            url = '/test?url=' + '{{env('MATCH_URL')}}' + url;
            $.ajax({
                "url": url,
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
                        if (scoreItem && scoreItem.length > 0) {
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
                            if (lastScore.indexOf(currentScore) == -1 && (dataItem.hscore + dataItem.ascore) > 0) {
                                var tmpTR = $('tr#m_tr_' + ID)[0];
                                if (tmpTR.className.indexOf('show') != -1)
                                    Goal(dataItem.hname, dataItem.aname, dataItem.hscore, dataItem.ascore, icon,dataItem.time.replace('\'',''), isHost?'host':'away');
                            }
                            scoreItem.html(currentScore);
                        }
                        if (chScoreItem) {
                            chScoreItem.html(dataItem.h_corner + ' - ' + dataItem.a_corner);
                        }
                        if (halfScoreItem) {
                            halfScoreItem.html('半 ' + dataItem.hscorehalf + ' - ' + dataItem.ascorehalf);
                        }
                        if (liveItem && liveItem.length > 0){
                            if(liveItem[0].src == "{{env('CDN_URL')}}/pc/img/icon_living.png") {
                                liveItem[0].src = "{{env('CDN_URL')}}/pc/img/icon_living.gif";
                            }
                            if (timeItem == '已结束'){
                                liveItem[0].src = "{{env('CDN_URL')}}/pc/img/icon_lived.png";
                            }
                        }

                        if (dataItem.time == '已结束') {
                            var tbody = $('tbody#End')[0];
                            var matchTr = document.getElementById('m_tr_' + ID);
                            tbody.appendChild(matchTr);
                            var liveItem = $('#live_' + ID);
                            if (liveItem && liveItem.length > 0){
                                liveItem[0].src = "{{env('CDN_URL')}}/pc/img/icon_lived.png";
                            }
                            _updateSection();
                        }
                    }
                },
                "error": function () {

                }
            });
        }
        if (htmlPathType == 'immediate') {
            window.setInterval('refresh()', 5000);
            window.setInterval('refreshRoll()',5000);
        }

        //比赛对应赔率详情
        var ct2;
        //动态比赛统计
        function refreshOddByMid(ID){
            window.clearInterval(ct2);
            ID = ID + '';
            var first = ID.substr(0,2);
            var second = ID.substr(2,2);
            var url = "/static/terminal/1/"+first+"/"+second+"/"+ID+"/roll.json";
            url = '/test?url=' + '{{env('MATCH_URL')}}' + url;
            $.ajax({
                {{--                "url": '{{env('MATCH_URL')}}' + "/static/terminal/1/"+first+"/"+second+"/"+ID+"/tech.json",--}}
                "url":url,
                "dataType": "json",
                "success": function (json) {
                    window.clearInterval(ct2);
                    //全场/半场
                    _updateOddBody('all',ID,json);
                    _updateOddBody('half',ID,json);
                },
                "error": function () {
                    window.clearInterval(ct2);
                }
            });
        }

        function _updateOddBody(key,ID,json) {
            var tbody = $('#'+ID + '_odd_'+key);
            if (json[key]) {
                _updateOdd(tbody, 'a', '1', json[key]['1'], '1');
                _updateOdd(tbody, 'o', '1', json[key]['3'], '1');
                _updateOdd(tbody, 'g', '1', json[key]['2'], '1');
                _updateOdd(tbody, 'a', '2', json[key]['1'], json[key]['1']['middle'] == null ? '2' : '');
                _updateOdd(tbody, 'o', '2', json[key]['3'], json[key]['2']['middle'] == null ? '2' : '');
                _updateOdd(tbody, 'g', '2', json[key]['2'], json[key]['3']['middle'] == null ? '2' : '');
            }
        }

        //更新赔率小框 tbody全场半场 key类型a亚盘o欧赔g大小球 key21初盘2即时 data数据 key3数据拿那个
        function _updateOdd (tbody,key,key2,data,key3) {
            var middle = data['middle'+key3];
            if (middle) {
                var p = tbody.find('p.' + key + 'up' + key2)[0];
                p.innerHTML = data['up'+key3];
                var p = tbody.find('p.' + key + 'mid' + key2)[0];
                if ('a' == key) {
                    middle = getHandicapCn(middle, '', 1, 1, true);
                }
                else if ('g' == key) {
                    middle = getHandicapCn(middle, '', 2, 1, true);
                }
                p.innerHTML = middle;
                var p = tbody.find('p.' + key + 'down' + key2)[0];
                p.innerHTML = data['down' + key3];
            }
        }

        jQuery(function(){
            $.datepicker.setDefaults( $.datepicker.regional[ "zh-TW" ] );

            $.datepicker.regional['zh-TW'] = {
                closeText: '关闭',
                prevText: '&#x3C;上月',
                nextText: '下月&#x3E;',
                currentText: '今天',
                monthNames: ['一月','二月','三月','四月','五月','六月',
                    '七月','八月','九月','十月','十一月','十二月'],
                monthNamesShort: ['一月','二月','三月','四月','五月','六月',
                    '七月','八月','九月','十月','十一月','十二月'],
                dayNames: ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],
                dayNamesShort: ['周日','周一','周二','周三','周四','周五','周六'],
                dayNamesMin: ['日','一','二','三','四','五','六'],
                weekHeader: '周',
                dateFormat: 'yy/mm/dd',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: true,
                yearSuffix: '年'};
            $.datepicker.setDefaults($.datepicker.regional['zh-TW']);
        });
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
                <p>已选择<span>0</span>项赛事</p>
            </div>
        </div>
    </div>
    <div id="OddFilter" class="filterBox" style="display: none;">
        <div class="inner">
            <div class="item" type="asia">
                @if(
                (isset($odd['asiaOdds']['up']) && count($odd['asiaOdds']['up']) > 0) ||
                (isset($odd['asiaOdds']['middle']) && count($odd['asiaOdds']['middle']) > 0) ||
                (isset($odd['asiaOdds']['down']) && count($odd['asiaOdds']['down']) > 0)
                )
                        <ul class="asia">
                            @if(isset($odd['asiaOdds']['middle']) && count($odd['asiaOdds']['middle']) > 0)
                                @foreach($odd['asiaOdds']['middle'] as $item)
                                    <li><button mid="{{'asiaMiddle_'.$item['sort']}}">{{$item['typeCn']}}({{$item['count']}})</button></li>
                                @endforeach
                                <p class="clear"></p>
                            @endif
                            @if(isset($odd['asiaOdds']['up']) && count($odd['asiaOdds']['up']) > 0)
                                @foreach($odd['asiaOdds']['up'] as $item)
                                    <li><button mid="{{'asiaUp_'.$item['sort']}}">{{$item['typeCn']}}({{$item['count']}})</button></li>
                                @endforeach
                                <p class="clear"></p>
                            @endif
                            @if(isset($odd['asiaOdds']['down']) &&count($odd['asiaOdds']['down']) > 0)
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
                <p>已选择<span>0</span>个盘口</p>
            </div>
        </div>
    </div>
@endsection
