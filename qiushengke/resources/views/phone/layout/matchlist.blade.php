@extends('phone.layout.base')
@section('js')
    @yield('match_list_js')
    {{--点击filter用--}}
    <script type="text/javascript">
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
    </script>
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

        //多少场隐藏
        function updateHideCount() {
            $('#hideMatchCount').html($('div#List a.hide').length);
        }
        updateHideCount();
        updateMatch();
        //初始化比赛列表,哪些显示,哪些不显示
        function updateMatch() {
            //选择赛事
            var filter = getCookie(htmlPathType + '_' + 'filter_league');
            if (filter != null){
                _updateMatchFilterBtn('league','Self');
                _updateConfirmFilter('league',filter,false);
                //精选 竞彩 直播 全部
                var filter = getCookie(htmlPathType + '_' + 'filter');
                if (filter != null){
                    _updateMatchFilterBtn('league',filter);
                }
            }
            else {
                _matchFilter('first');
                _updateMatchFilterBtn('league','Important');
            }
            //选择盘口
            var filter = getCookie(htmlPathType + '_' + 'filter_odd');
            if (filter != null){
                _updateMatchFilterBtn('odd','Self');
                _updateConfirmFilter('odd',filter,false);
            }
        }

        //比赛类型 竞彩 直播 完整
        function matchFilter(type) {
            //清空
            delCookie(htmlPathType + '_' + 'filter');
            delCookie(htmlPathType + '_' + 'filter_odd');
            delCookie(htmlPathType + '_' + 'filter_league');
            setCookie(htmlPathType + '_' + 'filter',type);
            _matchFilter(type);
        }

        //更新点击filter后的UI,初始化用版
        function _matchFilter(type) {
            _updateMatchFilterBtn(type);
            var matches = $('div#List a[isMatch=1]');
            for (var j = 0; j < matches.length; j++) {
                //跳过广告
                if (matches[j].getAttribute('match') == null) continue;
                if (type == "all") {
                    matches[j].className = 'default show';
                } else {
                    var trAttr = matches[j].getAttribute(type);
                    if (trAttr == type) {
                        matches[j].className = 'default show';
                    } else {
                        matches[j].className = 'default hide';
                    }
                }
            }
            updateHideCount();
        }

        //更新比赛类型按钮
        function _updateMatchFilterBtn(type,tabStr) {
            if (type == 'league'){
                if ('Important' == tabStr){
                    $('p.in.league').html('重要赛事');
                }
                else if ('Lottery' == tabStr){
                    $('p.in.league').html('竞彩赛事');
                }
                else if ('All' == tabStr){
                    $('p.in.league').html('全部赛事');
                }
                else if ('Self' == tabStr){
                    $('p.in.league').html('自定义');
                }
                $('p.in.odd').html('全部盘口');
            }
            else if(type == 'odd'){
                $('p.in.odd').html('自定义');
                $('p.in.league').html('全部赛事');
            }
        }

        //根据选中赛事 赔率大小 保留筛选还原
        function _updateConfirmFilter(type,valueStrs) {
            var matches = $('div#List a[isMatch=1]');
            for (var j = 0; j < matches.length; j++) {
                var trAttr = '';

                if (type == "odd") {
                    trAttr = type + "_" + matches[j].getAttribute('asiaOdd');
                    var tempAttr = type + "_" + matches[j].getAttribute('ouOdd');

                    if (valueStrs.indexOf('odd_asia') != -1 && valueStrs.indexOf('odd_ou') != -1) {
                        //大小球 亚盘 交集
                        if (valueStrs.indexOf(trAttr) != -1 && valueStrs.indexOf(tempAttr) != -1) {
                            matchItemShow(matches[j], true)
                        } else {
                            matchItemShow(matches[j], false);
                        }
                    }
                    else {
                        //独立
                        if (valueStrs.indexOf(trAttr) != -1 || valueStrs.indexOf(tempAttr) != -1) {
                            matchItemShow(matches[j], true);
                        } else {
                            matchItemShow(matches[j], false);
                        }
                    }
                } else if('league' == type) {
                    trAttr = type + "_" + matches[j].getAttribute(type) + ',';
                    if (valueStrs.indexOf(trAttr) == -1) {
                        matchItemShow(matches[j], false);
                    } else {
                        matchItemShow(matches[j], true);
                    }
                }
            }

            //还原赛事筛选状态
            if ('league' == type){
                var inputs = $("#LeagueFilter ul#Self li input");
                for (var i = 0 ; i < inputs.length ; i++){
                    var key = 'league_'+inputs[i].getAttribute('mid')+',';
                    if (valueStrs.indexOf(key) != -1) {
                        inputs[i].checked = true;
                    }
                    else{
                        inputs[i].checked = false;
                    }
                }
            }

            console.log(valueStrs);
            //还原盘口筛选状态
            if ('odd' == type){
                var inputs = $("#OddFilter ul li input");
                for (var i = 0 ; i < inputs.length ; i++){
                    var key = inputs[i].getAttribute('mid')+',';
                    if (valueStrs.indexOf(key) != -1) {
                        inputs[i].checked = true;
                    }
                    else{
                        inputs[i].checked = false;
                    }
                }
            }
        }

        //保留/删除:match 筛选赔率:odd赛事:league确认
        function confirmFilter(type) {
            //选中的条件
            var inputs;
            var showLive = false;
            var tabStr = 'Important';
            if (type == 'league'){
                tabStr = 'Important';
                showLive = $('input#liveOnlyLeague')[0].checked;
                //选中哪个tab 重要 竞彩 全部 自定义
                if ('' == $("div#LeagueFilter #Important")[0].style.display){
                    tabStr = 'Important';
                    setCookie(htmlPathType + '_' + 'filter','Important');
                }
                else if ('' == $("div#LeagueFilter #Lottery")[0].style.display){
                    tabStr = 'Lottery';
                    setCookie(htmlPathType + '_' + 'filter','Lottery');
                }
                else if ('' == $("div#LeagueFilter #All")[0].style.display){
                    tabStr = 'All';
                    setCookie(htmlPathType + '_' + 'filter','All');
                }
                else if ('' == $("div#LeagueFilter #Self")[0].style.display){
                    tabStr = 'Self';
                }

                inputs = $("#LeagueFilter div.default ul#"+tabStr+" li input:checked");
            } else if (type == 'odd'){
                showLive = $('input#liveOnlyGoal')[0].checked;
                tabStr = 'Goal';
                if ('' == $("div#OddFilter #Goal")[0].style.display){
                    tabStr = 'Goal';
                }
                else if ('' == $("div#OddFilter #Asia")[0].style.display){
                    tabStr = 'Asia';
                }
                inputs = $("#OddFilter div.default ul#"+tabStr+" li input:checked");
                setCookie(htmlPathType + '_' + 'filter','All');
            }
            delCookie(htmlPathType + '_' + 'filter_league');
            delCookie(htmlPathType + '_' + 'filter_odd');

            //没有返回
            if (inputs.length == 0) return;

            var valueStrs = '';

            //把输出转换成cookies保存字符串
            for (var i = 0; i < inputs.length; i++) {
                valueStrs += type + "_" + inputs[i].getAttribute('mid') + ",";
            }

            var matches;
            //非保留删除的,先重置所有比赛是显示
            if (valueStrs == '') {
                //重置
                matches = $('div#List a[isMatch=1]');
                for (var j = 0; j < matches.length; j++) {
                    matches[j].className = 'default show';
                }
            }

            setCookie(htmlPathType + '_' + 'filter_' + type,valueStrs);
            matches = $('div#List a[isMatch=1]');

            for (var j = 0; j < matches.length; j++) {
                var trAttr = '';

                if (type == "odd") {
                    trAttr = type + "_" + matches[j].getAttribute('asiaOdd');
                    var tempAttr = type + "_" + matches[j].getAttribute('ouOdd');

                    if (valueStrs.indexOf('odd_asia') != -1 && valueStrs.indexOf('odd_ou') != -1) {
                        //大小球 亚盘 交集
                        if (valueStrs.indexOf(trAttr) != -1 && valueStrs.indexOf(tempAttr) != -1) {
                            matchItemShow(matches[j], true)
                        } else {
                            matchItemShow(matches[j], false);
                        }
                    }
                    else {
                        //独立
                        if (valueStrs.indexOf(trAttr) != -1 || valueStrs.indexOf(tempAttr) != -1) {
                            matchItemShow(matches[j], true);
                        } else {
                            matchItemShow(matches[j], false);
                        }
                    }
                }
                else if('league' == type) {
                    trAttr = type + "_" + matches[j].getAttribute(type) + ',';
                    if (valueStrs.indexOf(trAttr) == -1) {
                        matchItemShow(matches[j], false);
                    } else {
                        matchItemShow(matches[j], true);
                    }
                }
            }

            //选择只显示有直播
            if (showLive){
                matches = $('div#List a.show[isMatch=1]');
                for (var j = 0; j < matches.length; j++) {
                    if (matches[j].getAttribute('live') == 'live') {
                        matchItemShow(matches[j], true);
                    }
                    else{
                        matchItemShow(matches[j], false);
                    }
                }
            }

            updateHideCount();
            _closeFilter();
            //精简的按钮不能选中
            _updateMatchFilterBtn(type,tabStr);
        }

        //是否显示比赛
        function matchItemShow(matchItem, isShow) {
            matchItem.className = isShow?'default show':'default hide';
        }

        //关闭筛选框
        function _closeFilter() {
            $('#LeagueFilter').css('display','none');
            $('#OddFilter').css('display','none');
        }
    </script>
    @endsection
@section('content')
    @yield('match_list_content')
    <div class="filterBox" id="LeagueFilter" style="display: none;">
        <div class="default">
            <div class="tab">
                <input type="radio" name="leagueFilter" id="LeagueImportant" value="Important" checked><label for="LeagueImportant">重要赛事</label>
                <input type="radio" name="leagueFilter" id="LeagueLottery" value="Lottery"><label for="LeagueLottery">竞彩赛事</label>
                <input type="radio" name="leagueFilter" id="LeagueAll" value="All"><label for="LeagueAll">全部赛事</label>
                <input type="radio" name="leagueFilter" id="LeagueSelf" value="Self"><label for="LeagueSelf">自定义</label>
            </div>
            @foreach($filter as $key=>$leagues)
                <ul id="{{$key}}"
                    @if($key != 'Important')
                    style="display: none;"
                        @endif
                >
                    @foreach($leagues as $league)
                        <li><input mid="{{$league['id']}}" type="checkbox" name="league" id="{{$key}}_{{$league['id']}}"
                                   @if('Self' == $key)
                                   checked
                                   @else
                                   disabled checked
                                    @endif
                            ><label for="{{$key}}_{{$league['id']}}">{{$league['name']}}</label></li>
                        @endforeach
                </ul>
            @endforeach
            <div class="comfirmLine">
                <input type="checkbox" name="liveOnly" id="liveOnlyLeague">
                <label for="liveOnlyLeague">只显示有直播信号</label>
                <button class="comfirm" onclick="confirmFilter('league')">确认</button>
            </div>
            <button class="close"></button>
        </div>
    </div>
    <div class="filterBox" id="OddFilter" style="display: none;">
        <div class="default">
            <div class="tab">
                <input type="radio" name="oddFilter" id="OddAsia" value="Asia" checked><label for="OddAsia">亚盘</label>
                <input type="radio" name="oddFilter" id="OddGoal" value="Goal"><label for="OddGoal">大小球</label>
            </div>

            @if(
                (isset($odd['asiaOdds']['up']) && count($odd['asiaOdds']['up']) > 0) ||
                (isset($odd['asiaOdds']['middle']) && count($odd['asiaOdds']['middle']) > 0) ||
                (isset($odd['asiaOdds']['down']) && count($odd['asiaOdds']['down']) > 0)
                )
                <ul id="Asia">
                    @if(isset($odd['asiaOdds']['middle']) && count($odd['asiaOdds']['middle']) > 0)
                        @foreach($odd['asiaOdds']['middle'] as $item)
                            <li><input mid="{{'asiaMiddle_'.$item['sort']}}" type="checkbox" name="league" id="{{'asiaMiddle_'.$item['sort']}}" checked><label for="{{'asiaMiddle_'.$item['sort']}}">{{$item['typeCn']}}</label></li>
                        @endforeach
                            <div class="line"></div>
                    @endif
                    @if(isset($odd['asiaOdds']['up']) && count($odd['asiaOdds']['up']) > 0)
                        @foreach($odd['asiaOdds']['up'] as $item)
                                <li><input mid="{{'asiaUp_'.$item['sort']}}" type="checkbox" name="league" id="{{'asiaUp_'.$item['sort']}}" checked><label for="{{'asiaUp_'.$item['sort']}}">{{$item['typeCn']}}</label></li>
                        @endforeach
                            <div class="line"></div>
                    @endif
                    @if(isset($odd['asiaOdds']['down']) &&count($odd['asiaOdds']['down']) > 0)
                        @foreach($odd['asiaOdds']['down'] as $item)
                                <li><input mid="{{'asiaDown_'.$item['sort']}}" type="checkbox" name="league" id="{{'asiaDown_'.$item['sort']}}" checked><label for="{{'asiaDown_'.$item['sort']}}">{{$item['typeCn']}}</label></li>
                        @endforeach
                    @endif
                </ul>
            @endif
            @if(count($odd['ouOdds']) > 0)
                <ul id="Goal" style="display: none;">
                    @if(isset($odd['ouOdds']['-1']))
                        <?php
                        $item = $odd['ouOdds']['-1'];
                        ?>
                        <li><input type="checkbox" name="league" id="{{'ou_'.$item['sort']}}" mid="{{'ou_'.$item['sort']}}" checked><label for="{{'ou_'.$item['sort']}}">{{$item['typeCn']}}</label></li>
                            <div class="line"></div>
                    @endif
                    @foreach($odd['ouOdds'] as $key=>$item)
                        @if($key != '-1')
                                <li><input type="checkbox" name="league" id="{{'ou_'.$item['sort']}}" mid="{{'ou_'.$item['sort']}}" checked><label for="{{'ou_'.$item['sort']}}">{{$item['typeCn']}}</label></li>
                        @endif
                    @endforeach
                </ul>
            @endif
            <div class="comfirmLine">
                <input type="checkbox" name="liveOnly" id="liveOnlyGoal">
                <label for="liveOnlyGoal">只显示有直播信号</label>
                <button class="comfirm" onclick="confirmFilter('odd')">确认</button>
            </div>
            <button class="close"></button>
        </div>
    </div>
    @component('phone.layout.bottom',['index'=>0,'cdn'=>$cdn])
    @endcomponent
@endsection
