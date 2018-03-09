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
        @foreach($stage['combo'] as $combo)
            <tr class="total">
                <th colspan="8">
                    <p>
                        <a href="team.html" class="host"><b>{{$combo['hscore']}}</b>{{$combo['hname']}}</a>
                        <a href="team.html" class="away"><b>{{$combo['ascore']}}</b>{{$combo['aname']}}</a>
                    </p>
                </th>
            </tr>
            @foreach($combo['matches'] as $match)
                @component('pc.cell.league_list_match',['match'=>$match])
                @endcomponent
            @endforeach
        @endforeach
        </tbody>
    </table>
</div>