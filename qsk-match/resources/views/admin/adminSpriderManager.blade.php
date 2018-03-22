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
                    更新拐点
                </td>
                <td>
                    -
                </td>
                <td>
                    <select id="select_inflexion" role="form" name="inflexion">
                        <option value=0>更新</option>
                        <option value=1>重算</option>
                    </select>
                    <button type="button" onclick="inflexion()">执行</button>
                </td>
            </tr>
            <tr>
                <td>
                    刷新当天的赛事赛程与积分
                </td>
                <td>
                    -
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderCurrentLeague'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    刷新当前比赛详情信息
                </td>
                <td>
                    -
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderCurrentMatchDetail'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    刷新当前比赛阵容
                </td>
                <td>
                    -
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderCurrentMatchLineup'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    爬阵容
                </td>
                <td>
                    -
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/spider/spiderFillMatchLineup'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    爬阵容
                </td>
                <td>
                    根据赛事id爬当季所有比赛的阵容
                </td>
                <td>
                    <input id="lineupId" type="text">
                    <button type="button" onclick="lineup()">执行</button>
                    {{--<a class="" target="_blank"  href='../../api/spider/spiderLeagueLineup'>执行</a>--}}
                </td>
            </tr>
            <tr>
                <td>
                    刷新阵容主力 比例 进球数据
                </td>
                <td>
                    当你发现阵容数据都是写未出,但是应该是有阵容的时候,执行这个,强制拉一次数据并计算
                </td>
                <td>
                    主队:<input id="line_up_hname" type="text"></br>
                    客队:<input id="line_up_aname" type="text">
                    <button type="button" onclick="forceLineup()">执行</button>
                </td>
            </tr>
            <tr>
                <td>
                    盘王
                </td>
                <td>
                    计算主流联赛的盘王信息
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/calculateKing/fillTeamOddResultByMainLeague'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    盘王
                </td>
                <td>
                    计算所有联赛的盘王信息
                </td>
                <td>
                    <a class="" target="_blank" href='../../api/calculateKing/fillTeamOddResultByAllLeague'>执行</a>
                </td>
            </tr>
            <tr>
                <td>
                    盘王
                </td>
                <td>
                    计算某联赛（lid）最新赛事的所有球队盘王信息
                </td>
                <td>
                    <input id="king_lid0" type="text">
                    <button type="button" onclick="spiderKingAll()">执行</button>
                </td>
            </tr>
            <tr>
                <td>
                    盘王
                </td>
                <td>
                    计算某联赛（名字）最新赛事的所有球队盘王信息
                </td>
                <td>
                    <input id="king_lname0" type="text">
                    <button type="button" onclick="spiderKingByLeagueName()">执行</button>
                </td>
            </tr>
            <tr>
                <td>
                    盘王
                </td>
                <td>
                    计算某联赛（lid）最新赛事的某球队（tid）盘王信息
                </td>
                <td>
                    <input id="king_lid" type="text">
                    <input id="king_tid" type="text">
                    <button type="button" onclick="spiderKing()">执行</button>
                </td>
            </tr>
            <tr>
                <td>
                    重新爬赛事的比赛列表
                </td>
                <td>
                    球探错了,到时赛事可能有问题,可以通过这个去重新爬这个赛事的赛季的数据
                </td>
                <td>
                    <input id="refresh_league" type="text">
                    <input id="refresh_league_season" type="text">
                    <button type="button" onclick="refreshLeague()">执行</button>
                </td>
            </tr>
            <tr>
                <td>
                    根据比赛id清空比赛缓存
                </td>
                <td>
                    -
                </td>
                <td>
                    <input id="redis_mid" type="text">
                    <button type="button" onclick="delMatchRedis()">执行</button>
                </td>
            </tr>
            <tr>
                <td>
                    根据比赛id重新爬取比赛数据（包括阵容、事件）
                </td>
                <td>
                    -
                </td>
                <td>
                    <input id="match_data_mid" type="text">
                    <button type="button" onclick="spiderMatchData()">执行</button>
                </td>
            </tr>
            <tr>
                <td>
                    清空主页缓存
                </td>
                <td>
                    -
                </td>
                <td>
                    <button type="button" onclick="delHomeRedis()">执行</button>
                </td>
            </tr>
            <tr>
                <td>
                    根据时间重新爬比赛裁判数据
                </td>
                <td>
                    时间：10:00~次日10:00
                </td>
                <td>
                    <input id="referee_data_date" name="date" size="16" type="text" value="{{@$_GET['date']}}"
                           data-date-format="yyyy-m-dd" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd" readonly
                           class="form_datetime">
                    <button type="button" onclick="spiderRefereeData(0)">执行</button>
                </td>
            </tr>
            <tr>
                <td>
                    根据比赛id重新爬去比赛裁判数据
                </td>
                <td>
                    -
                </td>
                <td>
                    <input id="referee_data_mid" type="text">
                    <button type="button" onclick="spiderRefereeData(1)">执行</button>
                </td>
            </tr>
        </table>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        function inflexion() {
            window.open('../../api/spider/analyseForOneHour?isReset=' + $("#select_inflexion").val(), '_blank');
        }
        function lineup() {
            window.open('../../api/spider/spiderLeagueLineup?id=' + document.getElementById('lineupId').value, '_blank');
        }
        function forceLineup() {
            window.open('../../matches/tool/refreshLineup?hname=' + document.getElementById('line_up_hname').value + '&aname=' + document.getElementById('line_up_aname').value, '_blank');
        }

        function spiderBD() {
            window.open('../../api/spider/spiderFillBDByIssueNum?issue_num=' + document.getElementById('issue_num_bd').value, '_blank');
        }

        function spiderBDWin() {
            window.open('../../api/spider/spiderFillBDWinByIssueNum?issue_num=' + document.getElementById('issue_num_bd_win').value, '_blank');
        }
        function spiderKingAll() {
            window.open('../../api/calculateKing/fillTeamOddResultByLeague?lid=' + document.getElementById('king_lid0').value, '_blank');
        }
        function spiderKingByLeagueName() {
            window.open('../../api/calculateKing/fillTeamOddResultByLeague?lname=' + document.getElementById('king_lname0').value, '_blank');
        }
        function spiderKing() {
            window.open('../../api/calculateKing/fillTeamOddResultByTeam?lid=' + document.getElementById('king_lid').value + '&tid=' + document.getElementById('king_tid').value + '&reset=1', '_blank');
        }
        function refreshLeague() {
            window.open('../../api/spider/spiderLeagueRefresh?lid=' + document.getElementById('refresh_league').value + '&season=' + document.getElementById('refresh_league_season').value, '_blank');
        }
        function spiderMatchData() {
            window.open('../../api/spider/spiderMatchData?mid=' + document.getElementById('match_data_mid').value, '_blank');
        }
        function delMatchRedis() {
            window.open('../../api/redis/delMatchRedis?mid=' + document.getElementById('redis_mid').value, '_blank');
        }
        function delHomeRedis() {
            window.open('../../api/redis/delHomeKingRemand', '_blank');
        }
        function spiderRefereeData(type) {
            if(type == 0) {
                window.open('../../api/spider/spiderMatchDataWithReferee?date='+document.getElementById('referee_data_date').value, '_blank');
            } else  if (type == 1) {
                window.open('../../api/spider/spiderMatchDataWithReferee?mid='+document.getElementById('referee_data_mid').value, '_blank');
            }
        }
    </script>
    <link href="../../css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="../../css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <script type="text/javascript" src="../../js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../../js/bootstrap-datetimepicker.min.js" charset="UTF-8"></script>
    <script type="text/javascript" src="../../js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
    <script type="text/javascript">

        $(".form_datetime").datetimepicker({
            language: 'zh-CN',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0
        });
    </script>
@endsection