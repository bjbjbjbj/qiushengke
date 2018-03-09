<div class="con" num="{{$num}}" style="{{$show?'':'display: none;'}}">
    <table class="match">
        <colgroup>
            <col num="1" width="10%">
            <col num="2" width="">
            <col num="3" width="32px">
            <col num="4" width="7%">
            <col num="5" width="32px">
            <col num="6" width="">
            <col num="7" width="28%">
            <col num="8" width="14%">
        </colgroup>
        <thead>
        <tr>
            <th>时间</th>
            <th colspan="5">对阵</th>
            <th>指数</th>
            <th>分析</th>
        </tr>
        </thead>
        <tbody>
        @foreach($stage['matches'] as $match)
            @component('pc.cell.league_list_match',['match'=>$match])
            @endcomponent
        @endforeach
        </tbody>
    </table>
</div>