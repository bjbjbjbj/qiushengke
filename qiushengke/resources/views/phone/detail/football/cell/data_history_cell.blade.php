<div class="history matchTable default" ha="0" le="0">
    <div class="title">
        近期战绩<button class="close"></button>
        <div class="labelbox">
            <label for="History_HA"><input type="checkbox" name="history" value="ha" id="History_HA"><span></span>同主客</label>
            <label for="History_LE"><input type="checkbox" name="history" value="le" id="History_LE"><span></span>同赛事</label>
        </div>
    </div>
    @if(isset($home) && isset($home['statistic']))
        <p class="teamName"><span>{{$match['hname']}}</span></p>
        @component('phone.detail.football.cell.data_history_item_cell', [
            'ha'=>0, 'le'=>0, 'hid'=>$match['hid']
            ,'result'=>$home['statistic']['all'], 'data'=>$home['all']
        ])
        @endcomponent
        @component('phone.detail.football.cell.data_history_item_cell', [
            'ha'=>1, 'le'=>0, 'hid'=>$match['hid']
            ,'result'=>$home['statistic']['sameHA'], 'data'=>$home['sameHA']
        ])
        @endcomponent
        @component('phone.detail.football.cell.data_history_item_cell', [
            'ha'=>0, 'le'=>1, 'hid'=>$match['hid']
            ,'result'=>$home['statistic']['sameL'], 'data'=>$home['sameL']
        ])
        @endcomponent
        @component('phone.detail.football.cell.data_history_item_cell', [
            'ha'=>1, 'le'=>1, 'hid'=>$match['hid']
            ,'result'=>$home['statistic']['sameHAL'], 'data'=>$home['sameHAL']
        ])
        @endcomponent
    @endif
    @if(isset($away) && isset($away['statistic']))
        <p class="teamName"><span>{{$match['aname']}}</span></p>
        @component('phone.detail.football.cell.data_history_item_cell', [
            'ha'=>0, 'le'=>0, 'hid'=>$match['aid']
            ,'result'=>$away['statistic']['sameHAL'], 'data'=>$away['sameHAL']
        ])
        @endcomponent
        @component('phone.detail.football.cell.data_history_item_cell', [
            'ha'=>1, 'le'=>0, 'hid'=>$match['aid']
            ,'result'=>$away['statistic']['sameHAL'], 'data'=>$away['sameHAL']
        ])
        @endcomponent
        @component('phone.detail.football.cell.data_history_item_cell', [
            'ha'=>0, 'le'=>1, 'hid'=>$match['aid']
            ,'result'=>$away['statistic']['sameHAL'], 'data'=>$away['sameHAL']
        ])
        @endcomponent
        @component('phone.detail.football.cell.data_history_item_cell', [
            'ha'=>1, 'le'=>1, 'hid'=>$match['aid']
            ,'result'=>$away['statistic']['sameHAL'], 'data'=>$away['sameHAL']
        ])
        @endcomponent
    @endif
</div>