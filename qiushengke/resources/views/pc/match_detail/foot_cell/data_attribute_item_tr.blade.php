<tr>
    <td>{{$team}}</td>
    <td>{{$attribute['all']['avgGoal']}}</td>
    <td>{{$attribute['all']['avgMiss']}}</td>
    <td>{{$attribute['all']['avgGoalMatch']}}</td>
    @if(isset($attribute['all']) && isset($attribute['all']['win']) && isset($attribute['all']['draw']) && isset($attribute['all']['lose']) && ($attribute['all']['draw']+$attribute['all']['win']+$attribute['all']['lose']) > 0)
        <?php
        $win = $attribute['all']['win'];
        $lose = $attribute['all']['lose'];
        $draw = $attribute['all']['draw'];
        $total = $win + $draw + $lose;
        ?>
        <td>{{round(100*$win/$total,0)}}%</td>
        <td>{{round(100*$draw/$total,0)}}%</td>
        <td>{{round(100*$lose/$total,0)}}%</td>
    @else
        <td>-</td>
        <td>-</td>
        <td>-</td>
    @endif
    <td>{{$attribute['host']['avgGoal']}}</td>
    <td>{{$attribute['host']['avgMiss']}}</td>
    <td>{{$attribute['host']['avgGoalMatch']}}</td>
    @if(isset($attribute['host']) && isset($attribute['host']['win']) && isset($attribute['host']['draw']) && isset($attribute['host']['lose']) && ($attribute['host']['draw']+$attribute['host']['win']+$attribute['host']['lose']) > 0)
        <?php
        $win = $attribute['host']['win'];
        $lose = $attribute['host']['lose'];
        $draw = $attribute['host']['draw'];
        $total = $win + $draw + $lose;
        ?>
        <td>{{round(100*$win/$total,0)}}%</td>
        <td>{{round(100*$draw/$total,0)}}%</td>
        <td>{{round(100*$lose/$total,0)}}%</td>
    @else
        <td>-</td>
        <td>-</td>
        <td>-</td>
    @endif
    <td>{{$attribute['league']['avgGoal']}}</td>
    <td>{{$attribute['league']['avgMiss']}}</td>
    <td>{{$attribute['league']['avgGoalMatch']}}</td>
    @if(isset($attribute['league']) && isset($attribute['league']['win']) && isset($attribute['league']['draw']) && isset($attribute['league']['lose']) && ($attribute['league']['draw']+$attribute['league']['win']+$attribute['league']['lose']) > 0)
        <?php
        $win = $attribute['league']['win'];
        $lose = $attribute['league']['lose'];
        $draw = $attribute['league']['draw'];
        $total = $win + $draw + $lose;
        ?>
        <td>{{round(100*$win/$total,0)}}%</td>
        <td>{{round(100*$draw/$total,0)}}%</td>
        <td>{{round(100*$lose/$total,0)}}%</td>
    @else
        <td>-</td>
        <td>-</td>
        <td>-</td>
    @endif
</tr>