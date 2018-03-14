<div class="battle" ma="0" ha="0">
    <p class="title">对赛往绩</p>
    <div class="cbox">
        <button name="ma">相同赛事</button>
        <button name="ha">相同主客</button>
        <p class="num"><button class="on" name="number" value="10">近10场</button><button name="number" value="5">近5场</button></p>
    </div>
    @component('pc.match_detail.foot_cell.corner_battle_item',['currMatch'=>$match,'ma'=>0,'ha'=>0,'matches'=>$analyse['cornerHistoryBattle']['historyBattle']['nhnl']])
    @endcomponent
    @component('pc.match_detail.foot_cell.corner_battle_item',['ma'=>1,'ha'=>0,'matches'=>$analyse['cornerHistoryBattle']['historyBattle']['nhsl']])
    @endcomponent
    @component('pc.match_detail.foot_cell.corner_battle_item',['ma'=>0,'ha'=>1,'matches'=>$analyse['cornerHistoryBattle']['historyBattle']['shnl']])
    @endcomponent
    @component('pc.match_detail.foot_cell.corner_battle_item',['ma'=>1,'ha'=>1,'matches'=>$analyse['cornerHistoryBattle']['historyBattle']['shsl']])
    @endcomponent
</div>