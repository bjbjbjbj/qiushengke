<div class="rank default">
    <div class="title">积分排名<button class="close"></button></div>
    <table>
        <thead>
        <tr>
            <th>排名</th>
            <th>球队</th>
            <th>赛</th>
            <th>胜/平/负</th>
            <th>进/失</th>
            <th>净</th>
            <th>积分</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="green">{{$host['rank']}}</td>
            <td>{{$match['hname']}}</td>
            <td>{{$host['count']}}</td>
            <td>{{$host['win']}}/{{$host['draw']}}/{{$host['lose']}}</td>
            <td>{{$host['goal']}}/{{$host['fumble']}}</td>
            <td>{{$host['goal'] - $host['fumble']}}</td>
            <td>{{$host['score']}}</td>
        </tr>
        <tr>
            <td class="green">{{$away['rank']}}</td>
            <td>{{$match['aname']}}</td>
            <td>{{$away['count']}}</td>
            <td>{{$away['win']}}/{{$away['draw']}}/{{$away['lose']}}</td>
            <td>{{$away['goal']}}/{{$away['fumble']}}</td>
            <td>{{$away['goal'] - $away['fumble']}}</td>
            <td>{{$away['score']}}</td>
        </tr>
        </tbody>
    </table>
</div>