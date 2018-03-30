@if(isset($battle['historyBattleResult']))
<div class="battle matchTable default" ha="0" le="0">
    <div class="title">
        交锋往绩<button class="close"></button>
        <div class="labelbox">
            <label for="Battle_HA"><input type="checkbox" name="battle" value="ha" id="Battle_HA"><span></span>同主客</label>
            <label for="Battle_LE"><input type="checkbox" name="battle" value="le" id="Battle_LE"><span></span>同赛事</label>
        </div>
    </div>
    @component("phone.detail.football.cell.data_battle_item_cell", [
        'ha'=>0, 'le'=>0, 'hid'=>$match['hid'], 'result'=>$battle['historyBattleResult']['all']
        , 'data'=>$battle['historyBattle']['nhnl']
    ]) @endcomponent
    @component("phone.detail.football.cell.data_battle_item_cell", [
        'ha'=>1, 'le'=>0, 'hid'=>$match['hid'], 'result'=>$battle['historyBattleResult']['team']
        , 'data'=>$battle['historyBattle']['shnl']
    ]) @endcomponent
    @component("phone.detail.football.cell.data_battle_item_cell", [
        'ha'=>0, 'le'=>1, 'hid'=>$match['hid'], 'result'=>$battle['historyBattleResult']['league']
        , 'data'=>$battle['historyBattle']['nhsl']
    ]) @endcomponent
    @component("phone.detail.football.cell.data_battle_item_cell", [
        'ha'=>1, 'le'=>1, 'hid'=>$match['hid'], 'result'=>$battle['historyBattleResult']['both']
        , 'data'=>$battle['historyBattle']['shsl']
    ]) @endcomponent
</div>
@endif