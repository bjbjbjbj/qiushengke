<?php
//主动构建结果
if(isset($matches) && $matches == 1){
}
?>
<table num="{{$key}}" @if(!$show) style="display: none" @endif>
    <colgroup>
        <col num="1" width="">
        <col num="2" width="4.7%">
        <col num="3" width="4.7%">
        <col num="4" width="4.7%">
        <col num="5" width="4.7%">
        <col num="6" width="4.7%">
        <col num="7" width="4.7%">
        <col num="8" width="4.7%">
        <col num="9" width="4.7%">
        <col num="10" width="4.7%">
        <col num="11" width="4.7%">
        <col num="12" width="4.7%">
        <col num="13" width="4.7%">
        <col num="14" width="4.7%">
        <col num="15" width="4.7%">
        <col num="16" width="4.7%">
        <col num="17" width="4.7%">
        <col num="18" width="4.7%">
        <col num="19" width="4.7%">
    </colgroup>
    <thead>
    <tr>
        <th colspan="7"></th>
        <th colspan="6">相同主客</th>
        <th colspan="6">相同赛事</th>
    </tr>
    <tr>
        <th>球队</th>
        <th>场均<br/>进球</th>
        <th>场均<br/>失球</th>
        <th>进球<br/>场次</th>
        <th>胜率</th>
        <th>平率</th>
        <th>负率</th>
        <th class="green">场均<br/>进球</th>
        <th class="green">场均<br/>失球</th>
        <th class="green">进球<br/>场次</th>
        <th class="yellow">胜率</th>
        <th class="yellow">平率</th>
        <th class="yellow">负率</th>
        <th class="green">场均<br/>进球</th>
        <th class="green">场均<br/>失球</th>
        <th class="green">进球<br/>场次</th>
        <th class="yellow">胜率</th>
        <th class="yellow">平率</th>
        <th class="yellow">负率</th>
    </tr>
    </thead>
    <tbody>
    @if(isset($attribute['home']))
        @component('pc.match_detail.foot_cell.data_attribute_item_tr',['team'=>$match['hname'],'attribute'=>$attribute['home']])
        @endcomponent
    @endif
    @if(isset($attribute['away']))
        @component('pc.match_detail.foot_cell.data_attribute_item_tr',['team'=>$match['aname'],'attribute'=>$attribute['away']])
        @endcomponent
    @endif
    </tbody>
</table>