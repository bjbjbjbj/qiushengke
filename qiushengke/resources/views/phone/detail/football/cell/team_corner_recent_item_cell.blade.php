<div class="proportionBox" ha="{{$ha}}" le="{{$le}}">
    <div class="proportion">
        <p class="win" style="width: 80%;"><b>{{$result['goalbig']}}</b></p>
        <p class="draw" style="width: 10%;"><b>{{$result['goaldraw']}}</b></p>
        <p class="lose" style="width: 10%;"><b>{{$result['goalsmall']}}</b></p>
    </div>
    <p class="summary">共{{$result['goaltotal']}}场，大球率：<b>{{$result['goalbig_percent']}}%</b></p>
</div>
<table ha="{{$ha}}" le="{{$le}}">
    <thead>
    <tr>
        <th>日期</th>
        <th>赛事</th>
        <th colspan="3">角球比分</th>
        <th>盘口</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $match)
        <tr>
            <td>{{substr($match['time'], 0, 10)}}</td>
            <td>{{$match['league']}}</td>
            <td @if($tid == $match['hid']) class="host" @endif >{{$match['hname']}}</td>
            <td>{{$match['h_corner']}}-{{$match['a_corner']}}<p class="goal">{{$match['h_half_corner']}}-{{$match['a_half_corner']}}</p></td>
            <td @if($tid == $match['aid']) class="host" @endif >{{$match['aname']}}</td>
            <td>{{$match['middle2']}}
                @if(isset($match['middle2']))
                    @if($match['h_corner'] + $match['a_corner'] > $match['middle2'])
                        <p class="big">大</p>
                    @elseif($match['h_corner'] + $match['a_corner'] < $match['middle2'])
                        <p class="small">小</p>
                    @else
                        <p class="draw">走</p>
                    @endif
                @else
                    <p>-</p>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>