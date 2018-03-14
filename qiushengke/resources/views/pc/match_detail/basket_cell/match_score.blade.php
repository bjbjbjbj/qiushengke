<?php $isShowScore = $match['status'] > 0 || $match['status'] == -1; ?>
<div class="score">
    <p class="title">比分统计</p>
    <table>
        <thead>
        <tr>
            <th></th>
            <th>第一节</th>
            <th>第二节</th>
            <th>第三节</th>
            <th>第四节</th>
            @if(isset($match['h_ot']))
                @foreach($match['h_ot'] as $key=>$ot)
                    <th>加时{{$key}}</th>
                @endforeach
            @endif
            <th>总分</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{{$match['hname']}}</td>
            @if($isShowScore)
                <td>{{isset($match['hscore_1st']) ? $match['hscore_1st'] : "-"}}</td>
                <td>{{isset($match['hscore_2nd']) ? $match['hscore_2nd'] : "-"}}</td>
                <td>{{isset($match['hscore_3rd']) ? $match['hscore_3rd'] : "-"}}</td>
                <td>{{isset($match['hscore_4th']) ? $match['hscore_4th'] : "-"}}</td>
                @if(isset($match['h_ot']))
                    @foreach($match['h_ot'] as $key=>$ot)
                        <th>{{$ot}}</th>
                    @endforeach
                @endif
                <td>{{isset($match['hscore']) ? $match['hscore'] : "-"}}</td>
            @else
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            @endif
        </tr>
        <tr>
            <td>{{$match['aname']}}</td>
            @if($isShowScore)
                <td>{{isset($match['ascore_1st']) ? $match['ascore_1st'] : "-"}}</td>
                <td>{{isset($match['ascore_2nd']) ? $match['ascore_2nd'] : "-"}}</td>
                <td>{{isset($match['ascore_3rd']) ? $match['ascore_3rd'] : "-"}}</td>
                <td>{{isset($match['ascore_4th']) ? $match['ascore_4th'] : "-"}}</td>
                @if(isset($match['a_ot']))
                    @foreach($match['a_ot'] as $key=>$ot)
                        <th>{{$ot}}</th>
                    @endforeach
                @endif
                <td>{{isset($match['ascore']) ? $match['ascore'] : "-"}}</td>
            @else
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            @endif
        </tr>
        </tbody>
    </table>
</div>