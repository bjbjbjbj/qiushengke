@if(isset($analyse['cornerAnalyse']))
    <div class="data">
        <?php
        $cornerAnalyse = $analyse['cornerAnalyse'];
        ?>
        <p class="title">数据统计</p>
        <p class="tabBox"><button class="on" value="10">近20场</button><button value="5">近10场</button></p>
        <table num="10">
            <tr>
                <th>球队</th>
                <th>得球</th>
                <th>失球</th>
                <th>净胜</th>
                <th>总数</th>
                <th>大球率</th>
            </tr>
            <tr>
                @if(isset($cornerAnalyse['home']['20']))
                    <td>{{$match['hname']}}</td>
                    <?php
                    $item = $cornerAnalyse['home']['20'];
                    ?>
                    <td>{{round($item['get'],0)}}</td>
                    <td>{{round($item['lose'],0)}}</td>
                    <td>{{round($item['leave'],0)}}</td>
                    <td>{{$item['lose'] + $item['get']}}</td>
                    <td>{{round($item['big'],0)}}%</td>
                @else
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                @endif
            </tr>
            <tr>
                @if(isset($cornerAnalyse['away']['20']))
                    <td>{{$match['aname']}}</td>
                    <?php
                    $item = $cornerAnalyse['away']['20'];
                    ?>
                    <td>{{round($item['get'],0)}}</td>
                    <td>{{round($item['lose'],0)}}</td>
                    <td>{{round($item['leave'],0)}}</td>
                    <td>{{$item['lose'] + $item['get']}}</td>
                    <td>{{round($item['big'],0)}}%</td>
                @else
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                @endif
            </tr>
        </table>
        <table num="5" style="display: none;">
            <tr>
                <th>球队</th>
                <th>得球</th>
                <th>失球</th>
                <th>净胜</th>
                <th>总数</th>
                <th>大球率</th>
            </tr>
            <tr>
                <td>{{$match['hname']}}</td>
                <?php
                $item = $cornerAnalyse['home']['10'];
                ?>
                <td>{{round($item['get'],0)}}</td>
                <td>{{round($item['lose'],0)}}</td>
                <td>{{round($item['leave'],0)}}</td>
                <td>{{$item['lose'] + $item['get']}}</td>
                <td>{{round($item['big'],0)}}%</td>
            </tr>
            <tr>
                <td>{{$match['aname']}}</td>
                <?php
                $item = $cornerAnalyse['away']['10'];
                ?>
                <td>{{round($item['get'],0)}}</td>
                <td>{{round($item['lose'],0)}}</td>
                <td>{{round($item['leave'],0)}}</td>
                <td>{{$item['lose'] + $item['get']}}</td>
                <td>{{round($item['big'],0)}}%</td>
            </tr>
        </table>
    </div>
@endif