<div id="Match" style="display: ;">
    @component('pc.match_detail.foot_cell.base_lineup',['match'=>$match,'rank'=>$rank,'lineup'=>$lineup])
    @endcomponent
    @component('pc.match_detail.foot_cell.base_total',['tech'=>$tech])
    @endcomponent
    @component('pc.match_detail.foot_cell.base_event',['match'=>$match,'events'=>$tech])
    @endcomponent
</div>