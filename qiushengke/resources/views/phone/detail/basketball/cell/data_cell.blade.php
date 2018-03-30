@if(isset($odds) && count($odds) > 0)
    <div class="odd default">
        @component('phone.detail.football.cell.data_odd_cell', ['odds'=>$odds])
        @endcomponent
    </div>
@endif
@if(isset($rank) && count($rank) > 0)
    <div class="rank default">
        <div class="title">积分排名<button class="close"></button></div>
        <p class="teamName"><span>索菲亚斯拉维亚</span></p>
        <table>
            <thead>
            <tr>
                <th>全场</th>
                <th>赛</th>
                <th>胜/负</th>
                <th>得/失</th>
                <th>净</th>
                <th>排名</th>
                <th>胜率</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>总</td>
                <td>22</td>
                <td>11/11</td>
                <td>102.4/100.6</td>
                <td>1.8</td>
                <td>10</td>
                <td>50.0%</td>
            </tr>
            <tr>
                <td>主</td>
                <td>22</td>
                <td>11/11</td>
                <td>102.4/100.6</td>
                <td>1.8</td>
                <td>10</td>
                <td>50.0%</td>
            </tr>
            <tr>
                <td>客</td>
                <td>22</td>
                <td>11/11</td>
                <td>102.4/100.6</td>
                <td>1.8</td>
                <td>10</td>
                <td>50.0%</td>
            </tr>
            <tr>
                <td>近10</td>
                <td>22</td>
                <td>11/11</td>
                <td>102.4/100.6</td>
                <td>1.8</td>
                <td>10</td>
                <td>50.0%</td>
            </tr>
            </tbody>
        </table>
        <p class="teamName"><span>索菲亚斯拉维亚</span></p>
        <table>
            <thead>
            <tr>
                <th>全场</th>
                <th>赛</th>
                <th>胜/负</th>
                <th>得/失</th>
                <th>净</th>
                <th>排名</th>
                <th>胜率</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>总</td>
                <td>22</td>
                <td>11/11</td>
                <td>102.4/100.6</td>
                <td>1.8</td>
                <td>10</td>
                <td>50.0%</td>
            </tr>
            <tr>
                <td>主</td>
                <td>22</td>
                <td>11/11</td>
                <td>102.4/100.6</td>
                <td>1.8</td>
                <td>10</td>
                <td>50.0%</td>
            </tr>
            <tr>
                <td>客</td>
                <td>22</td>
                <td>11/11</td>
                <td>102.4/100.6</td>
                <td>1.8</td>
                <td>10</td>
                <td>50.0%</td>
            </tr>
            <tr>
                <td>近10</td>
                <td>22</td>
                <td>11/11</td>
                <td>102.4/100.6</td>
                <td>1.8</td>
                <td>10</td>
                <td>50.0%</td>
            </tr>
            </tbody>
        </table>
    </div>
@endif
@component('phone.detail.basketball.cell.analyse_battle_cell', ['cdn'=>$cdn,'base'=>$analyse, 'match'=>$match])
@endcomponent