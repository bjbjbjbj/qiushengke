@extends('pc.layout.matchdetail_base')
@section('navContent')
    @component('pc.layout.nav_content',['type'=>0])
    @endcomponent
@endsection
@section('content')
    <?php
    $rank = isset($analyse['rank']) ? $analyse['rank'] : null;
    ?>
    @component('pc.match_detail.foot_cell.head',['match'=>$match,'analyse'=>$analyse,'rank'=>$rank])
    @endcomponent
    <div id="Con">
        {{--@component('pc.match_detail.foot_cell.base',['match'=>$match,'rank'=>$rank,'tech'=>$tech,'lineup'=>$lineup])--}}
        {{--@endcomponent--}}
        {{--@component('pc.match_detail.foot_cell.character',['match'=>$match,'analyse'=>$analyse])--}}
        {{--@endcomponent--}}
        @component('pc.match_detail.foot_cell.data',['match'=>$match,'analyse'=>$analyse])
        @endcomponent
        @component('pc.match_detail.foot_cell.odd_data',['cur_match'=>$match,'analyse'=>$analyse])
        @endcomponent
    </div>
    <div id="Play">
        <div class="abox">
            <ul>
                <li class="on" target="Data">数据分析</li>
                <li target="Odd">综合指数</li>
                <?php
                $liveUrl = \App\Http\Controllers\PC\CommonTool::matchLivePathWithId($match['mid'],1);
                ?>
                <a class="li" href="{{$liveUrl}}">比赛直播</a>
                <!-- <li target="Match">比赛赛况</li> -->
                <!-- <li target="Character">特色数据</li> -->
                <!-- <li target="Corner">角球数据</li> -->
            </ul>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{$cdn}}/pc/js/match.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            setPage();
            refreshOdd();
            refreshMatch();
        }
    </script>
    <script type="text/javascript">
        //刷新比赛
        function refreshMatch() {
            var mid = '{{$match['mid']}}';
            var first = mid.substr(0,2);
            var second = mid.substr(2,2);
            var url = '/static/terminal/1/'+ first +'/'+ second +'/'+mid+'/match.json';
            url = '{{env('MATCH_URL')}}' + url;
            $.ajax({
                'url': url,
                dataType: "jsonp",
                'success': function (json) {
                    //比分
                    if (json['status'] > 0 || json['status'] == -1) {
                        $('div#Info p.score').html(json['hscore'] + ' - ' + json['ascore']);
                    }
                    if (json['status'] > 0){
                        window.setTimeout('refreshOdd()', 5000);
                        window.setTimeout('refreshMatch()', 5000);
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

        function _getClassOfUPDOWN(o2,o1,isMiddle) {
            if (isMiddle){
                return parseFloat(o2) > parseFloat(o1) ? 'gambling up':(parseFloat(o2) < parseFloat(o1) ? 'gambling down':'');
            }
            else{
                return parseFloat(o2) > parseFloat(o1) ? 'up':(parseFloat(o2) < parseFloat(o1) ? 'down':'');
            }
        }

        //刷新赔率
        function refreshOdd() {
            var mid = '{{$match['mid']}}';
            var first = mid.substr(0,2);
            var second = mid.substr(2,2);
            var url = '/static/terminal/1/'+ first +'/'+ second +'/'+mid+'/odd.json';
            url = '{{env('MATCH_URL')}}' + url;
            $.ajax({
                'url':url,
                dataType: "jsonp",
                'success':function (json) {
                    var keys = Object.keys(json);
                    if (keys.length > 0){
                        $('div#AllOdd div.odd')[0].style.display = '';
                        var tbody = $('div#AllOdd div.odd table tbody');
                        tbody.html('')
                        for (var i = 0 ; i < keys.length ; i++){
                            var item = json[keys[i]];
//                            if(2 != item['id'] && 5 != item['id'] && 12 != item['id']){
//                                continue;
//                            }
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
                                var className1 = '';
                                var className2 = '';
                                var className3 = '';
                                if (item['ou']['middle1']){
                                    className1 = _getClassOfUPDOWN(item['ou']['up2'],item['ou']['up1'],false);
                                    className2 = _getClassOfUPDOWN(item['ou']['middle2'],item['ou']['middle1'],true);
                                    className3 = _getClassOfUPDOWN(item['ou']['down2'],item['ou']['down1'],false);
                                }
                                tr = tr +
                                        '<td class="' + className1 + '">'+item['ou']['up2']+'</td>'+
                                        '<td class="' + className2 + '">'+item['ou']['middle2']+'</td>'+
                                        '<td class="' + className3 + '">'+item['ou']['down2']+'</td>';
                            }
                            else{
                                tr = tr +
                                        '<td>-</td>'+
                                        '<td>-</td>'+
                                        '<td>-</td>';
                            }
                            if (item['asia']){
                                var className1 = '';
                                var className2 = '';
                                var className3 = '';
                                if (item['asia']['middle1']){
                                    className1 = _getClassOfUPDOWN(item['asia']['up2'],item['asia']['up1'],false);
                                    className2 = _getClassOfUPDOWN(item['asia']['middle2'],item['asia']['middle1'],true);
                                    className3 = _getClassOfUPDOWN(item['asia']['down2'],item['asia']['down1'],false);
                                }
                                tr = tr +
                                        '<td class="' + className1 + '">'+item['asia']['up2']+'</td>'+
                                        '<td class="' + className2 + '">'+panKouText(item['asia']['middle2'],false)+'</td>'+
                                        '<td class="' + className3 + '">'+item['asia']['down2']+'</td>'+
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
                                var className1 = '';
                                var className2 = '';
                                var className3 = '';
                                if (item['goal']['middle1']){
                                    className1 = _getClassOfUPDOWN(item['goal']['up2'],item['goal']['up1'],false);
                                    className2 = _getClassOfUPDOWN(item['goal']['middle2'],item['goal']['middle1'],true);
                                    className3 = _getClassOfUPDOWN(item['goal']['down2'],item['goal']['down1'],false);
                                }
                                tr = tr +
                                        '<td class="' + className1 + '">'+item['goal']['up2']+'</td>'+
                                        '<td class="' + className2 + '">'+panKouText(item['goal']['middle2'],false)+'</td>'+
                                        '<td class="' + className3 + '">'+item['goal']['down2']+'</td>'+
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

                    //指数的刷新
                    var sport = 1;
                    var keys = Object.keys(json);
                    if (keys.length > 0){
                        var tbodya = $('div#AsiaOdd div.tableIn tbody');
                        tbodya.html('');
                        var tbodyg = $('div#GoalOdd div.tableIn tbody');
                        tbodyg.html('');
                        var tbodye = $('div#EuropeOdd div.tableIn tbody');
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
                                    var className1 = '';
                                    var className2 = '';
                                    var className3 = '';
                                    if (data['middle1']){
                                        className1 = _getClassOfUPDOWN(data['up2'],data['up1'],false);
                                        className2 = _getClassOfUPDOWN(data['middle2'],data['middle1'],true);
                                        className3 = _getClassOfUPDOWN(data['down2'],data['down1'],false);
                                    }
                                    tr = tr + '<td class="' + className1 + '">'+data['up2']+'</td>'+
                                            '<td class="' + className2 + '">'+getHandicapCn(data['middle2'],'',1,sport,true)+'</td>'+
                                            '<td class="' + className3 + '">'+data['down2']+'</td>';
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
                                    var className1 = '';
                                    var className2 = '';
                                    var className3 = '';
                                    if (data['middle1']){
                                        className1 = _getClassOfUPDOWN(data['up2'],data['up1'],false);
                                        className2 = _getClassOfUPDOWN(data['middle2'],data['middle1'],true);
                                        className3 = _getClassOfUPDOWN(data['down2'],data['down1'],false);
                                    }
                                    tr = tr + '<td class="' + className1 + '">'+data['up2']+'</td>'+
                                            '<td class="' + className2 + '">'+data['middle2']+'</td>'+
                                            '<td class="' + className3 + '">'+data['down2']+'</td>';
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
                                    var pankou = getHandicapCn(data['middle1'],'',2,sport,true) + '';
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
                                    var className1 = '';
                                    var className2 = '';
                                    var className3 = '';
                                    if (data['middle1']){
                                        className1 = _getClassOfUPDOWN(data['up2'],data['up1'],false);
                                        className2 = _getClassOfUPDOWN(data['middle2'],data['middle1'],true);
                                        className3 = _getClassOfUPDOWN(data['down2'],data['down1'],false);
                                    }
                                    var pankou = getHandicapCn(data['middle2'],'',2,sport,true) + '';
                                    pankou = pankou.replace('让','');
                                    tr = tr + '<td class="' + className1 + '">'+data['up2']+'</td>'+
                                            '<td class="' + className2 + '">'+pankou+'</td>'+
                                            '<td class="' + className3 + '">'+data['down2']+'</td>';
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
    </script>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/pc/css/match.css">
@endsection