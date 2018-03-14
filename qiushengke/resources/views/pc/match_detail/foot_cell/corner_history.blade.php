<?php
$rank = $analyse['rank'];
?>
<div class="history" ma="0" ha="0">
    <p class="title">对赛往绩</p>
    <p class="team">
        <button class="host on">{{$match['hname']}}
            @if($rank['leagueRank']['hLeagueRank'] > 0)
                （{{$rank['leagueRank']['hLeagueName']}}{{$rank['leagueRank']['hLeagueRank']}}）
            @endif
        </button>
        <button class="away">{{$match['aname']}}
            @if($rank['leagueRank']['aLeagueRank'] > 0)
                （{{$rank['leagueRank']['aLeagueName']}}{{$rank['leagueRank']['aLeagueRank']}}）
            @endif
        </button>
    </p>
    <div class="cbox">
        <button name="ma">相同赛事</button>
        <button name="ha">相同主客</button>
        <p class="num"><button class="on" name="number" value="10">近10场</button><button name="number" value="5">近5场</button></p>
    </div>
    @component('pc.match_detail.foot_cell.corner_history_item',['className'=>'host','ma'=>0,'ha'=>0,'currMatch'=>$match,'matches'=>$analyse['cornerRecentBattle']['home']['all']])
    @endcomponent
    @component('pc.match_detail.foot_cell.corner_history_item',['className'=>'host','ma'=>1,'ha'=>0,'currMatch'=>$match,'matches'=>$analyse['cornerRecentBattle']['home']['sameL']])
    @endcomponent
    @component('pc.match_detail.foot_cell.corner_history_item',['className'=>'host','ma'=>0,'ha'=>1,'currMatch'=>$match,'matches'=>$analyse['cornerRecentBattle']['home']['sameHA']])
    @endcomponent
    @component('pc.match_detail.foot_cell.corner_history_item',['className'=>'host','ma'=>1,'ha'=>1,'currMatch'=>$match,'matches'=>$analyse['cornerRecentBattle']['home']['sameHAL']])
    @endcomponent
    @component('pc.match_detail.foot_cell.corner_history_item',['show'=>0,'className'=>'away','ma'=>0,'ha'=>0,'currMatch'=>$match,'matches'=>$analyse['cornerRecentBattle']['away']['all']])
    @endcomponent
    @component('pc.match_detail.foot_cell.corner_history_item',['show'=>0,'className'=>'away','ma'=>1,'ha'=>0,'currMatch'=>$match,'matches'=>$analyse['cornerRecentBattle']['away']['sameL']])
    @endcomponent
    @component('pc.match_detail.foot_cell.corner_history_item',['show'=>0,'className'=>'away','ma'=>0,'ha'=>1,'currMatch'=>$match,'matches'=>$analyse['cornerRecentBattle']['away']['sameHA']])
    @endcomponent
    @component('pc.match_detail.foot_cell.corner_history_item',['show'=>0,'className'=>'away','ma'=>1,'ha'=>1,'currMatch'=>$match,'matches'=>$analyse['cornerRecentBattle']['away']['sameHAL']])
    @endcomponent
</div>