@if((isset($future['home']) && count($future['home']) > 0) || (isset($future['away']) && count($future['away']) > 0) )
<div class="future matchTable default">
    <div class="title">
        未来赛程<button class="close"></button>
    </div>
    @if(isset($future['home']) && count($future['home']) > 0)
    <p class="teamName"><span>{{$match['hname']}}</span></p>
    <table>
        <thead>
        <tr>
            <th>日期</th>
            <th>赛事</th>
            <th colspan="3">主客球队</th>
            <th>相隔</th>
        </tr>
        </thead>
        <tbody>
        @foreach($future['home'] as $m)
            <tr>
                <td class="red">{{$m['time']}}</td>
                <td>{{$m['league']}}</td>
                <td @if($m['hid'] == $match['hid']) class="green" @endif >{{$m['hname']}}</td>
                <td>VS</td>
                <td @if($m['aid'] == $match['hid']) class="green" @endif >{{$m['aname']}}</td>
                <td>{{$m['day']}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif
    @if(isset($future['away']) && count($future['away']) > 0)
    <p class="teamName"><span>{{$match['aname']}}</span></p>
    <table>
        <thead>
        <tr>
            <th>日期</th>
            <th>赛事</th>
            <th colspan="3">主客球队</th>
            <th>相隔</th>
        </tr>
        </thead>
        <tbody>
        @foreach($future['away'] as $m)
            <tr>
                <td class="red">{{$m['time']}}</td>
                <td>{{$m['league']}}</td>
                <td @if($m['hid'] == $match['aid']) class="green" @endif >{{$m['hname']}}</td>
                <td>VS</td>
                <td @if($m['aid'] == $match['aid']) class="green" @endif >{{$m['aname']}}</td>
                <td>{{$m['day']}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>
@endif