<div id="Corner" style="display: none;">
    <div class="odd" style="display: none">
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
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>即盘</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
        </table>
    </div>
    @component('pc.match_detail.foot_cell.corner_data',['match'=>$match,'analyse'=>$analyse])
    @endcomponent
    @if(isset($analyse['cornerHistoryBattle']))
        @component('pc.match_detail.foot_cell.corner_battle',['match'=>$match,'analyse'=>$analyse])
        @endcomponent
    @endif
    @if(isset($analyse['cornerRecentBattle']))
        @component('pc.match_detail.foot_cell.corner_history',['match'=>$match,'analyse'=>$analyse])
        @endcomponent
    @endif
    <div class="noList"></div>
</div>