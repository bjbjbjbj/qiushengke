<table>
    <colgroup>
        <col num="1" width="10%">
        <col num="2" width="10%">
        <col num="3" width="">
        <col num="4" width="10%">
        <col num="5" width="">
        <col num="6" width="6.25%">
        <col num="7" width="6.25%">
        <col num="8" width="6.25%">
        <col num="9" width="6.25%">
        <col num="10" width="6.25%">
        <col num="11" width="6.25%">
        <col num="12" width="6.25%">
    </colgroup>
    <thead>
    <tr>
        <th>赛事</th>
        <th>日期</th>
        <th>主队</th>
        <th>比分</th>
        <th>客队</th>
        <th>胜负</th>
        <th>分差</th>
        <th>让分盘</th>
        <th>盘路</th>
        <th>总分</th>
        <th>总分盘</th>
        <th>盘路</th>
    </tr>
    </thead>
    <tbody>
    @foreach($matches as $match)
        <tr>
            <td>{{$match['league']}}</td>
            <td>{{date('Y.m.d',strtotime($match['time']))}}</td>
            <td>{{$match['hname']}}</td>
            <td>{{$match['hscore']}}-{{$match['ascore']}}</td>
            <td>{{$match['aname']}}</td>
            <td class="{{$match['ouCss']}}">{{$match['ouResultCn']}}</td>
            <td>{{$match['hscore']-$match['ascore']}}</td>
            @if($match['asiamiddle2'] < 0)
                <td class="blue">{{$match['asiamiddle2']}}</td>
            @else
                <td class="green">{{$match['asiamiddle2']}}</td>
            @endif
            <td class="{{$match['asiaCss']}}">{{$match['asiaResultCn']}}</td>
            <td>{{$match['hscore']+$match['ascore']}}</td>
            <td>{{$match['goalmiddle2']}}</td>
            <td class="{{$match['goalCss']}}">{{$match['goalResultCn']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>