@extends('pc.layout.matchdetail_base')
@section('navContent')
    <div class="home"><p class="abox"><a href="index.html"><img src="/pc/img/logo_image_n.png"></a></p></div>
    <div class="Column">
        <a class="on">足球</a>
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
        @component('pc.match_detail.foot_cell.character')
        @endcomponent
        @component('pc.match_detail.foot_cell.data')
        @endcomponent
        <div id="Corner" style="display: none;">
            <div class="odd">
                <p class="title">角球指数</p>
                <table>
                    <tr>
                        <th></th>
                        <th>大球</th>
                        <th>盘口</th>
                        <th>小球</th>
                    </tr>
                    <tr>
                        <td>初盘</td>
                        <td>0.88</td>
                        <td>9.50</td>
                        <td>0.92</td>
                    </tr>
                    <tr>
                        <td>即盘</td>
                        <td>0.88</td>
                        <td>9.50</td>
                        <td>0.92</td>
                    </tr>
                </table>
            </div>
            <div class="data">
                <p class="title">数据统计</p>
                <p class="tabBox"><button class="on" value="10">近10场</button><button value="5">近5场</button></p>
                <table num="10">
                    <tr>
                        <th>球队</th>
                        <th>得球</th>
                        <th>失球</th>
                        <th>净胜</th>
                        <th>总数</th>
                        <th>大球率</th>
                    </tr>
                    <tr>
                        <td>休斯顿迪纳摩</td>
                        <td>5</td>
                        <td>5</td>
                        <td>0</td>
                        <td>10.33</td>
                        <td>44%</td>
                    </tr>
                    <tr>
                        <td>休斯顿迪纳摩</td>
                        <td>5</td>
                        <td>5</td>
                        <td>0</td>
                        <td>10.33</td>
                        <td>44%</td>
                    </tr>
                </table>
                <table num="5" style="display: none;">
                    <tr>
                        <th>球队</th>
                        <th>得球</th>
                        <th>失球</th>
                        <th>净胜</th>
                        <th>总数</th>
                        <th>大球率</th>
                    </tr>
                    <tr>
                        <td>休斯顿迪纳摩</td>
                        <td>5</td>
                        <td>5</td>
                        <td>0</td>
                        <td>10.33</td>
                        <td>44%</td>
                    </tr>
                    <tr>
                        <td>休斯顿迪纳摩</td>
                        <td>5</td>
                        <td>5</td>
                        <td>0</td>
                        <td>10.33</td>
                        <td>44%</td>
                    </tr>
                </table>
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
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="120px">
                            <col num="2" width="120px">
                            <col num="3" width="">
                            <col num="4" width="11.5%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>角球比分（半场）</th>
                            <th>客队</th>
                            <th colspan="3">盘口</th>
                            <th>大小</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="con" ma="1" ha="0">
                    <dl num="10">
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="120px">
                            <col num="2" width="120px">
                            <col num="3" width="">
                            <col num="4" width="11.5%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>角球比分（半场）</th>
                            <th>客队</th>
                            <th colspan="3">盘口</th>
                            <th>大小</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="con" ma="0" ha="1">
                    <dl num="10">
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="120px">
                            <col num="2" width="120px">
                            <col num="3" width="">
                            <col num="4" width="11.5%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>角球比分（半场）</th>
                            <th>客队</th>
                            <th colspan="3">盘口</th>
                            <th>大小</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="con" ma="1" ha="1">
                    <dl num="10">
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="120px">
                            <col num="2" width="120px">
                            <col num="3" width="">
                            <col num="4" width="11.5%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>角球比分（半场）</th>
                            <th>客队</th>
                            <th colspan="3">盘口</th>
                            <th>大小</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="history" ma="0" ha="0">
                <p class="title">对赛往绩</p>
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
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="120px">
                            <col num="2" width="120px">
                            <col num="3" width="">
                            <col num="4" width="11.5%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>角球比分（半场）</th>
                            <th>客队</th>
                            <th colspan="3">盘口</th>
                            <th>大小</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="con host" ma="1" ha="0">
                    <dl num="10">
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="120px">
                            <col num="2" width="120px">
                            <col num="3" width="">
                            <col num="4" width="11.5%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>角球比分（半场）</th>
                            <th>客队</th>
                            <th colspan="3">盘口</th>
                            <th>大小</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="con host" ma="0" ha="1">
                    <dl num="10">
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="120px">
                            <col num="2" width="120px">
                            <col num="3" width="">
                            <col num="4" width="11.5%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>角球比分（半场）</th>
                            <th>客队</th>
                            <th colspan="3">盘口</th>
                            <th>大小</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="con host" ma="1" ha="1">
                    <dl num="10">
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="120px">
                            <col num="2" width="120px">
                            <col num="3" width="">
                            <col num="4" width="11.5%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>角球比分（半场）</th>
                            <th>客队</th>
                            <th colspan="3">盘口</th>
                            <th>大小</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="con away" ma="0" ha="0" style="display: none;">
                    <dl num="10">
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="120px">
                            <col num="2" width="120px">
                            <col num="3" width="">
                            <col num="4" width="11.5%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                        </colgroup>
                        <thead>
                        <th>赛事</th>
                        <tr>
                            <th>日期</th>
                            <th>主队</th>
                            <th>角球比分（半场）</th>
                            <th>客队</th>
                            <th colspan="3">盘口</th>
                            <th>大小</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="con away" ma="1" ha="0" style="display: none;">
                    <dl num="10">
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="120px">
                            <col num="2" width="120px">
                            <col num="3" width="">
                            <col num="4" width="11.5%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>角球比分（半场）</th>
                            <th>客队</th>
                            <th colspan="3">盘口</th>
                            <th>大小</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="con away" ma="0" ha="1" style="display: none;">
                    <dl num="10">
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="120px">
                            <col num="2" width="120px">
                            <col num="3" width="">
                            <col num="4" width="11.5%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>角球比分（半场）</th>
                            <th>客队</th>
                            <th colspan="3">盘口</th>
                            <th>大小</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="con away" ma="1" ha="1" style="display: none;">
                    <dl num="10">
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <dl num="5" style="display: none;">
                        <dt class="win">大球50%</dt>
                        <dt class="draw">走水20%</dt>
                        <dt class="lose">小球30%</dt>
                        <dd><p class="win" style="width: 50%"></p><p class="draw" style="width: 20%"></p><p class="lose" style="width: 30%"></p></dd>
                    </dl>
                    <table>
                        <colgroup>
                            <col num="1" width="120px">
                            <col num="2" width="120px">
                            <col num="3" width="">
                            <col num="4" width="11.5%">
                            <col num="5" width="">
                            <col num="6" width="6.25%">
                            <col num="7" width="6.25%">
                            <col num="8" width="6.25%">
                            <col num="9" width="6.25%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>日期</th>
                            <th>主队</th>
                            <th>角球比分（半场）</th>
                            <th>客队</th>
                            <th colspan="3">盘口</th>
                            <th>大小</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="gray">走</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="yellow">小</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
                        </tr>
                        <tr>
                            <td><p class="line" style="background: #6378e4;"></p>亚洲杯</td>
                            <td>17.07.27</td>
                            <td>西雅图音速</td>
                            <td>9-3<span class="half">（3-2）</span></td>
                            <td>休斯顿迪纳摩</td>
                            <td>0.82</td>
                            <td>9.50</td>
                            <td>0.82</td>
                            <td class="green">大</td>
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
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="/pc/css/match.css">
@endsection