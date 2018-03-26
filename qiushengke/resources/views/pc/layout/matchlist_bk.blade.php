@extends('pc.layout.base')
@section('navContent')
    @component('pc.layout.nav_content',['type'=>1])
    @endcomponent
    @component('pc.cell.top_leagues',['links'=>$basketLeagues])
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
        function changeBKFilter(type) {
            var url = window.location.href;
            if (type == 'league'){
                url = url.replace('_t.html','_l.html');
            }
            else{
                url = url.replace('_l.html','_t.html');
            }
            window.location.href = url;
        }
        
        var htmlPathType = 'bk_immediate';
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

        $('#hideMatchCount').html($('div.ConInner table[isMatch=1].hide').length);

        updateMatch();

        function _updateTimeLeagueFilterBtn(type) {
            var buttons = $('p.array button');
            for (var i = 0 ; i < buttons.length ; i++){
                var button = buttons[i];
                button.className = '';
            }
            var button = $('p.array button#array_'+type)[0];
            if (button)
                button.className = 'on';
        }

        //初始化比赛列表,哪些显示,哪些不显示
        function updateMatch() {
            //按时间 按赛事
            var type = 'time';
            var str = window.location.pathname;
            var index = str .lastIndexOf("\/");
            str  = str .substring(index + 1, str .length);
            str = str.replace('.html','');
            str = str.split('_');
            str = str[str.length - 1];
            if (str == 'l'){
                type = 'league';
            }
            else{
                type = 'time';
            }
            _updateTimeLeagueFilterBtn(type);

            //隐藏全部
            var bodys = $('div.ConInner table[isMatch=1]');
            for (var i = 0 ; i < bodys.length ; i++){
                bodys[i].className = 'hide';
            }
            //精选 竞彩 直播 全部
            var filter = getCookie(htmlPathType + '_' + 'filter');
            if (filter != null){
                _matchFilter(filter);
            }
            else{
                _matchFilter('nba');
            }
            //选择赛事
            filter = getCookie(htmlPathType + '_' + 'filter_league');
            if (filter != null){
                _updateMatchFilterBtn('null');
                _updateConfirmFilter('league',filter,false);
            }
            //选择盘口
//            filter = getCookie(htmlPathType + '_' + 'filter_odd');
//            if (filter != null){
//                _updateMatchFilterBtn('null');
//                _updateConfirmFilter('odd',filter,false);
//            }
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
            var matches = $('div.ConInner table[isMatch=1]');
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
            $('#hideMatchCount').html($('div.ConInner table[isMatch=1].hide').length);
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
            var matches = $('div.ConInner table[isMatch=1]');
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

            var matches = $('div.ConInner table[isMatch=1]');
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
                inputs = $("div.ConInner table[isMatch=1] button[name=match][value=1]");
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
                matches = $('div.ConInner table[isMatch=1]');
                for (var j = 0; j < matches.length; j++) {
                    matches[j].className = 'show';
                }
            }

            setCookie(htmlPathType + '_' + 'filter_' + type,valueStrs);
            matches = $('div.ConInner table[isMatch=1]');
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
            $('#hideMatchCount').html($('div.ConInner table[isMatch=1].hide').length);
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
        function changeSpanOdd(span,odd,isAsia,isMiddle) {
            if (span == null)
                return;
            if (odd) {
                var isUp = false;
                var isDown = false;
                if (odd > span.getAttribute('value')) {
                    span.setAttribute('class', 'up');
                    isUp = true;
                }
                else if (odd < span.getAttribute('value')) {
                    span.setAttribute('class', 'down');
                    isDown = true;
                }
                else{
                    span.setAttribute('class', '');
                }
                var text = odd;
                if (isMiddle) {
                    //亚盘
                    if (isAsia) {
                        text = getHandicapCn(odd, '',1,2,true);
                    }
                    //大小球
                    else {
                        text = getHandicapCn(odd, '',2,2,true);
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
            var url = "/static/change/2/roll.json?" + (new Date().getTime());
            url = '/test?url=' + '{{env('MATCH_URL')}}' + url;
            $.ajax({
                "url": url,
                "dataType": "json",
                "success": function (json) {
                    for (var ID in json) {
                        var dataItem = json[ID];
                        var asia = dataItem['all']['1'];
                        var goal = dataItem['all']['2'];
                        var table = $('#m_table_' + ID);

                        if (table && table.length > 0) {

                            var timeItem = $('#time_' + ID)[0];
                            if (timeItem && (timeItem.innerHTML == '' || timeItem.innerHTML == '已结束' || timeItem.innerHTML == '推迟')) {

                            }
                            else {
                                if (asia) {
                                    var value = asia['up'];
                                    var span = table.find('td.asia p')[0];
                                    changeSpanOdd(span, value, true, false);
                                    var value = asia['middle'];
                                    var span = table.find('td.asia span')[0];
                                    console.log(span);
                                    changeSpanOdd(span, value, true, true);
                                    var value = asia['down'];
                                    var span = table.find('td.asia p')[1];
                                    changeSpanOdd(span, value, true, false);
                                }
                                if (goal) {
                                    var value = goal['up'];
                                    var span = table.find('td.goal p')[0];
                                    changeSpanOdd(span, value, false, false);
                                    //主队
                                    var value = goal['middle'];
                                    var span = table.find('td.goal span')[0];
                                    changeSpanOdd(span, value, false, true);
                                    $(span).html('大 ' + span.innerHTML);
                                    //客队
                                    var value = goal['middle'];
                                    var span = table.find('td.goal span')[1];
                                    changeSpanOdd(span, value, false, true);
                                    $(span).html('小 ' + span.innerHTML);
                                    var value = goal['down'];
                                    var span = table.find('td.goal p')[1];
                                    changeSpanOdd(span, value, false, false);
                                }
                            }
                        }
                    }
                },
                "error": function () {

                }
            });
        }

        //主客比分刷新
        function _refreshBasketScore(dataItem,key,ID) {
            //比分
            var score = dataItem[key + 'scores'];
            var score1 = $('#'+ key + '_score1_' + ID);
            if (score1 && score1.length > 0) {
                score1.html(score[0]);
            }
            var score2 = $('#'+ key + '_score2_' + ID);
            if (score2 && score2.length > 0) {
                score2.html(score[1]);
            }
            var score3 = $('#'+ key + '_score3_' + ID);
            if (score3 && score3.length > 0) {
                score3.html(score[2]);
            }
            var score4 = $('#'+ key + '_score4_' + ID);
            if (score4 && score4.length > 0) {
                score4.html(score[3]);
            }

            //半全场
            var half = $('#'+key+'_score_half_' + ID);
            if (half && half.length > 0){
                half.html((parseInt(score[0]) + parseInt(score[1])) + ' / ' + (parseInt(score[2]) + parseInt(score[3])));
            }
            var full = $('#'+key+'_score_full_' + ID);
            if (full && full.length > 0){
                full.html((parseInt(score[0]) + parseInt(score[1])) + (parseInt(score[2]) + parseInt(score[3])));
            }

            //ot
            var h_ots = dataItem['h_ots'];
            var a_ots = dataItem['a_ots'];
            if (h_ots && a_ots) {
                var otCount = Math.min(h_ots.length, a_ots.length);
                if (otCount > 1) {
                    //改标题
                    var ths = $('#m_table_' + ID + ' th.th_name');
                    $(ths[0]).html('一');
                    $(ths[1]).html('二');
                    $(ths[2]).html('三');
                    $(ths[3]).html('四');
                }
                else if (otCount > 0) {
                }
                if (otCount > 0) {
                    var ots = dataItem[key + '_ots'];
                    var ot1 = $('#' + key + '_ot1_' + ID);
                    var th1 = $('#m_table_' + ID + ' th[name=ot_1]');
                    var th2 = $('#m_table_' + ID + ' th[name=ot_2]');
                    ot1.html(ots[0]);
                    ot1[0].className = 'score';
                    th1[0].className = '';
                    if (otCount > 1) {
                        var ot2 = $('#' + key + '_ot2_' + ID);
                        var tmp = 0;
                        for (var i = 1; i < ots.length; i++) {
                            tmp = parseInt(ots[i]);
                        }
                        ot2.html(tmp);
                        ot2[0].className = 'score';
                        th2[0].className = '';
                    }
                }
            }
        }

        //比分刷新
        function refresh() {
            if (htmlPathType !='bk_immediate')
                return;
            var url = "/static/change/2/score.json?" + (new Date().getTime());
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
                        var statusItem = $('#status_' + ID)[0];
                        var liveItem = $('#live_' + ID);

                        if (statusItem) {
                            if (dataItem.status > 0)
                                statusItem.className = 'live';
                            else{
                                statusItem.className = '';
                            }
                        }
                        if (timeItem) {
                            timeItem.html(dataItem.time);
                        }
                        _refreshBasketScore(dataItem,'h',ID);
                        _refreshBasketScore(dataItem,'a',ID);
                        if (liveItem && liveItem.length > 0){
                            //有直播
                            if(liveItem.find('img').length > 0 && dataItem.status > 0) {
                                liveItem[0].html('<span>直播中</span>');
                            }
                            //已结束
                            if (liveItem.find('img').length > 0 && dataItem.status == -1){
                                liveItem[0].html('<img src="/pc/img/icon_living.png">');
                            }
                        }

                        //分差
                        var hscore = dataItem['hscores'];
                        var ascore = dataItem['ascores'];
                        var hd = $('#score_half_diff_' + ID);
                        if (hd && hd.length > 0) {
                            hd.html('半：' + (parseInt(hscore[0]) + parseInt(hscore[1]) - parseInt(ascore[0]) - parseInt(ascore[1])));
                        }
                        var ht = $('#score_half_total_' + ID);
                        if (ht && ht.length > 0) {
                            ht.html('半：' + (parseInt(hscore[0]) + parseInt(hscore[1]) + parseInt(ascore[0]) + parseInt(ascore[1])));
                        }
                        var wd = $('#score_whole_diff_' + ID);
                        if (wd && wd.length > 0) {
                            wd.html('全：' + (parseInt(dataItem['hscore']) - parseInt(dataItem['ascore'])));
                        }
                        var wt = $('#score_whole_total_' + ID);
                        if (wt && wt.length > 0) {
                            wt.html('全：' + (parseInt(dataItem['hscore']) + parseInt(dataItem['ascore'])));
                        }

                        //迁移
                        if (dataItem.status == -1) {
                            var tbody = $('div.ConInner')[0];
                            var matchTr = document.getElementById('m_table_' + ID);
                            if (tbody && matchTr && matchTr.length > 0) {
                                tbody.appendChild(matchTr);
                            }
//                            _updateSection();
                        }
                    }
                },
                "error": function () {

                }
            });
        }
        if (htmlPathType == 'bk_immediate') {
            window.setInterval('refresh()', 5000);
            window.setInterval('refreshRoll()',5000);
        }

        //最终用盘口版本
        function getHandicapCn(handicap, defaultString, type, sport, isHome)
        {
            // console.log(handicap);
            handicap = parseFloat(handicap);
            if (sport == 1) {
                if (type == 1) {
                    return panKouText(handicap, !isHome);
                } else if (type == 2) {//大小球
                    if (handicap * 100 % 100 == 0) {
                        return handicap.toFixed(0);
                    }
                    handicap = handicap.toFixed(2);
                    handicap = parseFloat(handicap);
                    if (handicap * 100 % 50 == 0) {//尾数为0.5的直接返回
                        return handicap;
                    }
                    var tempHandicap = handicap.toFixed(0);//四舍五入
                    var intHandicap = parseInt(handicap);//取整
                    if (tempHandicap == intHandicap) {//比较 四舍五入 与 取整大小，尾数为 0.25 则为相同
                        return intHandicap + '/' + intHandicap + '.5';
                    } else {//否则尾数为0.75
                        return intHandicap + '.5/' + (intHandicap + 1);
                    }
                } else if (type == 3) {//竞彩
                    if (handicap > 0) {
                        return "+" + handicap;
                    } else if (handicap == 0) {
                        return "不让球";
                    } else {
                        return handicap;
                    }
                }
            } else if (sport == 2) {
                if (type == 1) {
                    //篮球
                    return BasketpanKouText(handicap, !isHome);
                } else if (type == 2) {//大小球
                    return ((handicap * 100 % 100 == 0) ? handicap.toFixed(0) : handicap.toFixed(2));
                } else if (type == 3) {//竞彩
                    if (handicap > 0) {
                        return "+" + handicap;
                    } else if (handicap == 0) {
                        return "不让分";
                    } else {
                        return handicap;
                    }
                } else if (type == 4) {
                    return ((handicap * 100 % 100 == 0) ? handicap.toFixed(0) : handicap.toFixed(2));
                }
            }
            return defaultString;
        }

        function BasketpanKouText(middle, isAway, isGoal) {
            var prefix = "";
            if (isGoal || middle == 0){
                prefix = "";
            } else{
                if (isAway){
                    prefix = middle < 0 ? "" : "-";
                }else{
                    prefix = middle < 0 ? "-" : "";
                }
            }
            return prefix + Math.abs(middle) + '分';
        }

        /**
         * 盘口中文转换
         * @param middle 盘口
         * @param isAway 是否客队
         * @returns {*}
         */
        function panKouText (middle, isAway) {
            if (isAway){
                var prefix = middle < 0 ? "让" : "受让";
            }else{
                var prefix = middle < 0 ? "受让" : "让";
            }

            if (middle == 0)
                prefix = '';

            var text = '';
            middle = Math.abs(middle);
            switch (middle) {
                case 7: text = "七球"; break;
                case 6.75: text = "六半/七球"; break;
                case 6.5: text = "六球半"; break;
                case 6.25: text = "六球/六半"; break;
                case 6: text = "六球"; break;
                case 5.75: text = "五半/六球"; break;
                case 5.5: text = "五球半"; break;
                case 5.25: text = "五球/五半"; break;
                case 5: text = "五球"; break;
                case 4.75: text = "四半/五球"; break;
                case 4.5: text = "四球半"; break;
                case 4.25: text = "四球/四半"; break;
                case 4: text = "四球"; break;
                case 3.75: text = "三半/四球"; break;
                case 3.5: text = "三球半"; break;
                case 3.25: text = "三球/三半"; break;
                case 3: text = "三球"; break;
                case 2.75: text = "两半/三球"; break;
                case 2.5: text = "两球半"; break;
                case 2.25: text = "两球/两半"; break;
                case 2: text = "两球"; break;
                case 1.75: text = "球半/两球"; break;
                case 1.5: text = "球半"; break;
                case 1.25: text = "一球/球半"; break;
                case 1: text = "一球"; break;
                case 0.75: text = "半/一"; break;
                case 0.5: text = "半球"; break;
                case 0.25: text = "平手/半球"; break;
                case 0: text = "平手"; break;
            }
            if (text.length > 0) {
                return prefix + text;
            }
            return text;
        }

        function _updateOddBody(key,ID,json) {
            var tbody = $('#'+ID + '_odd_'+key);
            _updateOdd(tbody,'a','1',json[key]['1'],'1');
            _updateOdd(tbody,'o','1',json[key]['3'],'1');
            _updateOdd(tbody,'g','1',json[key]['2'],'1');
            _updateOdd(tbody,'a','2',json[key]['1'],json[key]['1']['middle'] == null?'2':'');
            _updateOdd(tbody,'o','2',json[key]['3'],json[key]['2']['middle'] == null?'2':'');
            _updateOdd(tbody,'g','2',json[key]['2'],json[key]['3']['middle'] == null?'2':'');
        }

        //更新赔率小框 tbody全场半场 key类型a亚盘o欧赔g大小球 key21初盘2即时 data数据 key3数据拿那个
        function _updateOdd (tbody,key,key2,data,key3) {
            var p = tbody.find('p.' + key + 'up' + key2)[0];
            p.innerHTML = data['up'+key3];
            var p = tbody.find('p.' + key + 'mid' + key2)[0];
            var middle = data['middle'+key3];
            if ('a' == key){
                middle = getHandicapCn(middle, '',1,2,true);
            }
            else if('g' == key){
                middle = getHandicapCn(middle, '',2,2,true);
            }
            p.innerHTML = middle;
            var p = tbody.find('p.' + key + 'down' + key2)[0];
            p.innerHTML = data['down'+key3];
        }
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
                            <?php $leagueClass = ($league["isNBA"] ? "nba " : "")?>
                                <button mid="{{$league['id']}}" league="{{$leagueClass}}" class="item">{{$league["name"]}}({{$league["count"]}})</button>
                        @endforeach
                        <b class="letter">{{$key}}</b>
                    </li>
                @endforeach
            </ul>
            <div class="topBar">
                选择赛事
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
@endsection
