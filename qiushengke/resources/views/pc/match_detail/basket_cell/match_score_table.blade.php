<table>
    <thead>
    <tr>
        <th></th>
        <th>第一节</th>
        <th>第二节</th>
        <th>第三节</th>
        <th>第四节</th>
        @if(isset($match['h_ot']))
            @if(count($match['h_ot']) == 1)
                <th>加时</th>
            @else
                @foreach($match['h_ot'] as $key=>$ot)
                    <th>加时{{$key+1}}</th>
                @endforeach
            @endif
        @endif
        <th>总分</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>{{$match['hname']}}</td>
        @if($isShowScore)
            <td class="data_h_1">{{isset($match['hscore_1st']) ? $match['hscore_1st'] : "-"}}</td>
            <td class="data_h_2">{{isset($match['hscore_2nd']) ? $match['hscore_2nd'] : "-"}}</td>
            <td class="data_h_3">{{isset($match['hscore_3rd']) ? $match['hscore_3rd'] : "-"}}</td>
            <td class="data_h_4">{{isset($match['hscore_4th']) ? $match['hscore_4th'] : "-"}}</td>
            @if(isset($match['h_ot']))
                @foreach($match['h_ot'] as $key=>$ot)
                    <th>{{$ot}}</th>
                @endforeach
            @endif
            <td class="data_h_s">{{isset($match['hscore']) ? $match['hscore'] : "-"}}</td>
        @else
            <td class="data_h_1">-</td>
            <td class="data_h_2">-</td>
            <td class="data_h_3">-</td>
            <td class="data_h_4">-</td>
            <td class="data_h_s">-</td>
        @endif
    </tr>
    <tr>
        <td>{{$match['aname']}}</td>
        @if($isShowScore)
            <td class="data_a_1">{{isset($match['ascore_1st']) ? $match['ascore_1st'] : "-"}}</td>
            <td class="data_a_2">{{isset($match['ascore_2nd']) ? $match['ascore_2nd'] : "-"}}</td>
            <td class="data_a_3">{{isset($match['ascore_3rd']) ? $match['ascore_3rd'] : "-"}}</td>
            <td class="data_a_4">{{isset($match['ascore_4th']) ? $match['ascore_4th'] : "-"}}</td>
            @if(isset($match['a_ot']))
                @foreach($match['a_ot'] as $key=>$ot)
                    <th>{{$ot}}</th>
                @endforeach
            @endif
            <td class="data_a_s">{{isset($match['ascore']) ? $match['ascore'] : "-"}}</td>
        @else
            <td class="data_a_1">-</td>
            <td class="data_a_2">-</td>
            <td class="data_a_3">-</td>
            <td class="data_a_4">-</td>
            <td class="data_a_s">-</td>
        @endif
    </tr>
    </tbody>
</table>