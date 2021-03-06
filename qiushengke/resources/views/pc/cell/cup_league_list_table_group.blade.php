<div class="con" num="{{$num}}" id="Group" style="{{$show?'':'display: none;'}}">
    <div class="tabLine">
        <button class="on" value="All">总榜</button>
        @foreach($stage['groupMatch'] as $key=>$item)
            <button value="{{$key}}">{{$key}}组</button>
        @endforeach
    </div>
    <div class="item" type="All">
        <table class="group">
            <colgroup>
                <col num="1" width="100px">
                <col num="2" width="">
                <col num="3" width="7%">
                <col num="4" width="7%">
                <col num="5" width="7%">
                <col num="6" width="7%">
                <col num="7" width="7%">
                <col num="8" width="7%">
                <col num="9" width="7%">
                <col num="10" width="7%">
            </colgroup>
            <thead>
            <tr>
                <th>排名</th>
                <th>球队</th>
                <th>总</th>
                <th>胜</th>
                <th>平</th>
                <th>负</th>
                <th>得</th>
                <th>失</th>
                <th>净</th>
                <th>积分</th>
            </tr>
            </thead>
            @foreach($stage['groupMatch'] as $key=>$item)
                <tbody>
                <tr>
                    <th colspan="10">
                        <p>{{$key}}组</p>
                    </th>
                </tr>
                <?php
                $scores = $item['scores'];
                ?>
                @for($i = 0 ; $i < count($scores) ; $i++)
                    <?php
                    $score = $scores[$i];
                    ?>
                    <tr>
                        <td><span>{{$i+1}}</span></td>
                        <td><a href="team.html">{{$score['tname']}}</a></td>
                        <td>{{$score['count']}}</td>
                        <td>{{$score['win']}}</td>
                        <td>{{$score['draw']}}</td>
                        <td>{{$score['lose']}}</td>
                        <td>{{$score['goal']}}</td>
                        <td>{{$score['fumble']}}</td>
                        <td>{{$score['goal'] - $score['fumble']}}</td>
                        <td>{{$score['score']}}</td>
                    </tr>
                @endfor
                </tbody>
            @endforeach
        </table>
    </div>
    @foreach($stage['groupMatch'] as $key=>$item)
        @component('pc.cell.cup_league_list_table_group_item',['key'=>$key,'matches'=>$item['matches'],'scores'=>$item['scores']])
        @endcomponent
    @endforeach
</div>