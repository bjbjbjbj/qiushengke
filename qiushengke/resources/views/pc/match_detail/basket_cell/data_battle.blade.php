@if(isset($analyse) && isset($analyse['historyBattle']) && isset($analyse['historyBattle']['historyBattle']))
    <?php
    $historyBattle = $analyse['historyBattle']['historyBattle'];
    ?>
    <div class="battle" ma="0" ha="0">
        <p class="title">对赛往绩</p>
        <div class="cbox">
            <button name="ma">相同赛事</button>
            <button name="ha">相同主客</button>
            <p class="num"><button class="on" name="number" value="10">近10场</button><button name="number" value="5">近5场</button></p>
        </div>
        @component('pc.match_detail.basket_cell.data_battle_item',['ma'=>0,'ha'=>0,'data'=>$historyBattle['nhnl'],'tid'=>$match['hid']])
        @endcomponent
        @component('pc.match_detail.basket_cell.data_battle_item',['ma'=>1,'ha'=>0,'data'=>$historyBattle['nhsl'],'tid'=>$match['hid']])
        @endcomponent
        @component('pc.match_detail.basket_cell.data_battle_item',['ma'=>0,'ha'=>1,'data'=>$historyBattle['shnl'],'tid'=>$match['hid']])
        @endcomponent
        @component('pc.match_detail.basket_cell.data_battle_item',['ma'=>1,'ha'=>1,'data'=>$historyBattle['shsl'],'tid'=>$match['hid']])
        @endcomponent
    </div>
@endif