<?php
$isEast = isset($zone) && $zone == 1;
?>
<table @if($isEast)class="west" @endif>
    <colgroup>
        <col num="1" width="40px">
        <col num="2">
        <col num="3" width="5.2%">
        <col num="4" width="5.2%">
        <col num="5" width="5.2%">
        <col num="6" width="8.3%">
        <col num="7" width="8.3%">
        <col num="8" width="8.3%">
        <col num="9" width="7%">
        <col num="10" width="7%">
        <col num="11" width="7%">
        <col num="12" width="90px">
    </colgroup>
    <thead>
    <tr>
        <th>排名</th>
        <th>球队</th>
        <th>胜</th>
        <th>负</th>
        <th>胜差</th>
        <th>胜率</th>
        <th>主场</th>
        <th>客场</th>
        <th>得分</th>
        <th>失分</th>
        <th>近十场</th>
        <th>连胜/负</th>
    </tr>
    </thead>
    <tbody>
    @if(isset($scores) && count($scores) > 0)
        @foreach($scores as $key=>$score)
            <tr>
                <td><p>{{$key + 1}}</p></td>
                <td><a href="" class="team">{{$score['tname']}}</a></td>
                <td>{{$score['win']}}</td>
                <td>{{$score['lose']}}</td>
                <td>{{$score['win_diff']}}</td>
                <td>{{$score['count'] > 0 ? number_format($score['win']*100/$score['count'], 1) : 0}}%</td>
                <td>{{$score['home_bat_w']}}-{{$score['home_bat_l']}}</td>
                <td>{{$score['away_bat_w']}}-{{$score['away_bat_l']}}</td>
                <td>{{$score['goal']}}</td>
                <td>{{$score['fumble']}}</td>
                <td>{{$score['ten_bat_w']}}-{{$score['ten_bat_l']}}</td>
                @if($score['win_status'] > 0)
                    <td>{{abs($score['win_status'])}}连胜</td>
                @else
                    <td>{{abs($score['win_status'])}}连败</td>
                @endif
            </tr>
        @endforeach
    @endif
    </tbody>
</table>