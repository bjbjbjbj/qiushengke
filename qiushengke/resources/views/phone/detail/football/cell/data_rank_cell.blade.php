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
            <td class="green">{{empty($host['rank']) ? '-' : $host['rank']}}</td>
            <td>{{$match['hname']}}</td>
            <td>{{empty($host['count']) ? '-' : $host['count']}}</td>
            <td>{{!empty($host['win']) ? $host['win'] : '-'}}/{{empty($host['draw']) ? '-' : $host['draw']}}/{{empty($host['lose']) ? '-' : $host['lose']}}</td>
            <td>{{empty($host['goal']) ? '-' : $host['goal']}}/{{empty($host['fumble']) ? '-' : $host['fumble']}}</td>
            <td>{{$host['goal'] - $host['fumble']}}</td>
            <td>{{empty($host['score']) ? '-' : $host['score']}}</td>
        </tr>
        <tr>
            <td class="green">{{empty($away['rank']) ? '-' : $away['rank']}}</td>
            <td>{{$match['aname']}}</td>
            <td>{{empty($away['count']) ? '-' : $away['count']}}</td>
            <td>{{empty($away['win']) ? '-' : $away['win']}}/{{empty($away['draw']) ? '-' : $away['draw']}}/{{empty($away['lose']) ? '-' : $away['lose']}}</td>
            <td>{{empty($away['goal']) ? '-' : $away['goal']}}/{{empty($away['fumble']) ? '-' : $away['fumble']}}</td>
            <td>{{$away['goal'] - $away['fumble']}}</td>
            <td>{{empty($away['score']) ? '-' : $away['score']}}</td>
        </tr>
        </tbody>
    </table>
</div>