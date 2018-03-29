<div class="battle matchTable default" ha="0" le="0">
    <div class="title">
        交锋往绩<button class="close"></button>
        <div class="labelbox">
            <label for="Corner_Battle_HA"><input type="checkbox" name="corner_battle" value="ha" id="Corner_Battle_HA"><span></span>同主客</label>
            <label for="Corner_Battle_LE"><input type="checkbox" name="corner_battle" value="le" id="Corner_Battle_LE"><span></span>同赛事</label>
        </div>
    </div>
    @component('phone.detail.football.cell.team_corner_history_item_cell', [
        'ha'=>0, 'le'=>0, 'tid'=>$match['hid'],
        'result'=>$history['historyBattleResult']['all'], 'data'=>$history['historyBattle']['nhnl']
    ])
    @endcomponent
    @component('phone.detail.football.cell.team_corner_history_item_cell', [
        'ha'=>1, 'le'=>0, 'tid'=>$match['hid'],
        'result'=>$history['historyBattleResult']['team'], 'data'=>$history['historyBattle']['shnl']
    ])
    @endcomponent
    @component('phone.detail.football.cell.team_corner_history_item_cell', [
        'ha'=>0, 'le'=>1, 'tid'=>$match['hid'],
        'result'=>$history['historyBattleResult']['league'], 'data'=>$history['historyBattle']['nhsl']
    ])
    @endcomponent
    @component('phone.detail.football.cell.team_corner_history_item_cell', [
        'ha'=>1, 'le'=>1, 'tid'=>$match['hid'],
        'result'=>$history['historyBattleResult']['both'], 'data'=>$history['historyBattle']['shsl']
    ])
    @endcomponent
</div>