@foreach($matches as $match)
    @component('pc.cell.league_list_match_bk',['match'=>$match])
    @endcomponent
@endforeach