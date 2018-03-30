@if(isset($base['historyBattle']) && isset($base['historyBattle']['historyBattle']))
    <div class="battle matchTable default" ha="0" le="0">
        <div class="title">
            交锋往绩<button class="close"></button>
            <div class="labelbox">
                <label for="Battle_HA"><input type="checkbox" name="battle" value="ha" id="Battle_HA"><span></span>同主客</label>
                <label for="Battle_LE"><input type="checkbox" name="battle" value="le" id="Battle_LE"><span></span>同赛事</label>
            </div>
        </div>
        @component("phone.detail.basketball.cell.basketball_detail_hbattle",['cdn'=>$cdn,'base'=>$match,'data'=>$base['historyBattle']['historyBattle']['nhnl'],'league'=>0,'ha'=>0,'hid'=>$match['hid']])
        @endcomponent
        @component("phone.detail.basketball.cell.basketball_detail_hbattle",['cdn'=>$cdn,'base'=>$match,'data'=>$base['historyBattle']['historyBattle']['shnl'],'league'=>0,'ha'=>1,'hid'=>$match['hid']])
        @endcomponent
        @component("phone.detail.basketball.cell.basketball_detail_hbattle",['cdn'=>$cdn,'base'=>$match,'data'=>$base['historyBattle']['historyBattle']['nhsl'],'league'=>1,'ha'=>0,'hid'=>$match['hid']])
        @endcomponent
        @component("phone.detail.basketball.cell.basketball_detail_hbattle",['cdn'=>$cdn,'base'=>$match,'data'=>$base['historyBattle']['historyBattle']['shsl'],'league'=>1,'ha'=>1,'hid'=>$match['hid']])
        @endcomponent
    </div>
@endif
@if(isset($base['recentBattle']))
    <?php $recent = $base['recentBattle']; ?>
    <div class="history matchTable default" ha="0" le="0">
        <div class="title">
            近期战绩<button class="close"></button>
            <div class="labelbox">
                <label for="History_HA"><input type="checkbox" name="history" value="ha" id="History_HA"><span></span>同主客</label>
                <label for="History_LE"><input type="checkbox" name="history" value="le" id="History_LE"><span></span>同赛事</label>
            </div>
        </div>

        @component("phone.detail.basketball.cell.basketball_detail_recenet_battle_head", ['cdn'=>$cdn,'base'=>$match,'ha'=>0, 'le'=>0, 'hmatch'=>$recent['home']['all'], 'hid'=>$match['hid'],'amatch'=>$recent['away']['all'], 'aid'=>$match['aid']])
        @endcomponent
        @component("phone.detail.basketball.cell.basketball_detail_recenet_battle_head", ['cdn'=>$cdn,'base'=>$match,'ha'=>1, 'le'=>0, 'hmatch'=>$recent['home']['sameHA'], 'hid'=>$match['hid'],'amatch'=>$recent['away']['sameHA'], 'aid'=>$match['aid']])
        @endcomponent
        @component("phone.detail.basketball.cell.basketball_detail_recenet_battle_head", ['cdn'=>$cdn,'base'=>$match,'ha'=>0, 'le'=>1, 'hmatch'=>$recent['home']['sameL'], 'hid'=>$match['hid'],'amatch'=>$recent['away']['sameL'], 'aid'=>$match['aid']])
        @endcomponent
        @component("phone.detail.basketball.cell.basketball_detail_recenet_battle_head", ['cdn'=>$cdn,'base'=>$match,'ha'=>1, 'le'=>1, 'hmatch'=>$recent['home']['sameHAL'], 'hid'=>$match['hid'],'amatch'=>$recent['away']['sameHAL'], 'aid'=>$match['aid']])
        @endcomponent

        @if(isset($recent['home']))
            <p class="teamName"><span>{{$match['hname']}}</span></p>
            @component("phone.detail.basketball.cell.basketball_detail_recent_battle", ['cdn'=>$cdn,'base'=>$base,'ha'=>0, 'le'=>0, 'data'=>$recent['home']['all'], 'hid'=>$match['hid']])
            @endcomponent
            @component("phone.detail.basketball.cell.basketball_detail_recent_battle", ['cdn'=>$cdn,'base'=>$base,'ha'=>1, 'le'=>0, 'data'=>$recent['home']['sameHA'], 'hid'=>$match['hid']])
            @endcomponent
            @component("phone.detail.basketball.cell.basketball_detail_recent_battle", ['cdn'=>$cdn,'base'=>$base,'ha'=>0, 'le'=>1, 'data'=>$recent['home']['sameL'], 'hid'=>$match['hid']])
            @endcomponent
            @component("phone.detail.basketball.cell.basketball_detail_recent_battle", ['cdn'=>$cdn,'base'=>$base,'ha'=>1, 'le'=>1, 'data'=>$recent['home']['sameHAL'], 'hid'=>$match['hid']])
            @endcomponent
        @endif
        @if(isset($recent['away']))
            <p class="teamName"><span>{{$match['aname']}}</span></p>
            @component("phone.detail.basketball.cell.basketball_detail_recent_battle", ['cdn'=>$cdn,'base'=>$base,'ha'=>0, 'le'=>0, 'data'=>$recent['away']['all'], 'hid'=>$match['aid']])
            @endcomponent
            @component("phone.detail.basketball.cell.basketball_detail_recent_battle", ['cdn'=>$cdn,'base'=>$base,'ha'=>1, 'le'=>0, 'data'=>$recent['away']['sameHA'], 'hid'=>$match['aid']])
            @endcomponent
            @component("phone.detail.basketball.cell.basketball_detail_recent_battle", ['cdn'=>$cdn,'base'=>$base,'ha'=>0, 'le'=>1, 'data'=>$recent['away']['sameL'], 'hid'=>$match['aid']])
            @endcomponent
            @component("phone.detail.basketball.cell.basketball_detail_recent_battle", ['cdn'=>$cdn,'base'=>$base,'ha'=>1, 'le'=>1, 'data'=>$recent['away']['sameHAL'], 'hid'=>$match['aid']])
            @endcomponent
        @endif
    </div>
@endif
@if(isset($base['oddResult']))
    @component('phone.detail.football.cell.data_track_cell', [
        'match'=>$match, 'home'=>$base['oddResult']['home'],
        'away'=>$base['oddResult']['away'],
    ]) @endcomponent
@endif
@if(isset($base['schedule']))
    @component('phone.detail.football.cell.data_future_cell', [
        'match'=>$match, 'future'=>$base['schedule']
    ]) @endcomponent
@endif