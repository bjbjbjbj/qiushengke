<div id="Data" class="content" style="display: none;">
    <div class="odd default"></div>
    @if(isset($analyse['rank']))
        <?php
            $rank_host = isset($analyse['rank']['rank']['host']['all']) ? $analyse['rank']['rank']['host']['all'] : null;
            $rank_away = isset($analyse['rank']['rank']['away']['all']) ? $analyse['rank']['rank']['away']['all'] : null;
        ?>
        @component("phone.detail.football.cell.data_rank_cell", [
            'match'=>$match, 'host'=>$rank_host, 'away'=>$rank_away
        ]) @endcomponent
    @endif
    @if(isset($analyse['historyBattle']))
        @component("phone.detail.football.cell.data_battle_cell", [
            'battle'=>$analyse['historyBattle'], 'match'=>$match
        ]) @endcomponent
    @endif
    @if(isset($analyse['recentBattle']))
        @component("phone.detail.football.cell.data_history_cell", [
            'match'=>$match, 'home'=>isset($analyse['recentBattle']['home'])?$analyse['recentBattle']['home']:null,
            'away'=>isset($analyse['recentBattle']['away'])?$analyse['recentBattle']['away']:null,
        ]) @endcomponent
    @endif
    @if(isset($analyse['oddResult']))
        @component('phone.detail.football.cell.data_track_cell', [
            'match'=>$match, 'home'=>isset($analyse['oddResult']['home'])?$analyse['oddResult']['home']:null,
            'away'=>isset($analyse['oddResult']['away'])?$analyse['oddResult']['away']:null,
        ]) @endcomponent
    @endif
    @if(isset($analyse['schedule']))
        @component('phone.detail.football.cell.data_future_cell', [
            'match'=>$match, 'future'=>isset($analyse['schedule'])?$analyse['schedule']:null
        ]) @endcomponent
    @endif
</div>