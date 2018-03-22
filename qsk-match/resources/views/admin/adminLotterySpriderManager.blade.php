@extends('layouts.liaoAdmin')

@section('content')
    <div class="table-responsive">
        <div class="panel-heading">

        </div>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>名称</th>
                <th>详情</th>
                <th>操作</th>
            </tr>
            </thead>
            <tr>
                <td>
                    重新爬某期任九（四场、六场）足彩
                </td>
                <td>
                    期数：足彩期数；类型：0-任九，1-四场，2-六场；期次：根据期数爬该期与该期之前的足彩 总共n期
                </td>
                <td>
                    <form role="form" method="get" action='/api/spider/fillLotteryByIssue' target="_blank">
                    <select role="form" name="type">
                        <option value='0'>任九</option>
                        <option value='1'
                                @if(@$_GET['type'] && 1 == @$_GET['type'])
                                selected
                                @endif
                        >四场
                        </option>
                        <option value='2'
                                @if(@$_GET['type'] && 2 == @$_GET['type'])
                                selected
                                @endif
                        >六场
                        </option>
                    </select>
                    <label>期数</label>
                    <input name="issue" type="number" style="width: 80px">
                    <label>期次</label>
                    <input name="count" type="number" style="width: 40px">
                    <button type="submit">执行</button>
                    </form>
                </td>
            </tr>
            <tr>
                <td>
                    更新无结果竞彩
                </td>
                <td>
                    一等奖多少人中,没更新的时候用
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderBounseNull'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    更新足彩结果
                </td>
                <td>
                    爬历史数据框架,主要用于获取足彩期数对应id,结果等需要用这个id爬
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderLotteryInit'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    填充足彩比赛
                </td>
                <td>
                    填充比赛,足彩数据爬回来不包括比赛,需要这个另外爬数据(lotterydetail表)
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderLotteryFillMatch'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    爬最新3场足彩
                </td>
                <td>
                    -
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderCurrentLottery'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    爬足彩历史记录
                </td>
                <td>
                    不含具体比赛,只是结果,多少人中等
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderFillLottery'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    更新最新比赛
                </td>
                <td>
                    -
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderMatchLiveChange'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    爬任九赔率
                </td>
                <td>
                    任九用bet365的赔率,假设定时任务没爬到,可以用这个
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderLotteryOdd'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    爬足彩赔率
                </td>
                <td>
                    -
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderSportBettingFrame'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    爬足彩赔率(填充最新一期)
                </td>
                <td>
                    -
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderSportBettingLast'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    爬篮彩赔率(框架)
                </td>
                <td>
                    -
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderBasketSportBettings'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    爬篮彩赔率(填充最新一期)
                </td>
                <td>
                    -
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderBasketCurrentBetting'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    填充足彩六场历史框架(不含任何数据
                </td>
                <td>
                    爬历史数据框架,主要用于获取足彩期数对应id,结果等需要用这个id爬
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderLotterySixHistoryFrame'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    填充足彩六场奖金
                </td>
                <td>
                    不含具体比赛,只是结果,多少人中等
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderLotterySixHistory'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    填充足彩六场比赛
                </td>
                <td>
                    填充比赛,足彩数据爬回来不包括比赛,需要这个另外爬数据(lotterydetail表)
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/lotterySixFillMatch'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    填充足彩四场历史框架(不含任何数据
                </td>
                <td>
                    爬历史数据框架,主要用于获取足彩期数对应id,结果等需要用这个id爬
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderLotteryFourHistoryFrame'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    填充足彩四场奖金
                </td>
                <td>
                    不含具体比赛,只是结果,多少人中等
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderLotteryFourHistory'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    填充足彩四场比赛
                </td>
                <td>
                    填充比赛,足彩数据爬回来不包括比赛,需要这个另外爬数据(lotterydetail表)
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/lotteryFourFillMatch'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    爬北单数据,框架
                </td>
                <td>
                    爬的时候会顺路爬最新一条
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderSportBettingBDHistoryFrame'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    填充北单数据
                </td>
                <td>
                    -
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderSportBettingBD'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    爬某期北单数据
                </td>
                <td>
                    -
                </td>
                <td>
                    <input id="issue_num_bd" type="text">
                    <button type="button" onclick="spiderBD()">执行</button>
                </td>
            </tr>
            <tr>
                <td>
                    爬北单胜负过关数据,框架
                </td>
                <td>
                    爬的时候会顺路爬最新一条
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderSportBettingBDWinHistoryFrame'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    填充北单胜负过关数据
                </td>
                <td>
                    -
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderSportBettingBDWin'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    爬某期北单胜负过关数据
                </td>
                <td>
                    -
                </td>
                <td>
                    <input id="issue_num_bd_win" type="text">
                    <button type="button" onclick="spiderBDWin()">执行</button>
                </td>
            </tr>
        </table>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        function lineup() {
            window.open('../../api/spider/spiderLeagueLineup?id=' + document.getElementById('lineupId').value, '_blank');
        }

        function spiderBD() {
            window.open('../../api/spider/spiderFillBDByIssueNum?issue_num=' + document.getElementById('issue_num_bd').value, '_blank');
        }

        function spiderBDWin() {
            window.open('../../api/spider/spiderFillBDWinByIssueNum?issue_num=' + document.getElementById('issue_num_bd_win').value, '_blank');
        }
    </script>

@endsection