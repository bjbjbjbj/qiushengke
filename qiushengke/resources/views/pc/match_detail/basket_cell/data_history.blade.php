<?php
$recentBattle = $analyse['recentBattle'];
?>
<div class="history" ma="0" ha="0">
    <p class="title">近期战绩</p>
    <p class="team">
        <button class="host on">{{$match['hname']}}</button>
        <button class="away">{{$match['aname']}}</button>
    </p>
    <div class="cbox">
        <button name="ma">相同赛事</button>
        <button name="ha">相同主客</button>
        <p class="num"><button class="on" name="number" value="10">近10场</button><button name="number" value="5">近5场</button></p>
    </div>
    @component('pc.match_detail.basket_cell.data_history_item',['tid'=>$match['hid'],'matches'=>$recentBattle['home']['all'],'className'=>'host','ma'=>0,'ha'=>0,'show'=>1,'fill_key'=>1])
    @endcomponent
    @component('pc.match_detail.basket_cell.data_history_item',['tid'=>$match['hid'],'matches'=>$recentBattle['home']['sameL'],'className'=>'host','ma'=>1,'ha'=>0,'show'=>1,'fill_key'=>2])
    @endcomponent
    @component('pc.match_detail.basket_cell.data_history_item',['tid'=>$match['hid'],'matches'=>$recentBattle['home']['sameHA'],'className'=>'host','ma'=>0,'ha'=>1,'show'=>1,'fill_key'=>3])
    @endcomponent
    @component('pc.match_detail.basket_cell.data_history_item',['tid'=>$match['hid'],'matches'=>$recentBattle['home']['sameHAL'],'className'=>'host','ma'=>1,'ha'=>1,'show'=>1,'fill_key'=>4])
    @endcomponent
    @component('pc.match_detail.basket_cell.data_history_item',['tid'=>$match['aid'],'matches'=>$recentBattle['away']['all'],'className'=>'away','ma'=>0,'ha'=>0,'show'=>0,'fill_key'=>5])
    @endcomponent
    @component('pc.match_detail.basket_cell.data_history_item',['tid'=>$match['aid'],'matches'=>$recentBattle['away']['sameL'],'className'=>'away','ma'=>1,'ha'=>0,'show'=>0,'fill_key'=>6])
    @endcomponent
    @component('pc.match_detail.basket_cell.data_history_item',['tid'=>$match['aid'],'matches'=>$recentBattle['away']['sameHA'],'className'=>'away','ma'=>0,'ha'=>1,'show'=>0,'fill_key'=>7])
    @endcomponent
    @component('pc.match_detail.basket_cell.data_history_item',['tid'=>$match['aid'],'matches'=>$recentBattle['away']['sameHAL'],'className'=>'away','ma'=>1,'ha'=>1,'show'=>0,'fill_key'=>8])
    @endcomponent
</div>