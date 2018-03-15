@if(isset($players) && count($players) > 0)
    <div class="player">
        <p class="title">球员统计</p>
        <p class="name">{{$match['hname']}}</p>
        @component('pc.match_detail.basket_cell.match_players_table',['players'=>$players['home'],'key'=>'host'])
        @endcomponent
        <p class="name">{{$match['aname']}}</p>
        @component('pc.match_detail.basket_cell.match_players_table',['players'=>$players['away'],'key'=>'away'])
        @endcomponent
    </div>
@endif