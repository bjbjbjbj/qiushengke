@extends('pc.layout.base')
@section('content')
    <div id="Con">
        <div id="Info" class="football">
            <p class="info">-<br/>比赛时间：-</p>
            <p class="team host"><span class="img"><img src="/pc/img/icon_teamDefault.png"></span>-</p>
            <p class="team away"><span class="img"><img src="/pc/img/icon_teamDefault.png"></span>-</p>
            <p class="score">VS</p>
            <div class="odd">
                <p>亚：-&nbsp;&nbsp;-&nbsp;&nbsp;-</p>
                <p>欧：-&nbsp;&nbsp;-&nbsp;&nbsp;-</p>
                <p>大：-&nbsp;&nbsp;-&nbsp;&nbsp;-</p>
            </div>
        </div>
        <div id="Odd">
            <a target="_blank" class="match">【析】</a>
            <table id="Asia" style="display: none;">
                <colgroup>
                    <col num="1" width="15%">
                    <col num="2">
                    <col num="3">
                    <col num="4">
                    <col num="5">
                    <col num="6">
                    <col num="7">
                </colgroup>
                <thead>
                <tr>
                    <th rowspan="2">公司</th>
                    <th colspan="3">初盘</th>
                    <th colspan="3">终盘</th>
                </tr>
                <tr>
                    <th class="yellow">主队</th>
                    <th class="yellow">盘口</th>
                    <th class="yellow">客队</th>
                    <th class="green">主队</th>
                    <th class="green">盘口</th>
                    <th class="green">客队</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <table id="Europe" style="display: none;">
                <colgroup>
                    <col num="1" width="18%">
                    <col num="2">
                    <col num="3">
                    <col num="4">
                    <col num="5">
                    <col num="6">
                    <col num="7">
                </colgroup>
                <thead>
                <tr>
                    <th rowspan="2">公司</th>
                    <th colspan="3">初盘</th>
                    <th colspan="3">终盘</th>
                </tr>
                <tr>
                    <th class="yellow">主胜</th>
                    <th class="yellow">平局</th>
                    <th class="yellow">主负</th>
                    <th class="green">主胜</th>
                    <th class="green">平局</th>
                    <th class="green">主负</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <table id="Goal" style="display: none;">
                <colgroup>
                    <col num="1" width="17%">
                    <col num="2">
                    <col num="3">
                    <col num="4">
                    <col num="5">
                    <col num="6">
                    <col num="7">
                </colgroup>
                <thead>
                <tr>
                    <th rowspan="2">公司</th>
                    <th colspan="3">初盘</th>
                    <th colspan="3">终盘</th>
                </tr>
                <tr>
                    <th class="yellow">大球</th>
                    <th class="yellow">盘口</th>
                    <th class="yellow">小球</th>
                    <th class="green">大球</th>
                    <th class="green">盘口</th>
                    <th class="green">小球</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <div id="Play">
        <div class="abox">
            <ul>
                <li target="Asia">亚盘让分</li>
                <li target="Europe">欧盘</li>
                <li target="Goal">大小球</li>
            </ul>
        </div>
    </div>
@endsection

@section('navContent')
    @component('pc.layout.nav_content',['type'=>0])
    @endcomponent
    @component('pc.cell.top_leagues',['links'=>$footLeagues])
    @endcomponent
@endsection
@section('js')
    <script type="text/javascript" src="{{$cdn}}/pc/js/odd.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            setPage();
        }
    </script>
    <script type="text/javascript">
        var mid = GetQueryString('mid',window.location.href);
        var type = GetQueryString('type',window.location.href);
        var sport = GetQueryString('sport',window.location.href);
        if (type == ''){
            type = 1;
        }
        if (sport == ''){
            sport = 1;
        }

        //初始化ui
        var lis = $('div#Play li');
        for (var i = 0 ; i < 3 ; i++){
            var li = lis[i];
            if ((i + 1) == type) {
                li.className = 'on';
            }
        }
        if (type == 1){
            $('table#Asia')[0].style.display = '';
        }
        if (type == 2){
            $('table#Europe')[0].style.display = '';
        }
        if (type == 3){
            $('table#Goal')[0].style.display = '';
        }

        var url = matchPathWithId(mid,sport);

        $('div#Odd a')[0].href = url;

        //时间格式化
        function add0(m){return m<10?'0'+m:m }
        function format(string)
        {
            string = string*1000;
            var week = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];
            var time = new Date(string);
            var y = time.getFullYear();
            var m = time.getMonth()+1;
            var d = time.getDate();
            var h = time.getHours();
            var mm = time.getMinutes();
            var s = time.getSeconds();
            return y+'-'+add0(m)+'-'+add0(d)+' '+add0(h)+':'+add0(mm)+':'+add0(s) + ' ' + week[time.getDay()];
        }
        //刷新比赛
        function refreshMatch() {
            if (mid < 1000){
                return;
            }
            var first = mid.substr(0,2);
            var second = mid.substr(2,2);
            var url = '/static/terminal/'+sport+'/'+ first +'/'+ second +'/'+mid+'/match.json';
            url = '/test?url=' + '{{env('MATCH_URL')}}' + url;
            $.ajax({
                'url': url,
                'success': function (json) {
                    if (json) {
                        //比赛信息
                        var hicon = json['hicon'].length > 0 ? json['hicon'] : '/pc/img/icon_teamDefault.png';
                        var aicon = json['aicon'].length > 0 ? json['aicon'] : '/pc/img/icon_teamDefault.png';
                        var hname = json['hname'];
                        var aname = json['aname'];
                        var time = format(json['time']);
                        $('div#Info p.info').html(json['league'] + '<br>' + time);
                        $('div#Info p.host').html('<span class="img"><img src="'+hicon+'"></span>'+hname);
                        $('div#Info p.away').html('<span class="img"><img src="'+aicon+'"></span>'+aname);
                        //比分
                        if (json['status'] > 0 || json['status'] == -1) {
                            $('div#Info p.score').html(json['hscore'] + ' - ' + json['ascore']);
                        }
                        //赔率
                        var ps = $('div#Info div.odd p');
                        if (json['asiamiddle2']) {
                            $(ps[0]).html('亚：' + json['asiaup2'] + '&nbsp;&nbsp;' + json['asiamiddle2'] + '&nbsp;&nbsp;' + json['asiadown2']);
                        }
                        if (json['oumiddle2']) {
                            $(ps[1]).html('欧：' + json['ouup2'] + '&nbsp;&nbsp;' + json['oumiddle2'] + '&nbsp;&nbsp;' + json['oudown2']);
                        }
                        if (json['goalmiddle2']) {
                            $(ps[2]).html('大：' + json['goalup2'] + '&nbsp;&nbsp;' + json['goalmiddle2'] + '&nbsp;&nbsp;' + json['goaldown2']);
                        }
                        if (json['status'] >= 0){
                            window.setInterval('refreshOdd()', 5000);
                            window.setInterval('refreshMatch()', 5000);
                        }
                        var status = json['status'];
                        if (status >= 0){
                            window.setInterval('refreshOdd()', 5000);
                            window.setInterval('refreshMatch()', 5000);
                        }
                    }
                }
            });
        }
        //刷新赔率
        function refreshOdd() {
            var first = mid.substr(0,2);
            var second = mid.substr(2,2);
            var url = '/static/terminal/'+sport+'/'+ first +'/'+ second +'/'+mid+'/odd.json';
            url = '/test?url=' + '{{env('MATCH_URL')}}' + url;
            $.ajax({
                'url':url,
                'success':function (json) {
                    var keys = Object.keys(json);
                    if (keys.length > 0){
                        var tbodya = $('table#Asia tbody');
                        tbodya.html('');
                        var tbodyg = $('table#Goal tbody');
                        tbodyg.html('');
                        var tbodye = $('table#Europe tbody');
                        tbodye.html('');
                        for (var i = 0 ; i < keys.length ; i++){
                            var item = json[keys[i]];
                            var tr = '';
                            //亚盘
                            if (item['asia']){
                                var data = item['asia'];
                                tr = '<tr>'+
                                        '<td>'+item['name']+'</td>';
                                if(data['middle1']){
                                    tr = tr + '<td>'+data['up1']+'</td>'+
                                            '<td>'+getHandicapCn(data['middle1'],'',1,sport,true)+'</td>'+
                                            '<td>'+data['down1']+'</td>';
                                }
                                else{
                                    tr = tr +
                                            '<td>-</td>'+
                                            '<td>-</td>'+
                                            '<td>-</td>';
                                }
                                if(data['middle2']){
                                    tr = tr + '<td>'+data['up2']+'</td>'+
                                            '<td>'+getHandicapCn(data['middle2'],'',1,sport,true)+'</td>'+
                                            '<td>'+data['down2']+'</td>';
                                }
                                else{
                                    tr = tr +
                                            '<td>-</td>'+
                                            '<td>-</td>'+
                                            '<td>-</td>';
                                }
                                tr = tr + '</tr>';
                                tbodya.append(tr);
                            }

                            //欧盘
                            if (item['ou']){
                                var data = item['ou'];
                                tr = '<tr>'+
                                        '<td>'+item['name']+'</td>';
                                if(data['middle1']){
                                    tr = tr + '<td>'+data['up1']+'</td>'+
                                            '<td>'+data['middle1']+'</td>'+
                                            '<td>'+data['down1']+'</td>';
                                }
                                else{
                                    tr = tr +
                                            '<td>-</td>'+
                                            '<td>-</td>'+
                                            '<td>-</td>';
                                }
                                if(data['middle2']){
                                    tr = tr + '<td>'+data['up2']+'</td>'+
                                            '<td>'+data['middle2']+'</td>'+
                                            '<td>'+data['down2']+'</td>';
                                }
                                else{
                                    tr = tr +
                                            '<td>-</td>'+
                                            '<td>-</td>'+
                                            '<td>-</td>';
                                }
                                tr = tr + '</tr>';
                                tbodye.append(tr);
                            }

                            //大小球
                            if (item['goal']){
                                var data = item['goal'];
                                tr = '<tr>'+
                                        '<td>'+item['name']+'</td>';
                                if(data['middle1']){
                                    var pankou = getHandicapCn(data['middle1'],'',2,sport,true);
                                    pankou = pankou.replace('让','');
                                    tr = tr + '<td>'+data['up1']+'</td>'+
                                            '<td>'+pankou+'</td>'+
                                            '<td>'+data['down1']+'</td>';
                                }
                                else{
                                    tr = tr +
                                            '<td>-</td>'+
                                            '<td>-</td>'+
                                            '<td>-</td>';
                                }
                                if(data['middle2']){
                                    var pankou = getHandicapCn(data['middle2'],'',2,sport,true);
                                    pankou = pankou.replace('让','');
                                    tr = tr + '<td>'+data['up2']+'</td>'+
                                            '<td>'+pankou+'</td>'+
                                            '<td>'+data['down2']+'</td>';
                                }
                                else{
                                    tr = tr +
                                            '<td>-</td>'+
                                            '<td>-</td>'+
                                            '<td>-</td>';
                                }
                                tr = tr + '</tr>';
                                tbodyg.append(tr);
                            }
                        }
                    }
                }
            })
        }
        refreshOdd();
        refreshMatch();
//        window.setInterval('refreshOdd()', 5000);
//        window.setInterval('refreshMatch()', 5000);
    </script>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/pc/css/odd.css">
@endsection