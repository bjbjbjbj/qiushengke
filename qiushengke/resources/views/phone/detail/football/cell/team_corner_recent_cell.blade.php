<div class="history matchTable default" ha="0" le="0">
    <div class="title">
        近期战绩<button class="close"></button>
        <div class="labelbox">
            <label for="Corner_History_HA"><input type="checkbox" name="corner_history" value="ha" id="Corner_History_HA"><span></span>同主客</label>
            <label for="Corner_History_LE"><input type="checkbox" name="corner_history" value="le" id="Corner_History_LE"><span></span>同赛事</label>
        </div>
    </div>
    @if(isset($recent['home']))
        <p class="teamName"><span>{{$match['hname']}}</span></p>
        @component('phone.detail.football.cell.team_corner_recent_item_cell', [
            'tid'=>$match['hid'], 'ha'=>0, 'le'=>0, 'data'=>$recent['home']['all'], 'result'=>$recent['home']['statistic']['all']
        ]) @endcomponent
        @component('phone.detail.football.cell.team_corner_recent_item_cell', [
            'tid'=>$match['hid'], 'ha'=>1, 'le'=>0, 'data'=>$recent['home']['sameHA'], 'result'=>$recent['home']['statistic']['sameHA']
        ]) @endcomponent
        @component('phone.detail.football.cell.team_corner_recent_item_cell', [
            'tid'=>$match['hid'], 'ha'=>0, 'le'=>1, 'data'=>$recent['home']['sameL'], 'result'=>$recent['home']['statistic']['sameL']
        ]) @endcomponent
        @component('phone.detail.football.cell.team_corner_recent_item_cell', [
            'tid'=>$match['hid'], 'ha'=>1, 'le'=>1, 'data'=>$recent['home']['sameHAL'], 'result'=>$recent['home']['statistic']['sameHAL']
        ]) @endcomponent
    @endif
    @if(isset($recent['away']))
        <p class="teamName"><span>{{$match['aname']}}</span></p>
        @component('phone.detail.football.cell.team_corner_recent_item_cell', [
            'tid'=>$match['aid'], 'ha'=>0, 'le'=>0, 'data'=>$recent['away']['all'], 'result'=>$recent['away']['statistic']['all']
        ]) @endcomponent
        @component('phone.detail.football.cell.team_corner_recent_item_cell', [
            'tid'=>$match['aid'], 'ha'=>1, 'le'=>0, 'data'=>$recent['away']['sameHA'], 'result'=>$recent['away']['statistic']['sameHA']
        ]) @endcomponent
        @component('phone.detail.football.cell.team_corner_recent_item_cell', [
            'tid'=>$match['aid'], 'ha'=>0, 'le'=>1, 'data'=>$recent['away']['sameL'], 'result'=>$recent['away']['statistic']['sameL']
        ]) @endcomponent
        @component('phone.detail.football.cell.team_corner_recent_item_cell', [
            'tid'=>$match['aid'], 'ha'=>1, 'le'=>1, 'data'=>$recent['away']['sameHAL'], 'result'=>$recent['away']['statistic']['sameHAL']
        ]) @endcomponent
    @endif
</div>