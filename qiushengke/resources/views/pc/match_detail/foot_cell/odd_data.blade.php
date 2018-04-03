<div id="Odd" style="display: none;">
    <div class="tabLine">
        <button class="on" value="AllOdd">三合一</button>
        <button value="AsiaOdd">亚盘</button>
        <button value="EuropeOdd">欧盘</button>
        <button value="GoalOdd">大小球</button>
        <button value="Corner">角球</button>
    </div>
    <div id="AllOdd" style="display: ;">
        {{--盘口,另外数据刷新--}}
        @component('pc.match_detail.foot_cell.data_odd',['mid'=>$cur_match['mid']])
        @endcomponent
        @component('pc.match_detail.foot_cell.character_same_odd',['mid'=>$cur_match['mid'],'analyse'=>$analyse])
        @endcomponent
    </div>
    <div id="AsiaOdd" style="display: none;">
        <div class="tableIn">
            <table>
                <colgroup>
                    <col num="1" width="15%">
                    <col num="2">
                    <col num="3">
                    <col num="4">
                    <col num="5">
                    <col num="6">
                    <col num="7">
                    {{--<col num="8">--}}
                    {{--<col num="9">--}}
                    {{--<col num="10">--}}
                    {{--<col num="11" width="15%">--}}
                </colgroup>
                <thead>
                <tr>
                    <th rowspan="2">公司</th>
                    <th colspan="3">初盘</th>
                    <th colspan="3">终盘</th>
                    {{--<th colspan="3">即盘</th>--}}
                    {{--<th rowspan="2">历史同赔</th>--}}
                </tr>
                <tr>
                    <th class="yellow">主队</th>
                    <th class="yellow">盘口</th>
                    <th class="yellow">客队</th>
                    <th class="green">主队</th>
                    <th class="green">盘口</th>
                    <th class="green">客队</th>
                    {{--<th class="yellow">主队</th>--}}
                    {{--<th class="yellow">盘口</th>--}}
                    {{--<th class="yellow">客队</th>--}}
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <div id="EuropeOdd" style="display: none;">
        <div class="tableIn">
            <table>
                <colgroup>
                    <col num="1" width="18%">
                    <col num="2">
                    <col num="3">
                    <col num="4">
                    <col num="5">
                    <col num="6">
                    <col num="7">
                    {{--<col num="8">--}}
                    {{--<col num="9">--}}
                    {{--<col num="10">--}}
                    {{--<col num="11" width="20%">--}}
                </colgroup>
                <thead>
                <tr>
                    <th rowspan="2">公司</th>
                    <th colspan="3">初盘</th>
                    <th colspan="3">终盘</th>
                    {{--<th colspan="3">即盘</th>--}}
                    {{--<th rowspan="2">历史同赔</th>--}}
                </tr>
                <tr>
                    <th class="yellow">主胜</th>
                    <th class="yellow">平局</th>
                    <th class="yellow">主负</th>
                    <th class="green">主胜</th>
                    <th class="green">平局</th>
                    <th class="green">主负</th>
                    {{--<th class="yellow">主胜</th>--}}
                    {{--<th class="yellow">平局</th>--}}
                    {{--<th class="yellow">主负</th>--}}
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <div id="GoalOdd" style="display: none;">
        <div class="tableIn">
            <table>
                <colgroup>
                    <col num="1" width="17%">
                    <col num="2">
                    <col num="3">
                    <col num="4">
                    <col num="5">
                    <col num="6">
                    <col num="7">
                    {{--<col num="8">--}}
                    {{--<col num="9">--}}
                    {{--<col num="10">--}}
                    {{--<col num="11" width="15%">--}}
                </colgroup>
                <thead>
                <tr>
                    <th rowspan="2">公司</th>
                    <th colspan="3">初盘</th>
                    <th colspan="3">终盘</th>
                    {{--<th colspan="3">即盘</th>--}}
                    {{--<th rowspan="2">历史同赔</th>--}}
                </tr>
                <tr>
                    <th class="yellow">大球</th>
                    <th class="yellow">盘口</th>
                    <th class="yellow">小球</th>
                    <th class="green">大球</th>
                    <th class="green">盘口</th>
                    <th class="green">小球</th>
                    {{--<th class="yellow">大球</th>--}}
                    {{--<th class="yellow">盘口</th>--}}
                    {{--<th class="yellow">小球</th>--}}
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    @component('pc.match_detail.foot_cell.corner',['match'=>$cur_match,'analyse'=>$analyse])
    @endcomponent
</div>