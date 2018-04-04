<?php
$rank = isset($analyse) ? $analyse['rank'] : null;
$rankKeyArray = ['home'=>'主','guest'=>'客','all'=>'总','ten'=>'近10场'];
?>
@if(isset($rank))
    <div class="attack">
        <p class="title">积分排名</p>
        <div class="part host">
            <p class="name">{{$match['hname']}}</p>
            <table>
                <thead>
                <tr>
                    <th></th>
                    <th>赛</th>
                    <th>胜</th>
                    <th>负</th>
                    <th>得</th>
                    <th>失</th>
                    <th>净</th>
                    <th>排名</th>
                    <th>胜率</th>
                </tr>
                </thead>
                <tbody>
                @if(isset($rank['host']))
                    @foreach($rank['host'] as $key=>$item)
                        <tr>
                            <td>{{$rankKeyArray[$key]}}</td>
                            <td>{{$item['count']}}</td>
                            <td>{{$item['win']}}</td>
                            <td>{{$item['lose']}}</td>
                            <td>{{$item['goal']}}</td>
                            <td>{{$item['fumble']}}</td>
                            <td>{{number_format($item['goal']-$item['fumble'],1)}}</td>
                            <td>{{$item['rank']}}</td>
                            <td>{{number_format($item['count']>0?$item['win']*100/$item['count']:0,2)}}%</td>
                        </tr>
                    @endforeach
                    </tbody>
                @endif
            </table>
        </div>
        <div class="part away">
            <p class="name">{{$match['aname']}}</p>
            <table>
                <thead>
                <tr>
                    <th></th>
                    <th>赛</th>
                    <th>胜</th>
                    <th>负</th>
                    <th>得</th>
                    <th>失</th>
                    <th>净</th>
                    <th>排名</th>
                    <th>胜率</th>
                </tr>
                </thead>
                <tbody>
                @if(isset($rank['away']))
                    @foreach($rank['away'] as $key=>$item)
                        <tr>
                            <td>{{$rankKeyArray[$key]}}</td>
                            <td>{{$item['count']}}</td>
                            <td>{{$item['win']}}</td>
                            <td>{{$item['lose']}}</td>
                            <td>{{$item['goal']}}</td>
                            <td>{{$item['fumble']}}</td>
                            <td>{{number_format($item['goal']-$item['fumble'],1)}}</td>
                            <td>{{$item['rank']}}</td>
                            <td>{{number_format($item['count']>0?$item['win']*100/$item['count']:0,2)}}%</td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
@endif