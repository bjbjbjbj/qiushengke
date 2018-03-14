@extends('pc.layout.matchdetail_base')
@section('navContent')
    <div class="home"><p class="abox"><a href="index.html"><img src="/pc/img/logo_image_n.png"></a></p></div>
    <div class="Column">
        <a href="/match/foot/immediate_t.html">足球</a>
        <a class="on" href="/match/basket/immediate_t.html">篮球</a>
        <a href="">主播</a>
        <a href="">手机APP</a>
    </div>
    @component('pc.cell.top_leagues',['links'=>$basketLeagues])
    @endcomponent
@endsection
@section('content')
    <div id="Con">
        @component('pc.match_detail.basket_cell.base_info', ['match'=>$match])
        @endcomponent
        <div id="Match" style="display: ;">
            <div class="first">
                <p class="title">比赛阵容</p>
                <div class="part host">
                    <p class="name">休斯顿迪纳摩</p>
                    <table>
                        <thead>
                        <tr>
                            <th>首发</th>
                            <th>后备</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><p>1</p>威利斯</td>
                            <td><p>1</p>威利斯</td>
                        </tr>
                        <tr>
                            <td><p>1</p>比斯利</td>
                            <td><p>1</p>比斯利</td>
                        </tr>
                        <tr>
                            <td><p>1</p>森德罗斯</td>
                            <td><p>1</p>森德罗斯</td>
                        </tr>
                        <tr>
                            <td><p>1</p>艾度科玛查度</td>
                            <td><p>1</p>艾度科玛查度</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><p>1</p>威利斯</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><p>1</p>比斯利</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><p>1</p>森德罗斯</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><p>1</p>艾度科玛查度</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><p>1</p>威利斯</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><p>1</p>比斯利</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><p>1</p>森德罗斯</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="part away">
                    <p class="name">休斯顿迪纳摩</p>
                    <table>
                        <thead>
                        <tr>
                            <th>首发</th>
                            <th>后备</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><p>1</p>威利斯</td>
                            <td><p>1</p>威利斯</td>
                        </tr>
                        <tr>
                            <td><p>1</p>比斯利</td>
                            <td><p>1</p>比斯利</td>
                        </tr>
                        <tr>
                            <td><p>1</p>森德罗斯</td>
                            <td><p>1</p>森德罗斯</td>
                        </tr>
                        <tr>
                            <td><p>1</p>艾度科玛查度</td>
                            <td><p>1</p>艾度科玛查度</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><p>1</p>威利斯</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><p>1</p>比斯利</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><p>1</p>森德罗斯</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><p>1</p>艾度科玛查度</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><p>1</p>威利斯</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><p>1</p>比斯利</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><p>1</p>森德罗斯</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="score">
                <p class="title">比分统计</p>
                <table>
                    <thead>
                    <tr>
                        <th></th>
                        <th>第一节</th>
                        <th>第二节</th>
                        <th>第三节</th>
                        <th>第四节</th>
                        <th>总分</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>波士顿凯尔特人</td>
                        <td>24</td>
                        <td>28</td>
                        <td>12</td>
                        <td>-</td>
                        <td>52</td>
                    </tr>
                    <tr>
                        <td>洛杉矶湖人</td>
                        <td>24</td>
                        <td>28</td>
                        <td>10</td>
                        <td>-</td>
                        <td>52</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="total">
                <p class="title">技术统计</p>
                <ul>
                    <li>
                        得分
                        <div class="line host">
                            <p>108</p>
                            <span style="width: 66%;"></span>
                        </div>
                        <div class="line away">
                            <p>125</p>
                            <span style="width: 34%;"></span>
                        </div>
                    </li>
                    <li>
                        篮板
                        <div class="line host">
                            <p>33</p>
                            <span style="width: 66%;"></span>
                        </div>
                        <div class="line away">
                            <p>46</p>
                            <span style="width: 34%;"></span>
                        </div>
                    </li>
                    <li>
                        助攻
                        <div class="line host">
                            <p>20</p>
                            <span style="width: 66%;"></span>
                        </div>
                        <div class="line away">
                            <p>21</p>
                            <span style="width: 34%;"></span>
                        </div>
                    </li>
                    <li>
                        抢断
                        <div class="line host">
                            <p>5</p>
                            <span style="width: 66%;"></span>
                        </div>
                        <div class="line away">
                            <p>4</p>
                            <span style="width: 34%;"></span>
                        </div>
                    </li>
                    <li>
                        盖帽
                        <div class="line host">
                            <p>3</p>
                            <span style="width: 66%;"></span>
                        </div>
                        <div class="line away">
                            <p>2</p>
                            <span style="width: 34%;"></span>
                        </div>
                    </li>
                    <li>
                        失误
                        <div class="line host">
                            <p>10</p>
                            <span style="width: 66%;"></span>
                        </div>
                        <div class="line away">
                            <p>11</p>
                            <span style="width: 34%;"></span>
                        </div>
                    </li>
                    <li>
                        罚球
                        <div class="line host">
                            <p>20</p>
                            <span style="width: 66%;"></span>
                        </div>
                        <div class="line away">
                            <p>21</p>
                            <span style="width: 34%;"></span>
                        </div>
                    </li>
                    <li>
                        三分
                        <div class="line host">
                            <p>12</p>
                            <span style="width: 66%;"></span>
                        </div>
                        <div class="line away">
                            <p>11</p>
                            <span style="width: 34%;"></span>
                        </div>
                    </li>
                    <li>
                        犯规
                        <div class="line host">
                            <p>20</p>
                            <span style="width: 66%;"></span>
                        </div>
                        <div class="line away">
                            <p>17</p>
                            <span style="width: 34%;"></span>
                        </div>
                    </li>
                    <li>
                        投篮命中率
                        <div class="line host">
                            <p>48%</p>
                            <span style="width: 66%;"></span>
                        </div>
                        <div class="line away">
                            <p>49%</p>
                            <span style="width: 34%;"></span>
                        </div>
                    </li>
                    <li>
                        罚球命中率
                        <div class="line host">
                            <p>83%</p>
                            <span style="width: 66%;"></span>
                        </div>
                        <div class="line away">
                            <p>77%</p>
                            <span style="width: 34%;"></span>
                        </div>
                    </li>
                    <li>
                        三分命中率
                        <div class="line host">
                            <p>42%</p>
                            <span style="width: 66%;"></span>
                        </div>
                        <div class="line away">
                            <p>41%</p>
                            <span style="width: 34%;"></span>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="player">
                <p class="title">技术统计</p>
                <p class="name">波士顿凯尔特人</p>
                <table class="host">
                    <colgroup>
                        <col num="1">
                        <col num="2" width="6%">
                        <col num="3" width="6%">
                        <col num="4" width="8%">
                        <col num="5" width="8%">
                        <col num="6" width="8%">
                        <col num="7" width="6%">
                        <col num="8" width="6%">
                        <col num="9" width="6%">
                        <col num="10" width="6%">
                        <col num="11" width="6%">
                        <col num="12" width="6%">
                        <col num="13" width="6%">
                    </colgroup>
                    <thead>
                    <tr>
                        <th>球员</th>
                        <th>首发</th>
                        <th>得分</th>
                        <th>投篮</th>
                        <th>三分</th>
                        <th>罚球</th>
                        <th>篮板</th>
                        <th>助攻</th>
                        <th>犯规</th>
                        <th>抢断</th>
                        <th>失误</th>
                        <th>盖帽</th>
                        <th>时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>贾斯丁·霍乐迪</td>
                        <td>后卫</td>
                        <td>24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>14</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24'</td>
                    </tr>
                    <tr>
                        <td>贾斯丁·霍乐迪</td>
                        <td>后卫</td>
                        <td>24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>14</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24'</td>
                    </tr>
                    <tr>
                        <td>贾斯丁·霍乐迪</td>
                        <td>后卫</td>
                        <td>24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>14</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24'</td>
                    </tr>
                    <tr>
                        <td>贾斯丁·霍乐迪</td>
                        <td>后卫</td>
                        <td>24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>14</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24'</td>
                    </tr>
                    <tr>
                        <td>贾斯丁·霍乐迪</td>
                        <td>后卫</td>
                        <td>24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>14</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24'</td>
                    </tr>
                    <tr>
                        <td>贾斯丁·霍乐迪</td>
                        <td></td>
                        <td>24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>14</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24'</td>
                    </tr>
                    <tr>
                        <td>贾斯丁·霍乐迪</td>
                        <td></td>
                        <td>24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>14</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24'</td>
                    </tr>
                    <tr>
                        <td>贾斯丁·霍乐迪</td>
                        <td></td>
                        <td>24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>14</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24'</td>
                    </tr>
                    <tr>
                        <td>贾斯丁·霍乐迪</td>
                        <td></td>
                        <td>24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>14</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24'</td>
                    </tr>
                    <tr>
                        <td>贾斯丁·霍乐迪</td>
                        <td></td>
                        <td>24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>14</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24'</td>
                    </tr>
                    <tr class="noPlay">
                        <td>贾斯丁·霍乐迪</td>
                        <td></td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                    <tr class="total">
                        <td>总计</td>
                        <td></td>
                        <td>24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>14</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>-</td>
                    </tr>
                    <tr class="total">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>48.1%</td>
                        <td>48.1%</td>
                        <td>48.1%</td>
                        <td></td>
                        <td colspan="3">二次进攻：0</td>
                        <td colspan="3">总失误：8</td>
                    </tr>
                    </tbody>
                </table>
                <p class="name">洛杉矶湖人</p>
                <table class="away">
                    <colgroup>
                        <col num="1">
                        <col num="2" width="6%">
                        <col num="3" width="6%">
                        <col num="4" width="8%">
                        <col num="5" width="8%">
                        <col num="6" width="8%">
                        <col num="7" width="6%">
                        <col num="8" width="6%">
                        <col num="9" width="6%">
                        <col num="10" width="6%">
                        <col num="11" width="6%">
                        <col num="12" width="6%">
                        <col num="13" width="6%">
                    </colgroup>
                    <thead>
                    <tr>
                        <th>球员</th>
                        <th>首发</th>
                        <th>得分</th>
                        <th>投篮</th>
                        <th>三分</th>
                        <th>罚球</th>
                        <th>篮板</th>
                        <th>助攻</th>
                        <th>犯规</th>
                        <th>抢断</th>
                        <th>失误</th>
                        <th>盖帽</th>
                        <th>时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>科比·布莱恩特</td>
                        <td>后卫</td>
                        <td>24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>14</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24'</td>
                    </tr>
                    <tr>
                        <td>科比·布莱恩特</td>
                        <td>后卫</td>
                        <td>24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>14</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24'</td>
                    </tr>
                    <tr>
                        <td>科比·布莱恩特</td>
                        <td>后卫</td>
                        <td>24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>14</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24'</td>
                    </tr>
                    <tr>
                        <td>科比·布莱恩特</td>
                        <td>后卫</td>
                        <td>24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>14</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24'</td>
                    </tr>
                    <tr>
                        <td>科比·布莱恩特</td>
                        <td>后卫</td>
                        <td>24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>14</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24'</td>
                    </tr>
                    <tr>
                        <td>科比·布莱恩特</td>
                        <td></td>
                        <td>24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>14</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24'</td>
                    </tr>
                    <tr>
                        <td>科比·布莱恩特</td>
                        <td></td>
                        <td>24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>14</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24'</td>
                    </tr>
                    <tr>
                        <td>科比·布莱恩特</td>
                        <td></td>
                        <td>24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>14</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24'</td>
                    </tr>
                    <tr>
                        <td>科比·布莱恩特</td>
                        <td></td>
                        <td>24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>14</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24'</td>
                    </tr>
                    <tr>
                        <td>科比·布莱恩特</td>
                        <td></td>
                        <td>24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>14</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24'</td>
                    </tr>
                    <tr class="noPlay">
                        <td>科比·布莱恩特</td>
                        <td></td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                    <tr class="total">
                        <td>总计</td>
                        <td></td>
                        <td>24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>6-24</td>
                        <td>14</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>24</td>
                        <td>-</td>
                    </tr>
                    <tr class="total">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>48.1%</td>
                        <td>48.1%</td>
                        <td>48.1%</td>
                        <td></td>
                        <td colspan="3">二次进攻：0</td>
                        <td colspan="3">总失误：8</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="Data" style="display: none;">
            <div class="odd">
                <p class="title">赔率指数</p>
                <p class="abox"><a href="odd.html">【亚】</a><a href="odd.html">【欧】</a><a href="odd.html">【大】</a></p>
                <table>
                    <colgroup>
                        <col num="1" width="">
                        <col num="2" width="6%">
                        <col num="3" width="10%">
                        <col num="4" width="10%">
                        <col num="5" width="10%">
                        <col num="6" width="10%">
                        <col num="7" width="10%">
                        <col num="8" width="10%">
                        <col num="9" width="10%">
                        <col num="10" width="10%">
                    </colgroup>
                    <thead>
                    <tr>
                        <th colspan="2"></th>
                        <th colspan="2">欧洲指数</th>
                        <th colspan="3">亚洲指数</th>
                        <th colspan="3">大小球分指数</th>
                    </tr>
                    <tr>
                        <th>公司</th>
                        <th></th>
                        <th>主胜</th>
                        <th>客胜</th>
                        <th>主队</th>
                        <th>让分</th>
                        <th>客队</th>
                        <th>大球</th>
                        <th>盘口</th>
                        <th>小球</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td rowspan="2">SB</td>
                        <td>初盘</td>
                        <td>2.29</td>
                        <td>3.5</td>
                        <td>1.00</td>
                        <td>-6.5</td>
                        <td>0.98</td>
                        <td>1.00</td>
                        <td>216.5</td>
                        <td>0.98</td>
                    </tr>
                    <tr>
                        <td>终盘</td>
                        <td>2.29</td>
                        <td>3.5</td>
                        <td>1.00</td>
                        <td>-6.5</td>
                        <td>0.98</td>
                        <td>1.00</td>
                        <td>216.5</td>
                        <td>0.98</td>
                    </tr>
                    <tr>
                        <td rowspan="2">Bet365</td>
                        <td>初盘</td>
                        <td>2.29</td>
                        <td>3.5</td>
                        <td>1.00</td>
                        <td>-6.5</td>
                        <td>0.98</td>
                        <td>1.00</td>
                        <td>216.5</td>
                        <td>0.98</td>
                    </tr>
                    <tr>
                        <td>终盘</td>
                        <td>2.29</td>
                        <td>3.5</td>
                        <td>1.00</td>
                        <td>-6.5</td>
                        <td>0.98</td>
                        <td>1.00</td>
                        <td>216.5</td>
                        <td>0.98</td>
                    </tr>
                    <tr>
                        <td rowspan="2">金宝博</td>
                        <td>初盘</td>
                        <td>2.29</td>
                        <td>3.5</td>
                        <td>1.00</td>
                        <td>-6.5</td>
                        <td>0.98</td>
                        <td>1.00</td>
                        <td>216.5</td>
                        <td>0.98</td>
                    </tr>
                    <tr>
                        <td>终盘</td>
                        <td>2.29</td>
                        <td>3.5</td>
                        <td>1.00</td>
                        <td>-6.5</td>
                        <td>0.98</td>
                        <td>1.00</td>
                        <td>216.5</td>
                        <td>0.98</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="attack">
                <p class="title">技术统计</p>
                <div class="part host">
                    <p class="name">休斯顿迪纳摩</p>
                    <table>
                        <thead>
                        <tr>
                            <th></th>
                            <th>投篮命中率</th>
                            <th>三分命中率</th>
                            <th>平均篮板</th>
                            <th>平均助攻</th>
                            <th>平均抢断</th>
                            <th>平均失误</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>季前赛</td>
                            <td>48.5%</td>
                            <td>37.5%</td>
                            <td>44.2</td>
                            <td>27.2</td>
                            <td>8.8</td>
                            <td>8.8</td>
                        </tr>
                        <tr>
                            <td>常规赛</td>
                            <td>48.5%</td>
                            <td>37.5%</td>
                            <td>44.2</td>
                            <td>27.2</td>
                            <td>8.8</td>
                            <td>8.8</td>
                        </tr>
                        <tr>
                            <td class="gray">近10场</td>
                            <td>48.5%</td>
                            <td>37.5%</td>
                            <td>44.2</td>
                            <td>27.2</td>
                            <td>8.8</td>
                            <td>8.8</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="part away">
                    <p class="name">休斯顿迪纳摩</p>
                    <table>
                        <thead>
                        <tr>
                            <th></th>
                            <th>投篮命中率</th>
                            <th>三分命中率</th>
                            <th>平均篮板</th>
                            <th>平均助攻</th>
                            <th>平均抢断</th>
                            <th>平均失误</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>季前赛</td>
                            <td>48.5%</td>
                            <td>37.5%</td>
                            <td>44.2</td>
                            <td>27.2</td>
                            <td>8.8</td>
                            <td>8.8</td>
                        </tr>
                        <tr>
                            <td>常规赛</td>
                            <td>48.5%</td>
                            <td>37.5%</td>
                            <td>44.2</td>
                            <td>27.2</td>
                            <td>8.8</td>
                            <td>8.8</td>
                        </tr>
                        <tr>
                            <td class="gray">近10场</td>
                            <td>48.5%</td>
                            <td>37.5%</td>
                            <td>44.2</td>
                            <td>27.2</td>
                            <td>8.8</td>
                            <td>8.8</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="battle" ma="0" ha="0">
                <p class="title">对赛往绩</p>
                <div class="cbox">
                    <button name="ma">相同赛事</button>
                    <button name="ha">相同主客</button>
                    <p class="num"><button class="on" name="number" value="10">近10场</button><button name="number" value="5">近5场</button></p>
                </div>
                <div class="con" ma="0" ha="0">
                    <dl num="10">
                        <dt class="win">5胜<p>场均<span>107.2</span>分</p></dt>
                        <dt class="lose">5胜<p>场均<span>107.2</span>分</p></dt>
                        <dd><p class="win" style="width: 50%"></p><p class="lose" style="width: 50%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">3胜<p>场均<span>107.2</span>分</p></dt>
                        <dt class="lose">2胜<p>场均<span>107.2</span>分</p></dt>
                        <dd><p class="win" style="width: 60%"></p><p class="lose" style="width: 40%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="10%">
                            <col num="2" width="10%">
                            <col num="3" width="">
                            <col num="4" width="10%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                            <col num="10" width="6.25%">
                            <col num="11" width="6.25%">
                            <col num="12" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>比分</th>
                            <th>客队</th>
                            <th>胜负</th>
                            <th>分差</th>
                            <th>让分盘</th>
                            <th>盘路</th>
                            <th>总分</th>
                            <th>总分盘</th>
                            <th>盘路</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="blue">负</td>
                            <td>1</td>
                            <td class="blue">-11.5</td>
                            <td class="blue">输</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="gray">走</td>
                            <td>207</td>
                            <td>221</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="con" ma="1" ha="0">
                    <dl num="10">
                        <dt class="win">5胜<p>场均<span>107.2</span>分</p></dt>
                        <dt class="lose">5胜<p>场均<span>107.2</span>分</p></dt>
                        <dd><p class="win" style="width: 50%"></p><p class="lose" style="width: 50%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">3胜<p>场均<span>107.2</span>分</p></dt>
                        <dt class="lose">2胜<p>场均<span>107.2</span>分</p></dt>
                        <dd><p class="win" style="width: 60%"></p><p class="lose" style="width: 40%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="10%">
                            <col num="2" width="10%">
                            <col num="3" width="">
                            <col num="4" width="10%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                            <col num="10" width="6.25%">
                            <col num="11" width="6.25%">
                            <col num="12" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>比分</th>
                            <th>客队</th>
                            <th>胜负</th>
                            <th>分差</th>
                            <th>让分盘</th>
                            <th>盘路</th>
                            <th>总分</th>
                            <th>总分盘</th>
                            <th>盘路</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="blue">负</td>
                            <td>1</td>
                            <td class="blue">-11.5</td>
                            <td class="blue">输</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="gray">走</td>
                            <td>207</td>
                            <td>221</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="con" ma="0" ha="1">
                    <dl num="10">
                        <dt class="win">5胜<p>场均<span>107.2</span>分</p></dt>
                        <dt class="lose">5胜<p>场均<span>107.2</span>分</p></dt>
                        <dd><p class="win" style="width: 50%"></p><p class="lose" style="width: 50%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">3胜<p>场均<span>107.2</span>分</p></dt>
                        <dt class="lose">2胜<p>场均<span>107.2</span>分</p></dt>
                        <dd><p class="win" style="width: 60%"></p><p class="lose" style="width: 40%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="10%">
                            <col num="2" width="10%">
                            <col num="3" width="">
                            <col num="4" width="10%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                            <col num="10" width="6.25%">
                            <col num="11" width="6.25%">
                            <col num="12" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>比分</th>
                            <th>客队</th>
                            <th>胜负</th>
                            <th>分差</th>
                            <th>让分盘</th>
                            <th>盘路</th>
                            <th>总分</th>
                            <th>总分盘</th>
                            <th>盘路</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="blue">负</td>
                            <td>1</td>
                            <td class="blue">-11.5</td>
                            <td class="blue">输</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="gray">走</td>
                            <td>207</td>
                            <td>221</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="con" ma="1" ha="1">
                    <dl num="10">
                        <dt class="win">5胜<p>场均<span>107.2</span>分</p></dt>
                        <dt class="lose">5胜<p>场均<span>107.2</span>分</p></dt>
                        <dd><p class="win" style="width: 50%"></p><p class="lose" style="width: 50%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">3胜<p>场均<span>107.2</span>分</p></dt>
                        <dt class="lose">2胜<p>场均<span>107.2</span>分</p></dt>
                        <dd><p class="win" style="width: 60%"></p><p class="lose" style="width: 40%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="10%">
                            <col num="2" width="10%">
                            <col num="3" width="">
                            <col num="4" width="10%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                            <col num="10" width="6.25%">
                            <col num="11" width="6.25%">
                            <col num="12" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>比分</th>
                            <th>客队</th>
                            <th>胜负</th>
                            <th>分差</th>
                            <th>让分盘</th>
                            <th>盘路</th>
                            <th>总分</th>
                            <th>总分盘</th>
                            <th>盘路</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="blue">负</td>
                            <td>1</td>
                            <td class="blue">-11.5</td>
                            <td class="blue">输</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="gray">走</td>
                            <td>207</td>
                            <td>221</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="history" ma="0" ha="0">
                <p class="title">近期战绩</p>
                <p class="team">
                    <button class="host on">休斯顿迪纳摩（美职业4）</button>
                    <button class="away">西雅图音速（美职业1）</button>
                </p>
                <div class="cbox">
                    <button name="ma">相同赛事</button>
                    <button name="ha">相同主客</button>
                    <p class="num"><button class="on" name="number" value="10">近10场</button><button name="number" value="5">近5场</button></p>
                </div>
                <div class="con host" ma="0" ha="0">
                    <dl num="10">
                        <dt class="win">5胜</dt>
                        <dt class="lose">5负</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="lose" style="width: 50%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">3胜</dt>
                        <dt class="lose">2胜</dt>
                        <dd><p class="win" style="width: 60%"></p><p class="lose" style="width: 40%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="10%">
                            <col num="2" width="10%">
                            <col num="3" width="">
                            <col num="4" width="10%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                            <col num="10" width="6.25%">
                            <col num="11" width="6.25%">
                            <col num="12" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>比分</th>
                            <th>客队</th>
                            <th>胜负</th>
                            <th>分差</th>
                            <th>让分盘</th>
                            <th>盘路</th>
                            <th>总分</th>
                            <th>总分盘</th>
                            <th>盘路</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="blue">负</td>
                            <td>1</td>
                            <td class="blue">-11.5</td>
                            <td class="blue">输</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="gray">走</td>
                            <td>207</td>
                            <td>221</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="con host" ma="1" ha="0">
                    <dl num="10">
                        <dt class="win">5胜</dt>
                        <dt class="lose">5负</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="lose" style="width: 50%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">3胜</dt>
                        <dt class="lose">2胜</dt>
                        <dd><p class="win" style="width: 60%"></p><p class="lose" style="width: 40%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="10%">
                            <col num="2" width="10%">
                            <col num="3" width="">
                            <col num="4" width="10%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                            <col num="10" width="6.25%">
                            <col num="11" width="6.25%">
                            <col num="12" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>比分</th>
                            <th>客队</th>
                            <th>胜负</th>
                            <th>分差</th>
                            <th>让分盘</th>
                            <th>盘路</th>
                            <th>总分</th>
                            <th>总分盘</th>
                            <th>盘路</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="blue">负</td>
                            <td>1</td>
                            <td class="blue">-11.5</td>
                            <td class="blue">输</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="gray">走</td>
                            <td>207</td>
                            <td>221</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="con host" ma="0" ha="1">
                    <dl num="10">
                        <dt class="win">5胜</dt>
                        <dt class="lose">5负</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="lose" style="width: 50%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">3胜</dt>
                        <dt class="lose">2胜</dt>
                        <dd><p class="win" style="width: 60%"></p><p class="lose" style="width: 40%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="10%">
                            <col num="2" width="10%">
                            <col num="3" width="">
                            <col num="4" width="10%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                            <col num="10" width="6.25%">
                            <col num="11" width="6.25%">
                            <col num="12" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>比分</th>
                            <th>客队</th>
                            <th>胜负</th>
                            <th>分差</th>
                            <th>让分盘</th>
                            <th>盘路</th>
                            <th>总分</th>
                            <th>总分盘</th>
                            <th>盘路</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="blue">负</td>
                            <td>1</td>
                            <td class="blue">-11.5</td>
                            <td class="blue">输</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="gray">走</td>
                            <td>207</td>
                            <td>221</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="con host" ma="1" ha="1">
                    <dl num="10">
                        <dt class="win">5胜</dt>
                        <dt class="lose">5负</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="lose" style="width: 50%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">3胜</dt>
                        <dt class="lose">2胜</dt>
                        <dd><p class="win" style="width: 60%"></p><p class="lose" style="width: 40%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="10%">
                            <col num="2" width="10%">
                            <col num="3" width="">
                            <col num="4" width="10%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                            <col num="10" width="6.25%">
                            <col num="11" width="6.25%">
                            <col num="12" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>比分</th>
                            <th>客队</th>
                            <th>胜负</th>
                            <th>分差</th>
                            <th>让分盘</th>
                            <th>盘路</th>
                            <th>总分</th>
                            <th>总分盘</th>
                            <th>盘路</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="blue">负</td>
                            <td>1</td>
                            <td class="blue">-11.5</td>
                            <td class="blue">输</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="gray">走</td>
                            <td>207</td>
                            <td>221</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="con away" ma="0" ha="0" style="display: none;">
                    <dl num="10">
                        <dt class="win">5胜</dt>
                        <dt class="lose">5负</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="lose" style="width: 50%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">3胜</dt>
                        <dt class="lose">2胜</dt>
                        <dd><p class="win" style="width: 60%"></p><p class="lose" style="width: 40%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="10%">
                            <col num="2" width="10%">
                            <col num="3" width="">
                            <col num="4" width="10%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                            <col num="10" width="6.25%">
                            <col num="11" width="6.25%">
                            <col num="12" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>比分</th>
                            <th>客队</th>
                            <th>胜负</th>
                            <th>分差</th>
                            <th>让分盘</th>
                            <th>盘路</th>
                            <th>总分</th>
                            <th>总分盘</th>
                            <th>盘路</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="blue">负</td>
                            <td>1</td>
                            <td class="blue">-11.5</td>
                            <td class="blue">输</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="gray">走</td>
                            <td>207</td>
                            <td>221</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="con away" ma="1" ha="0" style="display: none;">
                    <dl num="10">
                        <dt class="win">5胜</dt>
                        <dt class="lose">5负</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="lose" style="width: 50%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">3胜</dt>
                        <dt class="lose">2胜</dt>
                        <dd><p class="win" style="width: 60%"></p><p class="lose" style="width: 40%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="10%">
                            <col num="2" width="10%">
                            <col num="3" width="">
                            <col num="4" width="10%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                            <col num="10" width="6.25%">
                            <col num="11" width="6.25%">
                            <col num="12" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>比分</th>
                            <th>客队</th>
                            <th>胜负</th>
                            <th>分差</th>
                            <th>让分盘</th>
                            <th>盘路</th>
                            <th>总分</th>
                            <th>总分盘</th>
                            <th>盘路</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="blue">负</td>
                            <td>1</td>
                            <td class="blue">-11.5</td>
                            <td class="blue">输</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="gray">走</td>
                            <td>207</td>
                            <td>221</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="con away" ma="0" ha="1" style="display: none;">
                    <dl num="10">
                        <dt class="win">5胜</dt>
                        <dt class="lose">5负</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="lose" style="width: 50%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">3胜</dt>
                        <dt class="lose">2胜</dt>
                        <dd><p class="win" style="width: 60%"></p><p class="lose" style="width: 40%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="10%">
                            <col num="2" width="10%">
                            <col num="3" width="">
                            <col num="4" width="10%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                            <col num="10" width="6.25%">
                            <col num="11" width="6.25%">
                            <col num="12" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>比分</th>
                            <th>客队</th>
                            <th>胜负</th>
                            <th>分差</th>
                            <th>让分盘</th>
                            <th>盘路</th>
                            <th>总分</th>
                            <th>总分盘</th>
                            <th>盘路</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="blue">负</td>
                            <td>1</td>
                            <td class="blue">-11.5</td>
                            <td class="blue">输</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="gray">走</td>
                            <td>207</td>
                            <td>221</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="con away" ma="1" ha="1" style="display: none;">
                    <dl num="10">
                        <dt class="win">5胜</dt>
                        <dt class="lose">5负</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="lose" style="width: 50%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">3胜</dt>
                        <dt class="lose">2胜</dt>
                        <dd><p class="win" style="width: 60%"></p><p class="lose" style="width: 40%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="10%">
                            <col num="2" width="10%">
                            <col num="3" width="">
                            <col num="4" width="10%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                            <col num="10" width="6.25%">
                            <col num="11" width="6.25%">
                            <col num="12" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>比分</th>
                            <th>客队</th>
                            <th>胜负</th>
                            <th>分差</th>
                            <th>让分盘</th>
                            <th>盘路</th>
                            <th>总分</th>
                            <th>总分盘</th>
                            <th>盘路</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="blue">负</td>
                            <td>1</td>
                            <td class="blue">-11.5</td>
                            <td class="blue">输</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="gray">走</td>
                            <td>207</td>
                            <td>221</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td>NBA</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>110-111</td>
                            <td>休斯顿迪纳摩</td>
                            <td class="green">胜</td>
                            <td>1</td>
                            <td class="green">11.5</td>
                            <td class="green">赢</td>
                            <td>207.5</td>
                            <td>221</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
    </div>
    <div id="Play">
        <div class="abox">
            <ul>
                <li class="on" target="Match">比赛赛况</li>
                <li target="Data">数据分析</li>
                <li target="Total">统计比较</li>
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
    <script type="text/javascript" src="/pc/js/match_bk.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            setPage();
        }
    </script>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="/pc/css/match_bk.css">
@endsection