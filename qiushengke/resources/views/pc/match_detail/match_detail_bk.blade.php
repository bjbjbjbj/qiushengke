@extends('pc.layout.matchdetail_base')
@section('navContent')
    @component('pc.layout.nav_content',['type'=>1])
    @endcomponent
    @component('pc.cell.top_leagues',['links'=>$basketLeagues])
    @endcomponent
@endsection
@section('content')
    <div id="Con">
        @component('pc.match_detail.basket_cell.base_info', ['match'=>$match])
        @endcomponent
        <div id="Match" style="display: ;">
            {{--@component('pc.match_detail.basket_cell.match_first',['match'=>$match])--}}
            {{--@endcomponent--}}
            @component('pc.match_detail.basket_cell.match_score', ['match'=>$match])
            @endcomponent
            @component('pc.match_detail.basket_cell.match_tech', ['tech'=>$tech])
            @endcomponent
            @component('pc.match_detail.basket_cell.match_players', ['match'=>$match,'players'=>$players])
            @endcomponent
        </div>
        <div id="Data" style="display: none;">
            @component('pc.match_detail.basket_cell.data_odd', ['odds'=>$odds])
            @endcomponent
            @component('pc.match_detail.basket_cell.data_rank', ['match'=>$match,'analyse'=>$analyse])
            @endcomponent
            {{--@component('pc.match_detail.basket_cell.data_attack', ['match'=>$match])--}}
            {{--@endcomponent--}}
            @component('pc.match_detail.basket_cell.data_battle', ['match'=>$match,'analyse'=>$analyse])
            @endcomponent
            @component('pc.match_detail.basket_cell.data_history', ['match'=>$match,'analyse'=>$analyse])
            @endcomponent
        </div>
        @if(isset($hasTotal))
            <div id="Total" style="display: none;">
                <div class="asia">
                    <p class="title">让分盘路比较</p>
                    <div class="part host">
                        <p class="name">休斯顿迪纳摩</p>
                        <div class="fh">
                            <button class="full on">全场</button><button class="half">半场</button>
                        </div>
                        <table class="full">
                            <thead>
                            <tr>
                                <th></th>
                                <th>赛</th>
                                <th>赢盘</th>
                                <th>走水</th>
                                <th>输盘</th>
                                <th>赢盘率</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>总</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td>主</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td>客</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td class="gray">近6场</td>
                                <td>6</td>
                                <td colspan="3">
                                    <p class="green">赢</p><p class="green">赢</p><p class="green">赢</p><p class="blue">输</p><p class="blue">输</p><p class="blue">输</p>
                                </td>
                                <td>50.0%</td>
                            </tr>
                            </tbody>
                        </table>
                        <table class="away" style="display: none;">
                            <thead>
                            <tr>
                                <th></th>
                                <th>赛</th>
                                <th>赢盘</th>
                                <th>走水</th>
                                <th>输盘</th>
                                <th>赢盘率</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>总</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td>主</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td>客</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td class="gray">近6场</td>
                                <td>6</td>
                                <td colspan="3">
                                    <p class="green">赢</p><p class="green">赢</p><p class="green">赢</p><p class="blue">输</p><p class="blue">输</p><p class="blue">输</p>
                                </td>
                                <td>50.0%</td>
                            </tr>
                            </tbody>
                        </table>
                        <p class="name">【相同历史盘口】<span>近5场</span></p>
                        <table>
                            <colgroup>
                                <col num="1" width="11%">
                                <col num="2" width="12%">
                                <col num="3" width="">
                                <col num="4" width="12.5%">
                                <col num="5" width="">
                                <col num="6" width="8%">
                                <col num="7" width="8%">
                                <col num="8" width="8%">
                                <col num="9" width="8%">
                            </colgroup>
                            <thead>
                            <tr>
                                <th>赛事</th>
                                <th>时间</th>
                                <th>主队</th>
                                <th>比分</th>
                                <th>客队</th>
                                <th>分差</th>
                                <th>胜负</th>
                                <th>盘口</th>
                                <th>盘路</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>NBA</td>
                                <td>17.01.19</td>
                                <td>勇士</td>
                                <td>120-100</td>
                                <td>太阳</td>
                                <td>29</td>
                                <td class="green">胜</td>
                                <td>16.5</td>
                                <td class="green">赢</td>
                            </tr>
                            <tr>
                                <td>NBA</td>
                                <td>17.01.19</td>
                                <td>勇士</td>
                                <td>120-100</td>
                                <td>太阳</td>
                                <td>29</td>
                                <td class="blue">负</td>
                                <td>16.5</td>
                                <td class="blue">输</td>
                            </tr>
                            <tr>
                                <td>NBA</td>
                                <td>17.01.19</td>
                                <td>勇士</td>
                                <td>120-100</td>
                                <td>太阳</td>
                                <td>29</td>
                                <td class="green">胜</td>
                                <td>16.5</td>
                                <td class="green">赢</td>
                            </tr>
                            <tr>
                                <td>NBA</td>
                                <td>17.01.19</td>
                                <td>勇士</td>
                                <td>120-100</td>
                                <td>太阳</td>
                                <td>29</td>
                                <td class="green">胜</td>
                                <td>16.5</td>
                                <td class="green">赢</td>
                            </tr>
                            <tr>
                                <td>NBA</td>
                                <td>17.01.19</td>
                                <td>勇士</td>
                                <td>120-100</td>
                                <td>太阳</td>
                                <td>29</td>
                                <td class="green">胜</td>
                                <td>16.5</td>
                                <td class="green">赢</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="part away">
                        <p class="name">休斯顿迪纳摩</p>
                        <div class="fh">
                            <button class="full on">全场</button><button class="half">半场</button>
                        </div>
                        <table class="full">
                            <thead>
                            <tr>
                                <th></th>
                                <th>赛</th>
                                <th>赢盘</th>
                                <th>走水</th>
                                <th>输盘</th>
                                <th>赢盘率</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>总</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td>主</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td>客</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td class="gray">近6场</td>
                                <td>6</td>
                                <td colspan="3">
                                    <p class="green">赢</p><p class="green">赢</p><p class="green">赢</p><p class="blue">输</p><p class="blue">输</p><p class="blue">输</p>
                                </td>
                                <td>50.0%</td>
                            </tr>
                            </tbody>
                        </table>
                        <table class="away" style="display: none;">
                            <thead>
                            <tr>
                                <th></th>
                                <th>赛</th>
                                <th>赢盘</th>
                                <th>走水</th>
                                <th>输盘</th>
                                <th>赢盘率</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>总</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td>主</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td>客</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td class="gray">近6场</td>
                                <td>6</td>
                                <td colspan="3">
                                    <p class="green">赢</p><p class="green">赢</p><p class="green">赢</p><p class="blue">输</p><p class="blue">输</p><p class="blue">输</p>
                                </td>
                                <td>50.0%</td>
                            </tr>
                            </tbody>
                        </table>
                        <p class="name">【相同历史盘口】<span>近5场</span></p>
                        <table>
                            <colgroup>
                                <col num="1" width="11%">
                                <col num="2" width="12%">
                                <col num="3" width="">
                                <col num="4" width="12.5%">
                                <col num="5" width="">
                                <col num="6" width="8%">
                                <col num="7" width="8%">
                                <col num="8" width="8%">
                                <col num="9" width="8%">
                            </colgroup>
                            <thead>
                            <tr>
                                <th>赛事</th>
                                <th>时间</th>
                                <th>主队</th>
                                <th>比分</th>
                                <th>客队</th>
                                <th>分差</th>
                                <th>胜负</th>
                                <th>盘口</th>
                                <th>盘路</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>NBA</td>
                                <td>17.01.19</td>
                                <td>勇士</td>
                                <td>120-100</td>
                                <td>太阳</td>
                                <td>29</td>
                                <td class="green">胜</td>
                                <td>16.5</td>
                                <td class="green">赢</td>
                            </tr>
                            <tr>
                                <td>NBA</td>
                                <td>17.01.19</td>
                                <td>勇士</td>
                                <td>120-100</td>
                                <td>太阳</td>
                                <td>29</td>
                                <td class="blue">负</td>
                                <td>16.5</td>
                                <td class="blue">输</td>
                            </tr>
                            <tr>
                                <td>NBA</td>
                                <td>17.01.19</td>
                                <td>勇士</td>
                                <td>120-100</td>
                                <td>太阳</td>
                                <td>29</td>
                                <td class="green">胜</td>
                                <td>16.5</td>
                                <td class="green">赢</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="goal">
                    <p class="title">总分盘路比较</p>
                    <div class="part host">
                        <p class="name">休斯顿迪纳摩</p>
                        <div class="fh">
                            <button class="full on">全场</button><button class="half">半场</button>
                        </div>
                        <table class="full">
                            <thead>
                            <tr>
                                <th></th>
                                <th>赛</th>
                                <th>大分</th>
                                <th>走水</th>
                                <th>小分</th>
                                <th>大分率</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>总</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td>主</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td>客</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td class="gray">近6场</td>
                                <td>6</td>
                                <td colspan="3">
                                    <p class="green">大</p><p class="green">大</p><p class="green">大</p><p class="yellow">小</p><p class="yellow">小</p><p class="yellow">小</p>
                                </td>
                                <td>50.0%</td>
                            </tr>
                            </tbody>
                        </table>
                        <table class="away" style="display: none;">
                            <thead>
                            <tr>
                                <th></th>
                                <th>赛</th>
                                <th>大分</th>
                                <th>走水</th>
                                <th>小分</th>
                                <th>大分率</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>总</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td>主</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td>客</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td class="gray">近6场</td>
                                <td>6</td>
                                <td colspan="3">
                                    <p class="green">大</p><p class="green">大</p><p class="green">大</p><p class="yellow">小</p><p class="yellow">小</p><p class="yellow">小</p>
                                </td>
                                <td>50.0%</td>
                            </tr>
                            </tbody>
                        </table>
                        <p class="name">【相同历史盘口】<span>近5场</span></p>
                        <table>
                            <colgroup>
                                <col num="1" width="11%">
                                <col num="2" width="12%">
                                <col num="3" width="">
                                <col num="4" width="12.5%">
                                <col num="5" width="">
                                <col num="6" width="8%">
                                <col num="7" width="8%">
                                <col num="8" width="8%">
                                <col num="9" width="8%">
                            </colgroup>
                            <thead>
                            <tr>
                                <th>赛事</th>
                                <th>时间</th>
                                <th>主队</th>
                                <th>比分</th>
                                <th>客队</th>
                                <th>分差</th>
                                <th>胜负</th>
                                <th>盘口</th>
                                <th>盘路</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>NBA</td>
                                <td>17.01.19</td>
                                <td>勇士</td>
                                <td>120-100</td>
                                <td>太阳</td>
                                <td>29</td>
                                <td class="green">胜</td>
                                <td>210.5</td>
                                <td class="green">大</td>
                            </tr>
                            <tr>
                                <td>NBA</td>
                                <td>17.01.19</td>
                                <td>勇士</td>
                                <td>120-100</td>
                                <td>太阳</td>
                                <td>29</td>
                                <td class="blue">负</td>
                                <td>216.5</td>
                                <td class="yellow">小</td>
                            </tr>
                            <tr>
                                <td>NBA</td>
                                <td>17.01.19</td>
                                <td>勇士</td>
                                <td>120-100</td>
                                <td>太阳</td>
                                <td>29</td>
                                <td class="green">胜</td>
                                <td>210.5</td>
                                <td class="green">大</td>
                            </tr>
                            <tr>
                                <td>NBA</td>
                                <td>17.01.19</td>
                                <td>勇士</td>
                                <td>120-100</td>
                                <td>太阳</td>
                                <td>29</td>
                                <td class="green">胜</td>
                                <td>210.5</td>
                                <td class="green">大</td>
                            </tr>
                            <tr>
                                <td>NBA</td>
                                <td>17.01.19</td>
                                <td>勇士</td>
                                <td>120-100</td>
                                <td>太阳</td>
                                <td>29</td>
                                <td class="green">胜</td>
                                <td>210.5</td>
                                <td class="green">大</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="part away">
                        <p class="name">休斯顿迪纳摩</p>
                        <div class="fh">
                            <button class="full on">全场</button><button class="half">半场</button>
                        </div>
                        <table class="full">
                            <thead>
                            <tr>
                                <th></th>
                                <th>赛</th>
                                <th>大分</th>
                                <th>走水</th>
                                <th>小分</th>
                                <th>大分率</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>总</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td>主</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td>客</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td class="gray">近6场</td>
                                <td>6</td>
                                <td colspan="3">
                                    <p class="green">大</p><p class="green">大</p><p class="green">大</p><p class="yellow">小</p><p class="yellow">小</p><p class="yellow">小</p>
                                </td>
                                <td>50.0%</td>
                            </tr>
                            </tbody>
                        </table>
                        <table class="away" style="display: none;">
                            <thead>
                            <tr>
                                <th></th>
                                <th>赛</th>
                                <th>大分</th>
                                <th>走水</th>
                                <th>小分</th>
                                <th>大分率</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>总</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td>主</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td>客</td>
                                <td>10</td>
                                <td>4</td>
                                <td>0</td>
                                <td>6</td>
                                <td>40.0%</td>
                            </tr>
                            <tr>
                                <td class="gray">近6场</td>
                                <td>6</td>
                                <td colspan="3">
                                    <p class="green">大</p><p class="green">大</p><p class="green">大</p><p class="yellow">小</p><p class="yellow">小</p><p class="yellow">小</p>
                                </td>
                                <td>50.0%</td>
                            </tr>
                            </tbody>
                        </table>
                        <p class="name">【相同历史盘口】<span>近5场</span></p>
                        <table>
                            <colgroup>
                                <col num="1" width="11%">
                                <col num="2" width="12%">
                                <col num="3" width="">
                                <col num="4" width="12.5%">
                                <col num="5" width="">
                                <col num="6" width="8%">
                                <col num="7" width="8%">
                                <col num="8" width="8%">
                                <col num="9" width="8%">
                            </colgroup>
                            <thead>
                            <tr>
                                <th>赛事</th>
                                <th>时间</th>
                                <th>主队</th>
                                <th>比分</th>
                                <th>客队</th>
                                <th>分差</th>
                                <th>胜负</th>
                                <th>盘口</th>
                                <th>盘路</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>NBA</td>
                                <td>17.01.19</td>
                                <td>勇士</td>
                                <td>120-100</td>
                                <td>太阳</td>
                                <td>29</td>
                                <td class="green">胜</td>
                                <td>210.5</td>
                                <td class="green">大</td>
                            </tr>
                            <tr>
                                <td>NBA</td>
                                <td>17.01.19</td>
                                <td>勇士</td>
                                <td>120-100</td>
                                <td>太阳</td>
                                <td>29</td>
                                <td class="blue">负</td>
                                <td>216.5</td>
                                <td class="yellow">小</td>
                            </tr>
                            <tr>
                                <td>NBA</td>
                                <td>17.01.19</td>
                                <td>勇士</td>
                                <td>120-100</td>
                                <td>太阳</td>
                                <td>29</td>
                                <td class="green">胜</td>
                                <td>210.5</td>
                                <td class="green">大</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="average">
                    <p class="title">平均得分/失分对比</p>
                    <div class="part host">
                        <div class="name">
                            休斯顿迪纳摩
                            <p class="num"><button class="on" name="number" value="10">近10场</button><button name="number" value="5">近5场</button></p>
                        </div>
                        <table num="10">
                            <thead>
                            <tr>
                                <th rowspan="2"></th>
                                <th rowspan="2">场次</th>
                                <th colspan="2">第一节</th>
                                <th colspan="2">第二节</th>
                                <th colspan="2">第三节</th>
                                <th colspan="2">第四节</th>
                                <th colspan="2">全场</th>
                            </tr>
                            <tr>
                                <th>得</th>
                                <th>失</th>
                                <th>得</th>
                                <th>失</th>
                                <th>得</th>
                                <th>失</th>
                                <th>得</th>
                                <th>失</th>
                                <th>得</th>
                                <th>失</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>总</td>
                                <td>10</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                            </tr>
                            <tr>
                                <td>主</td>
                                <td>10</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                            </tr>
                            <tr>
                                <td>客</td>
                                <td>10</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                            </tr>
                            </tbody>
                        </table>
                        <table num="5" style="display: none;">
                            <thead>
                            <tr>
                                <th rowspan="2"></th>
                                <th rowspan="2">场次</th>
                                <th colspan="2">第一节</th>
                                <th colspan="2">第二节</th>
                                <th colspan="2">第三节</th>
                                <th colspan="2">第四节</th>
                                <th colspan="2">全场</th>
                            </tr>
                            <tr>
                                <th>得</th>
                                <th>失</th>
                                <th>得</th>
                                <th>失</th>
                                <th>得</th>
                                <th>失</th>
                                <th>得</th>
                                <th>失</th>
                                <th>得</th>
                                <th>失</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>总</td>
                                <td>5</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                            </tr>
                            <tr>
                                <td>主</td>
                                <td>5</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                            </tr>
                            <tr>
                                <td>客</td>
                                <td>5</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="part away">
                        <div class="name">
                            休斯顿迪纳摩
                            <p class="num"><button class="on" name="number" value="10">近10场</button><button name="number" value="5">近5场</button></p>
                        </div>
                        <table num="10">
                            <thead>
                            <tr>
                                <th rowspan="2"></th>
                                <th rowspan="2">场次</th>
                                <th colspan="2">第一节</th>
                                <th colspan="2">第二节</th>
                                <th colspan="2">第三节</th>
                                <th colspan="2">第四节</th>
                                <th colspan="2">全场</th>
                            </tr>
                            <tr>
                                <th>得</th>
                                <th>失</th>
                                <th>得</th>
                                <th>失</th>
                                <th>得</th>
                                <th>失</th>
                                <th>得</th>
                                <th>失</th>
                                <th>得</th>
                                <th>失</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>总</td>
                                <td>10</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                            </tr>
                            <tr>
                                <td>主</td>
                                <td>10</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                            </tr>
                            <tr>
                                <td>客</td>
                                <td>10</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                            </tr>
                            </tbody>
                        </table>
                        <table num="5" style="display: none;">
                            <thead>
                            <tr>
                                <th rowspan="2"></th>
                                <th rowspan="2">场次</th>
                                <th colspan="2">第一节</th>
                                <th colspan="2">第二节</th>
                                <th colspan="2">第三节</th>
                                <th colspan="2">第四节</th>
                                <th colspan="2">全场</th>
                            </tr>
                            <tr>
                                <th>得</th>
                                <th>失</th>
                                <th>得</th>
                                <th>失</th>
                                <th>得</th>
                                <th>失</th>
                                <th>得</th>
                                <th>失</th>
                                <th>得</th>
                                <th>失</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>总</td>
                                <td>5</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                            </tr>
                            <tr>
                                <td>主</td>
                                <td>5</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                            </tr>
                            <tr>
                                <td>客</td>
                                <td>5</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                                <td>30</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="total">
                    <p class="title">总分统计</p>
                    <div class="part host">
                        <div class="name">
                            休斯顿迪纳摩
                            <p class="num"><button class="on" name="number" value="10">近10场</button><button name="number" value="5">近5场</button></p>
                        </div>
                        <table num="10">
                            <thead>
                            <tr>
                                <th></th>
                                <th>赛</th>
                                <th>160<br/>-<br/>&nbsp;</th>
                                <th>160<br/>-<br/>170</th>
                                <th>170<br/>-<br/>180</th>
                                <th>180<br/>-<br/>190</th>
                                <th>190<br/>-<br/>200</th>
                                <th>200<br/>-<br/>210</th>
                                <th>210<br/>-<br/>220</th>
                                <th>220<br/>-<br/>230</th>
                                <th>230<br/>+<br/>&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>总</td>
                                <td>10</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                                <td>2</td>
                                <td>1</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>主</td>
                                <td>10</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                                <td>2</td>
                                <td>1</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>客</td>
                                <td>10</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                                <td>2</td>
                                <td>1</td>
                                <td>0</td>
                            </tr>
                            </tbody>
                        </table>
                        <table num="5" style="display: none;">
                            <thead>
                            <tr>
                                <th></th>
                                <th>赛</th>
                                <th>160<br/>-<br/>&nbsp;</th>
                                <th>160<br/>-<br/>170</th>
                                <th>170<br/>-<br/>180</th>
                                <th>180<br/>-<br/>190</th>
                                <th>190<br/>-<br/>200</th>
                                <th>200<br/>-<br/>210</th>
                                <th>210<br/>-<br/>220</th>
                                <th>220<br/>-<br/>230</th>
                                <th>230<br/>+<br/>&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>总</td>
                                <td>5</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                                <td>2</td>
                                <td>1</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>主</td>
                                <td>5</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                                <td>2</td>
                                <td>1</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>客</td>
                                <td>5</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                                <td>2</td>
                                <td>1</td>
                                <td>0</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="part away">
                        <div class="name">
                            休斯顿迪纳摩
                            <p class="num"><button class="on" name="number" value="10">近10场</button><button name="number" value="5">近5场</button></p>
                        </div>
                        <table num="10">
                            <thead>
                            <tr>
                                <th></th>
                                <th>赛</th>
                                <th>160<br/>-<br/>&nbsp;</th>
                                <th>160<br/>-<br/>170</th>
                                <th>170<br/>-<br/>180</th>
                                <th>180<br/>-<br/>190</th>
                                <th>190<br/>-<br/>200</th>
                                <th>200<br/>-<br/>210</th>
                                <th>210<br/>-<br/>220</th>
                                <th>220<br/>-<br/>230</th>
                                <th>230<br/>+<br/>&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>总</td>
                                <td>10</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                                <td>2</td>
                                <td>1</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>主</td>
                                <td>10</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                                <td>2</td>
                                <td>1</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>客</td>
                                <td>10</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                                <td>2</td>
                                <td>1</td>
                                <td>0</td>
                            </tr>
                            </tbody>
                        </table>
                        <table num="5" style="display: none;">
                            <thead>
                            <tr>
                                <th></th>
                                <th>赛</th>
                                <th>160<br/>-<br/>&nbsp;</th>
                                <th>160<br/>-<br/>170</th>
                                <th>170<br/>-<br/>180</th>
                                <th>180<br/>-<br/>190</th>
                                <th>190<br/>-<br/>200</th>
                                <th>200<br/>-<br/>210</th>
                                <th>210<br/>-<br/>220</th>
                                <th>220<br/>-<br/>230</th>
                                <th>230<br/>+<br/>&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>总</td>
                                <td>5</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                                <td>2</td>
                                <td>1</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>主</td>
                                <td>5</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                                <td>2</td>
                                <td>1</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>客</td>
                                <td>5</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                                <td>2</td>
                                <td>1</td>
                                <td>0</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="total">
                    <p class="title">胜分差统计</p>
                    <div class="part host">
                        <div class="name">
                            休斯顿迪纳摩
                            <p class="num"><button class="on" name="number" value="win">获胜</button><button name="number" value="lose">落败</button></p>
                        </div>
                        <table num="win">
                            <thead>
                            <tr>
                                <th></th>
                                <th>场次</th>
                                <th>胜1-5</th>
                                <th>胜6-10</th>
                                <th>胜11-15</th>
                                <th>胜16-20</th>
                                <th>胜21-25</th>
                                <th>胜26+</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>总</td>
                                <td>10</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>主</td>
                                <td>10</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>客</td>
                                <td>10</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                            </tr>
                            </tbody>
                        </table>
                        <table num="lose" style="display: none;">
                            <thead>
                            <tr>
                                <th></th>
                                <th>场次</th>
                                <th>胜1-5</th>
                                <th>胜6-10</th>
                                <th>胜11-15</th>
                                <th>胜16-20</th>
                                <th>胜21-25</th>
                                <th>胜26+</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>总</td>
                                <td>10</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>主</td>
                                <td>10</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>客</td>
                                <td>10</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="part away">
                        <div class="name">
                            休斯顿迪纳摩
                            <p class="num"><button class="on" name="number" value="win">获胜</button><button name="number" value="lose">落败</button></p>
                        </div>
                        <table num="win">
                            <thead>
                            <tr>
                                <th></th>
                                <th>场次</th>
                                <th>胜1-5</th>
                                <th>胜6-10</th>
                                <th>胜11-15</th>
                                <th>胜16-20</th>
                                <th>胜21-25</th>
                                <th>胜26+</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>总</td>
                                <td>10</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>主</td>
                                <td>10</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>客</td>
                                <td>10</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                            </tr>
                            </tbody>
                        </table>
                        <table num="lose" style="display: none;">
                            <thead>
                            <tr>
                                <th></th>
                                <th>场次</th>
                                <th>胜1-5</th>
                                <th>胜6-10</th>
                                <th>胜11-15</th>
                                <th>胜16-20</th>
                                <th>胜21-25</th>
                                <th>胜26+</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>总</td>
                                <td>10</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>主</td>
                                <td>10</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>客</td>
                                <td>10</td>
                                <td>3</td>
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                <td>2</td>
                                <td>0</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div id="Play">
        <div class="abox">
            <ul>
                <li class="on" target="Match">比赛赛况</li>
                <li target="Data">数据分析</li>
                @if(isset($hasTotal))
                    <li target="Total">统计比较</li>
                @endif
            </ul>
        </div>
    </div>
    <div id="Totop">
        <div class="abox">
            <a class="totop" href="javascript:void(0)"></a>
        </div>
    </div>
    <div id="Bottom">
        <p>友情链接：<a href="">料狗商城</a><a href="">cctv5在线直播</a><a href="">258直播网</a><a href="">料狗TV</a><a href="">世界杯直播</a><a href="">5播体育</a></p>
        <p>免责声明：本站所有直播和视频链接均由网友提供，如有侵权问题，请及时联系，我们将尽快处理。</p>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{$cdn}}/pc/js/match_bk.js"></script>
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
            var url = '/static/terminal/2/'+ first +'/'+ second +'/'+mid+'/match.json';
            url = '/test?url=' + '{{env('MATCH_URL')}}' + url;
            $.ajax({
                'url': url,
                'success': function (json) {
                    //比分
                    if (json['status'] > 0 || json['status'] == -1) {
                        $('div#Info p.score').html(json['hscore'] + ' - ' + json['ascore']);
                    }
                    if (json['status'] > 0){
                        window.setTimeout('refreshOdd()', 5000);
                        window.setTimeout('refreshMatch()', 5000);
                    }

                    var match = json;

                    $('.data_h_1').html(match['hscore_1st']?match['hscore_1st']:'-');
                    $('.data_h_2').html(match['hscore_2nd']?match['hscore_2nd']:'-');
                    $('.data_h_3').html(match['hscore_3rd']?match['hscore_3rd']:'-');
                    $('.data_h_4').html(match['hscore_4th']?match['hscore_4th']:'-');
                    $('.data_h_s').html(match['hscore']?match['hscore']:'-');

                    $('.data_a_1').html(match['ascore_1st']?match['ascore_1st']:'-');
                    $('.data_a_2').html(match['ascore_2nd']?match['ascore_2nd']:'-');
                    $('.data_a_3').html(match['ascore_3rd']?match['ascore_3rd']:'-');
                    $('.data_a_4').html(match['ascore_4th']?match['ascore_4th']:'-');
                    $('.data_a_s').html(match['ascore']?match['ascore']:'-');

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
                }
            });
        }
        //刷新赔率
        function refreshOdd() {
            var mid = '{{$match['mid']}}';
            var first = mid.substr(0,2);
            var second = mid.substr(2,2);
            var url = '/static/terminal/2/'+ first +'/'+ second +'/'+mid+'/odd.json';
            url = '/test?url=' + '{{env('MATCH_URL')}}' + url;
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
    </script>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/pc/css/match_bk.css">
@endsection