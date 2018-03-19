@extends('pc.layout.matchdetail_base')
@section('navContent')
    <div class="home"><p class="abox"><a href="index.html"><img src="/pc/img/logo_image_n.png"></a></p></div>
    <div class="Column">
        <a class="on" href="/match/foot/immediate.html">足球</a>
        <a href="/match/basket/immediate_t.html">篮球</a>
        <a href="">主播</a>
        <a href="">手机APP</a>
    </div>
    @component('pc.cell.top_leagues',['links'=>$footLeagues])
    @endcomponent
@endsection
@section('content')
    <div id="Con">
        @component('pc.match_detail.foot_cell.head',['match'=>$match,'analyse'=>$analyse,'rank'=>$analyse['rank']])
        @endcomponent
        @component('pc.match_detail.foot_cell.base',['match'=>$match,'rank'=>$analyse['rank'],'tech'=>$tech,'lineup'=>$lineup])
        @endcomponent
        @component('pc.match_detail.foot_cell.character',['match'=>$match,'analyse'=>$analyse])
        @endcomponent
        @component('pc.match_detail.foot_cell.data',['match'=>$match,'analyse'=>$analyse])
        @endcomponent
        @component('pc.match_detail.foot_cell.corner',['match'=>$match,'analyse'=>$analyse])
        @endcomponent
    </div>
    <div id="Play">
        <div class="abox">
            <ul>
                <li class="on" target="Match">比赛赛况</li>
                <li target="Character">特色数据</li>
                <li target="Data">数据分析</li>
                <li target="Corner">角球数据</li>
            </ul>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="/pc/js/match.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            setPage();
        }
    </script>
    <script type="text/javascript">
        //刷新比赛
        function refreshMatch() {
            var mid = '{{$match['mid']}}';
            var first = mid.substr(0,2);
            var second = mid.substr(2,2);
            var url = '/static/terminal/1/'+ first +'/'+ second +'/'+mid+'/match.json';
//            url = 'http://localhost:8000/static/terminal/1/10/20/1020697/match.json';
            $.ajax({
                'url': url,
                'success': function (json) {
                    //比分
                    if (json['status'] > 0 || json['status'] == -1) {
                        $('div#Info p.score').html(json['hscore'] + ' - ' + json['ascore']);
                    }
                    //赔率
                    var ps = $('div#Info div.odd p');
                    if (json['asiamiddle2']){
                        $(ps[0]).html('亚：'+json['asiaup2']+'&nbsp;&nbsp;'+json['asiamiddle2']+'&nbsp;&nbsp;'+json['asiadown2']);
                    }
                    if (json['oumiddle2']){
                        $(ps[1]).html('欧：'+json['ouup2']+'&nbsp;&nbsp;'+json['oumiddle2']+'&nbsp;&nbsp;'+json['oudown2']);
                    }
                    if (json['goalmiddle2']){
                        $(ps[2]).html('大：'+json['goalup2']+'&nbsp;&nbsp;'+json['goalmiddle2']+'&nbsp;&nbsp;'+json['goaldown2']);
                    }
                    //角球
                    if(json['cornermiddle2'] || json['cornermiddle1']) {
                        var div = $('div#Corner div.odd');
                        div[0].style.display = '';
                        var tds = div.find('table td');
                        if(json['cornermiddle1']){
                            $(tds[1]).html(json['cornerup1']);
                            $(tds[2]).html(json['cornermiddle1']);
                            $(tds[3]).html(json['cornerdown1']);
                        }
                        if(json['cornermiddle2']){
                            $(tds[5]).html(json['cornerup2']);
                            $(tds[6]).html(json['cornermiddle2']);
                            $(tds[7]).html(json['cornerdown2']);
                        }
                    }
                }
            });
        }
        //刷新赔率
        function refreshOdd() {
            var mid = '{{$match['mid']}}';
            var first = mid.substr(0,2);
            var second = mid.substr(2,2);
            var url = '/static/terminal/1/'+ first +'/'+ second +'/'+mid+'/odd.json';
//            url = 'http://localhost:8000/static/terminal/1/10/20/1020697/odd.json';
            $.ajax({
                'url':url,
                'success':function (json) {
                    var keys = Object.keys(json);
                    if (keys.length > 0){
                        $('div#Data div.odd')[0].style.display = '';
                        var tbody = $('div#Data div.odd table tbody');
                        tbody.html('')
                        for (var i = 0 ; i < keys.length ; i++){
                            var item = json[keys[i]];
                            if(2 != item['id'] && 5 != item['id'] && 12 != item['id']){
                                continue;
                            }
                            var tr = '<tr>'+
                                    '<td rowspan="2">'+item['name']+'</td>'+
                                    '<td>初盘</td>';
                            if (item['ou']){
                                tr = tr +
                                        '<td>'+item['ou']['up1']+'</td>'+
                                        '<td>'+item['ou']['middle1']+'</td>'+
                                        '<td>'+item['ou']['down1']+'</td>';
                            }
                            else{
                                tr = tr +
                                        '<td>-</td>'+
                                        '<td>-</td>'+
                                        '<td>-</td>';
                            }
                            if (item['asia']){
                                tr = tr +
                                        '<td>'+item['asia']['up1']+'</td>'+
                                        '<td>'+panKouText(item['asia']['middle1'],false)+'</td>'+
                                        '<td>'+item['asia']['down1']+'</td>'+
                                        '<td>'+(parseFloat(item['asia']['up1'])+parseFloat(item['asia']['down1'])).toFixed(2)+'</td>';
                            }
                            else{
                                tr = tr +
                                        '<td>-</td>'+
                                        '<td>-</td>'+
                                        '<td>-</td>'+
                                        '<td>-</td>';
                            }
                            if (item['goal']){
                                tr = tr +
                                        '<td>'+item['goal']['up1']+'</td>'+
                                        '<td>'+panKouText(item['goal']['middle1'],false)+'</td>'+
                                        '<td>'+item['goal']['down1']+'</td>'+
                                        '<td>'+(parseFloat(item['goal']['up1'])+parseFloat(item['goal']['down1'])).toFixed(2)+'</td>';
                            }
                            else{
                                tr = tr +
                                        '<td>-</td>'+
                                        '<td>-</td>'+
                                        '<td>-</td>'+
                                        '<td>-</td>';
                            }

                            tr = tr+'</tr>';
                            //初盘
                            tbody.append(tr);

                            tr = '<tr>'+
                                    '<td>终盘</td>';
                            if (item['ou']){
                                tr = tr +
                                        '<td>'+item['ou']['up2']+'</td>'+
                                        '<td>'+item['ou']['middle2']+'</td>'+
                                        '<td>'+item['ou']['down2']+'</td>';
                            }
                            else{
                                tr = tr +
                                        '<td>-</td>'+
                                        '<td>-</td>'+
                                        '<td>-</td>';
                            }
                            if (item['asia']){
                                tr = tr +
                                        '<td>'+item['asia']['up2']+'</td>'+
                                        '<td>'+panKouText(item['asia']['middle2'],false)+'</td>'+
                                        '<td>'+item['asia']['down2']+'</td>'+
                                        '<td>'+(parseFloat(item['asia']['down2'])+parseFloat(item['asia']['up2'])).toFixed(2)+'</td>';
                            }
                            else{
                                tr = tr +
                                        '<td>-</td>'+
                                        '<td>-</td>'+
                                        '<td>-</td>'+
                                        '<td>-</td>';
                            }
                            if (item['goal']){
                                tr = tr +
                                        '<td>'+item['goal']['up2']+'</td>'+
                                        '<td>'+panKouText(item['goal']['middle2'],false)+'</td>'+
                                        '<td>'+item['goal']['down2']+'</td>'+
                                        '<td>'+(parseFloat(item['goal']['down2'])+parseFloat(item['goal']['up2'])).toFixed(2)+'</td>';
                            }
                            else{
                                tr = tr +
                                        '<td>-</td>'+
                                        '<td>-</td>'+
                                        '<td>-</td>'+
                                        '<td>-</td>';
                            }
                            tr = tr+'</tr>';
                            //终盘
                            tbody.append(tr);
                        }
                    }
                }
            })
        }
        refreshOdd();
        refreshMatch();
        if ('{{$match['status']}}' == -1){

        }
        else{
            window.setInterval('refreshOdd()', 5000);
            window.setInterval('refreshMatch()', 5000);
        }
    </script>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="/pc/css/match.css">
@endsection