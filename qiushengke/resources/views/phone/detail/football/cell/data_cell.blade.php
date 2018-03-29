<div id="Data" class="content" style="display: none;">
    <div class="odd default">
        <div class="title">
            <select>
                <option value="Data_Odd_SB">SB</option>
                <option value="Data_Odd_Bet365">Bet365</option>
                <option value="Data_Odd_Aocai">澳彩</option>
            </select>
            <button class="close"></button>
        </div>
        <table id="Data_Odd_SB" style="display: ">
            <thead>
            <tr>
                <th></th>
                <th>初赔</th>
                <th>即赔</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>亚盘</td>
                <td><p>1.08</p><p>0</p><p>0.80</p></td>
                <td><p class="green">1.08</p><p>0</p><p>0.80</p></td>
            </tr>
            <tr>
                <td>欧赔</td>
                <td><p>1.08</p><p>4.20</p><p>0.80</p></td>
                <td><p>1.08</p><p>4.20</p><p class="blue">0.80</p></td>
            </tr>
            <tr>
                <td>大小球</td>
                <td><p>1.08</p><p>3</p><p>0.80</p></td>
                <td><p>1.08</p><p>3</p><p>0.80</p></td>
            </tr>
            </tbody>
        </table>
        <table id="Data_Odd_Bet365" style="display: none;">
            <thead>
            <tr>
                <th></th>
                <th>初赔</th>
                <th>即赔</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>亚盘</td>
                <td><p>1.08</p><p>0</p><p>0.80</p></td>
                <td><p class="green">1.08</p><p>0</p><p>0.80</p></td>
            </tr>
            <tr>
                <td>欧赔</td>
                <td><p>1.08</p><p>4.20</p><p>0.80</p></td>
                <td><p>1.08</p><p>4.20</p><p class="red">0.80</p></td>
            </tr>
            <tr>
                <td>大小球</td>
                <td><p>1.08</p><p>3</p><p>0.80</p></td>
                <td><p>1.08</p><p>3</p><p>0.80</p></td>
            </tr>
            </tbody>
        </table>
        <table id="Data_Odd_Aocai" style="display: none;">
            <thead>
            <tr>
                <th></th>
                <th>初赔</th>
                <th>即赔</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>亚盘</td>
                <td><p>1.08</p><p>0</p><p>0.80</p></td>
                <td><p class="green">1.08</p><p>0</p><p>0.80</p></td>
            </tr>
            <tr>
                <td>欧赔</td>
                <td><p>1.08</p><p>4.20</p><p>0.80</p></td>
                <td><p>1.08</p><p>4.20</p><p class="red">0.80</p></td>
            </tr>
            <tr>
                <td>大小球</td>
                <td><p>1.08</p><p>3</p><p>0.80</p></td>
                <td><p>1.08</p><p>3</p><p>0.80</p></td>
            </tr>
            </tbody>
        </table>
    </div>
    @if(isset($analyse['rank']))
    @component("phone.detail.football.cell.data_rank_cell", [
        'match'=>$match, 'host'=>$analyse['rank']['rank']['host']['all'], 'away'=>$analyse['rank']['rank']['away']['all']
    ]) @endcomponent
    @endif
    <div class="battle matchTable default" ha="0" le="0">
        <div class="title">
            交锋往绩<button class="close"></button>
            <div class="labelbox">
                <label for="Battle_HA"><input type="checkbox" name="battle" value="ha" id="Battle_HA"><span></span>同主客</label>
                <label for="Battle_LE"><input type="checkbox" name="battle" value="le" id="Battle_LE"><span></span>同赛事</label>
            </div>
        </div>
        <div class="canvasBox" ha="0" le="0">
            <div class="canvasArea">
                <div class="circle"><canvas width="140px" height="140px" value="0.4" color="#34b45d"></canvas></div>
                <p>主胜<b class="red">4</b></p>
            </div>
            <div class="canvasArea">
                <div class="circle"><canvas width="140px" height="140px" value="0.2" color="#9e9e9e"></canvas></div>
                <p>平局<b class="green">2</b></p>
            </div>
            <div class="canvasArea">
                <div class="circle"><canvas width="140px" height="140px" value="0.4" color="#1974bd"></canvas></div>
                <p>主负<b class="gray">4</b></p>
            </div>
            <p class="summary">共10场，胜率：<b>16.7%</b>，赢盘率：<b>60%</b></p>
        </div>
        <div class="canvasBox" ha="1" le="0">
            <div class="canvasArea">
                <div class="circle"><canvas width="140px" height="140px" value="0.4" color="#34b45d"></canvas></div>
                <p>主胜<b class="red">4</b></p>
            </div>
            <div class="canvasArea">
                <div class="circle"><canvas width="140px" height="140px" value="0.2" color="#9e9e9e"></canvas></div>
                <p>平局<b class="green">2</b></p>
            </div>
            <div class="canvasArea">
                <div class="circle"><canvas width="140px" height="140px" value="0.4" color="#1974bd"></canvas></div>
                <p>主负<b class="gray">4</b></p>
            </div>
            <p class="summary">共10场，胜率：<b>16.7%</b>，赢盘率：<b>60%</b></p>
        </div>
        <div class="canvasBox" ha="0" le="1">
            <div class="canvasArea">
                <div class="circle"><canvas width="140px" height="140px" value="0.4" color="#34b45d"></canvas></div>
                <p>主胜<b class="red">4</b></p>
            </div>
            <div class="canvasArea">
                <div class="circle"><canvas width="140px" height="140px" value="0.2" color="#9e9e9e"></canvas></div>
                <p>平局<b class="green">2</b></p>
            </div>
            <div class="canvasArea">
                <div class="circle"><canvas width="140px" height="140px" value="0.4" color="#1974bd"></canvas></div>
                <p>主负<b class="gray">4</b></p>
            </div>
            <p class="summary">共10场，胜率：<b>16.7%</b>，赢盘率：<b>60%</b></p>
        </div>
        <div class="canvasBox" ha="1" le="1">
            <div class="canvasArea">
                <div class="circle"><canvas width="140px" height="140px" value="0.4" color="#34b45d"></canvas></div>
                <p>主胜<b class="red">4</b></p>
            </div>
            <div class="canvasArea">
                <div class="circle"><canvas width="140px" height="140px" value="0.2" color="#9e9e9e"></canvas></div>
                <p>平局<b class="green">2</b></p>
            </div>
            <div class="canvasArea">
                <div class="circle"><canvas width="140px" height="140px" value="0.4" color="#1974bd"></canvas></div>
                <p>主负<b class="gray">4</b></p>
            </div>
            <p class="summary">共10场，胜率：<b>16.7%</b>，赢盘率：<b>60%</b></p>
        </div>
        <table ha="0" le="0">
            <thead>
            <tr>
                <th>日期</th>
                <th>赛事</th>
                <th colspan="3">比分</th>
                <th>主让</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td>尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>0.5/1<p class="win">赢</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td>尤文图斯</td>
                <td>0.5/1<p class="draw">走</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td>尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>0.5/1<p class="lose">输</p></td>
            </tr>
            </tbody>
        </table>
        <table ha="1" le="0">
            <thead>
            <tr>
                <th>日期</th>
                <th>赛事</th>
                <th colspan="3">比分</th>
                <th>主让</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td>尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>0.5/1<p class="win">赢</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td>尤文图斯</td>
                <td>0.5/1<p class="draw">走</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td>尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>0.5/1<p class="lose">输</p></td>
            </tr>
            </tbody>
        </table>
        <table ha="0" le="1">
            <thead>
            <tr>
                <th>日期</th>
                <th>赛事</th>
                <th colspan="3">比分</th>
                <th>主让</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td>尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>0.5/1<p class="win">赢</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td>尤文图斯</td>
                <td>0.5/1<p class="draw">走</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td>尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>0.5/1<p class="lose">输</p></td>
            </tr>
            </tbody>
        </table>
        <table ha="1" le="1">
            <thead>
            <tr>
                <th>日期</th>
                <th>赛事</th>
                <th colspan="3">比分</th>
                <th>主让</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td>尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>0.5/1<p class="win">赢</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td>尤文图斯</td>
                <td>0.5/1<p class="draw">走</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td>尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>0.5/1<p class="lose">输</p></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="history matchTable default" ha="0" le="0">
        <div class="title">
            近期战绩<button class="close"></button>
            <div class="labelbox">
                <label for="History_HA"><input type="checkbox" name="history" value="ha" id="History_HA"><span></span>同主客</label>
                <label for="History_LE"><input type="checkbox" name="history" value="le" id="History_LE"><span></span>同赛事</label>
            </div>
        </div>
        <p class="teamName"><span>索菲亚斯拉维亚</span></p>
        <div class="proportionBox" ha="0" le="0">
            <div class="proportion">
                <p class="win" style="width: 80%;"><b>8</b></p>
                <p class="draw" style="width: 10%;"><b>1</b></p>
                <p class="lose" style="width: 10%;"><b>1</b></p>
            </div>
            <p class="summary">共10场，胜率：<b>16.7%</b>，赢盘率：<b>60%</b></p>
        </div>
        <div class="proportionBox" ha="1" le="0">
            <div class="proportion">
                <p class="win" style="width: 80%;"><b>8</b></p>
                <p class="draw" style="width: 10%;"><b>1</b></p>
                <p class="lose" style="width: 10%;"><b>1</b></p>
            </div>
            <p class="summary">共10场，胜率：<b>16.7%</b>，赢盘率：<b>60%</b></p>
        </div>
        <div class="proportionBox" ha="0" le="1">
            <div class="proportion">
                <p class="win" style="width: 80%;"><b>8</b></p>
                <p class="draw" style="width: 10%;"><b>1</b></p>
                <p class="lose" style="width: 10%;"><b>1</b></p>
            </div>
            <p class="summary">共10场，胜率：<b>16.7%</b>，赢盘率：<b>60%</b></p>
        </div>
        <div class="proportionBox" ha="1" le="1">
            <div class="proportion">
                <p class="win" style="width: 80%;"><b>8</b></p>
                <p class="draw" style="width: 10%;"><b>1</b></p>
                <p class="lose" style="width: 10%;"><b>1</b></p>
            </div>
            <p class="summary">共10场，胜率：<b>16.7%</b>，赢盘率：<b>60%</b></p>
        </div>
        <table ha="0" le="0">
            <thead>
            <tr>
                <th>日期</th>
                <th>赛事</th>
                <th colspan="3">比分</th>
                <th>主让</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td>尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>0.5/1<p class="win">赢</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td>尤文图斯</td>
                <td>0.5/1<p class="draw">走</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td>尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>0.5/1<p class="lose">输</p></td>
            </tr>
            </tbody>
        </table>
        <table ha="1" le="0">
            <thead>
            <tr>
                <th>日期</th>
                <th>赛事</th>
                <th colspan="3">比分</th>
                <th>主让</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td>尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>0.5/1<p class="win">赢</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td>尤文图斯</td>
                <td>0.5/1<p class="draw">走</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td>尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>0.5/1<p class="lose">输</p></td>
            </tr>
            </tbody>
        </table>
        <table ha="0" le="1">
            <thead>
            <tr>
                <th>日期</th>
                <th>赛事</th>
                <th colspan="3">比分</th>
                <th>主让</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td>尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>0.5/1<p class="win">赢</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td>尤文图斯</td>
                <td>0.5/1<p class="draw">走</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td>尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>0.5/1<p class="lose">输</p></td>
            </tr>
            </tbody>
        </table>
        <table ha="1" le="1">
            <thead>
            <tr>
                <th>日期</th>
                <th>赛事</th>
                <th colspan="3">比分</th>
                <th>主让</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td>尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>0.5/1<p class="win">赢</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td>尤文图斯</td>
                <td>0.5/1<p class="draw">走</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td>尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td class="host">索菲亚斯拉维亚</td>
                <td>0.5/1<p class="lose">输</p></td>
            </tr>
            </tbody>
        </table>
        <p class="teamName"><span>尤文图斯</span></p>
        <div class="proportionBox" ha="0" le="0">
            <div class="proportion">
                <p class="win" style="width: 50%;"><b>5</b></p>
                <p class="draw" style="width: 20%;"><b>2</b></p>
                <p class="lose" style="width: 30%;"><b>3</b></p>
            </div>
            <p class="summary">共10场，胜率：<b>16.7%</b>，赢盘率：<b>60%</b></p>
        </div>
        <div class="proportionBox" ha="1" le="0">
            <div class="proportion">
                <p class="win" style="width: 50%;"><b>5</b></p>
                <p class="draw" style="width: 20%;"><b>2</b></p>
                <p class="lose" style="width: 30%;"><b>3</b></p>
            </div>
            <p class="summary">共10场，胜率：<b>16.7%</b>，赢盘率：<b>60%</b></p>
        </div>
        <div class="proportionBox" ha="0" le="1">
            <div class="proportion">
                <p class="win" style="width: 50%;"><b>5</b></p>
                <p class="draw" style="width: 20%;"><b>2</b></p>
                <p class="lose" style="width: 30%;"><b>3</b></p>
            </div>
            <p class="summary">共10场，胜率：<b>16.7%</b>，赢盘率：<b>60%</b></p>
        </div>
        <div class="proportionBox" ha="1" le="1">
            <div class="proportion">
                <p class="win" style="width: 50%;"><b>5</b></p>
                <p class="draw" style="width: 20%;"><b>2</b></p>
                <p class="lose" style="width: 30%;"><b>3</b></p>
            </div>
            <p class="summary">共10场，胜率：<b>16.7%</b>，赢盘率：<b>60%</b></p>
        </div>
        <table ha="0" le="0">
            <thead>
            <tr>
                <th>日期</th>
                <th>赛事</th>
                <th colspan="3">比分</th>
                <th>主让</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td class="host">尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td>索菲亚斯拉维亚</td>
                <td>0.5/1<p class="win">赢</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td>索菲亚斯拉维亚</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td class="host">尤文图斯</td>
                <td>0.5/1<p class="draw">走</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td class="host">尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td>索菲亚斯拉维亚</td>
                <td>0.5/1<p class="lose">输</p></td>
            </tr>
            </tbody>
        </table>
        <table ha="1" le="0">
            <thead>
            <tr>
                <th>日期</th>
                <th>赛事</th>
                <th colspan="3">比分</th>
                <th>主让</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td class="host">尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td>索菲亚斯拉维亚</td>
                <td>0.5/1<p class="win">赢</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td>索菲亚斯拉维亚</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td class="host">尤文图斯</td>
                <td>0.5/1<p class="draw">走</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td class="host">尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td>索菲亚斯拉维亚</td>
                <td>0.5/1<p class="lose">输</p></td>
            </tr>
            </tbody>
        </table>
        <table ha="0" le="1">
            <thead>
            <tr>
                <th>日期</th>
                <th>赛事</th>
                <th colspan="3">比分</th>
                <th>主让</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td class="host">尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td>索菲亚斯拉维亚</td>
                <td>0.5/1<p class="win">赢</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td>索菲亚斯拉维亚</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td class="host">尤文图斯</td>
                <td>0.5/1<p class="draw">走</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td class="host">尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td>索菲亚斯拉维亚</td>
                <td>0.5/1<p class="lose">输</p></td>
            </tr>
            </tbody>
        </table>
        <table ha="1" le="1">
            <thead>
            <tr>
                <th>日期</th>
                <th>赛事</th>
                <th colspan="3">比分</th>
                <th>主让</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td class="host">尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td>索菲亚斯拉维亚</td>
                <td>0.5/1<p class="win">赢</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td>索菲亚斯拉维亚</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td class="host">尤文图斯</td>
                <td>0.5/1<p class="draw">走</p></td>
            </tr>
            <tr>
                <td>2017-12-24</td>
                <td>保超</td>
                <td class="host">尤文图斯</td>
                <td>100 - 100<p class="goal">小2/2.5</p></td>
                <td>索菲亚斯拉维亚</td>
                <td>0.5/1<p class="lose">输</p></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="track default">
        <div class="title">
            赛事盘路<button class="close"></button>
        </div>
        <p class="teamName"><span>索菲亚斯拉维亚</span></p>
        <table>
            <thead>
            <tr>
                <th>全场</th>
                <th>赢/走/输</th>
                <th>赢盘率</th>
                <th>大球</th>
                <th>大球率</th>
                <th>小球</th>
                <th>小球率</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>总</td>
                <td>15/1/14</td>
                <td>50.00%</td>
                <td>17</td>
                <td>50.00%</td>
                <td>17</td>
                <td>50.00%</td>
            </tr>
            <tr>
                <td>主</td>
                <td>6/0/9</td>
                <td>40.00%</td>
                <td>11</td>
                <td>40.00%</td>
                <td>11</td>
                <td>40.00%</td>
            </tr>
            <tr>
                <td>客</td>
                <td>9/1/5</td>
                <td>60.00%</td>
                <td>6</td>
                <td>60.00%</td>
                <td>6</td>
                <td>60.00%</td>
            </tr>
            <tr>
                <td>近6</td>
                <td colspan="3"><p class="lose asia">输</p><p class="win asia">赢</p><p class="win asia">赢</p><p class="win asia">赢</p><p class="draw asia">走</p><p class="win asia">赢</p></td>
                <td colspan="3"><p class="small goal">小</p><p class="big goal">大</p><p class="draw goal">走</p><p class="big goal">大</p><p class="big goal">大</p><p class="small goal">小</p></td>
            </tr>
            </tbody>
        </table>
        <p class="teamName"><span>尤文图斯</span></p>
        <table>
            <thead>
            <tr>
                <th>全场</th>
                <th>赢/走/输</th>
                <th>赢盘率</th>
                <th>大球</th>
                <th>大球率</th>
                <th>小球</th>
                <th>小球率</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>总</td>
                <td>15/1/14</td>
                <td>50.00%</td>
                <td>17</td>
                <td>50.00%</td>
                <td>17</td>
                <td>50.00%</td>
            </tr>
            <tr>
                <td>主</td>
                <td>6/0/9</td>
                <td>40.00%</td>
                <td>11</td>
                <td>40.00%</td>
                <td>11</td>
                <td>40.00%</td>
            </tr>
            <tr>
                <td>客</td>
                <td>9/1/5</td>
                <td>60.00%</td>
                <td>6</td>
                <td>60.00%</td>
                <td>6</td>
                <td>60.00%</td>
            </tr>
            <tr>
                <td>近6</td>
                <td colspan="3"><p class="lose asia">输</p><p class="win asia">赢</p><p class="win asia">赢</p><p class="win asia">赢</p><p class="draw asia">走</p><p class="win asia">赢</p></td>
                <td colspan="3"><p class="small goal">小</p><p class="big goal">大</p><p class="draw goal">走</p><p class="big goal">大</p><p class="big goal">大</p><p class="small goal">小</p></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="future matchTable default">
        <div class="title">
            未来赛程<button class="close"></button>
        </div>
        <p class="teamName"><span>索菲亚斯拉维亚</span></p>
        <table>
            <thead>
            <tr>
                <th>日期</th>
                <th>赛事</th>
                <th colspan="3">主客球队</th>
                <th>相隔</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="red">2017-12-24</td>
                <td>保超</td>
                <td>尤文图斯</td>
                <td>VS</td>
                <td class="green">索菲亚斯拉维亚</td>
                <td>3天</td>
            </tr>
            <tr>
                <td class="red">2017-12-24</td>
                <td>保超</td>
                <td class="green">索菲亚斯拉维亚</td>
                <td>VS</td>
                <td>尤文图斯</td>
                <td>7天</td>
            </tr>
            <tr>
                <td class="red">2017-12-24</td>
                <td>保超</td>
                <td>尤文图斯</td>
                <td>VS</td>
                <td class="green">索菲亚斯拉维亚</td>
                <td>14天</td>
            </tr>
            </tbody>
        </table>
        <p class="teamName"><span>尤文图斯</span></p>
        <table>
            <thead>
            <tr>
                <th>日期</th>
                <th>赛事</th>
                <th colspan="3">比分</th>
                <th>主让</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="red">2017-12-24</td>
                <td>保超</td>
                <td class="green">尤文图斯</td>
                <td>VS</td>
                <td>索菲亚斯拉维亚</td>
                <td>3天</td>
            </tr>
            <tr>
                <td class="red">2017-12-24</td>
                <td>保超</td>
                <td>索菲亚斯拉维亚</td>
                <td>VS</td>
                <td class="green">尤文图斯</td>
                <td>7天</td>
            </tr>
            <tr>
                <td class="red">2017-12-24</td>
                <td>保超</td>
                <td class="green">尤文图斯</td>
                <td>VS</td>
                <td>索菲亚斯拉维亚</td>
                <td>14天</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>