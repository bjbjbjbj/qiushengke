<tr>
    <td><p>{{$score['rank']}}</p></td>
    <td><a href="" class="team">{{$score['tname']}}</a></td>
    <td>{{$score['count']}}</td>
    <td>{{$score['win']}}</td>
    <td>{{$score['draw']}}</td>
    <td>{{$score['lose']}}</td>
    <td>{{$score['goal']}}</td>
    <td>{{$score['fumble']}}</td>
    <td>{{$score['goal'] - $score['fumble']}}</td>
    @if($score['count'] > 0)
        <td>{{round($score['win']/$score['count'],3)*100}}%</td>
        <td>{{round($score['draw']/$score['count'],3)*100}}%</td>
        <td>{{round($score['lose']/$score['count'],3)*100}}%</td>
    @else
        <td>-</td>
        <td>-</td>
        <td>-</td>
    @endif
    <td>{{$score['score']}}</td>
    <td>
        <?php
            $six = '';
        for($i = 0 ; $i < 6 ; $i++)
            if(count($score['six']) > (6 - 1 - $i)){
                $tmp = $score['six'][6 - 1 - $i];
                $six = $six . "<span>" . ($tmp == 3 ? 'W' : ($tmp == 1 ? 'D' : 'L')). "</span>";
        }
            else
                $six = $six . "<span>-</span>";
        ?>
        {!! $six !!}
    </td>
</tr>