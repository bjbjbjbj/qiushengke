@extends('pc.layout.live_base')
@section('live_content')
    <div id="Score">
        <p class="title">比分统计</p>
        <div class="con">
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
                    <td class="total">52</td>
                </tr>
                <tr>
                    <td>洛杉矶湖人</td>
                    <td>24</td>
                    <td>28</td>
                    <td>10</td>
                    <td>-</td>
                    <td class="total">52</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div id="BKPlayer">
        <p class="title">球员数据</p>
        <div class="con">
            <div class="tab">
                <p class="host"><button class="on">波士顿凯尔特人</button></p>
                <p class="away"><button>洛杉矶湖人</button></p>
            </div>
            <table class="host" style="">
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
            <table class="away" style="display: none;">
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
@endsection

@section('navContent')
    <div class="home"><p class="abox"><a href="index.html"><img src="/pc/img/logo_image_n.png"></a></p></div>
    <div class="Column">
        <a href="/match/foot/immediate.html">足球</a>
        <a class="on" href="/match/basket/immediate_t.html">篮球</a>
        <a href="">主播</a>
        <a href="">手机APP</a>
    </div>
@endsection