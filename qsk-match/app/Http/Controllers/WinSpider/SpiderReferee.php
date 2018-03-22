<?php
/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 17/02/07
 * Time: 11:50
 */

namespace App\Http\Controllers\WinSpider;

use App\Models\LiaoGouModels\Team;
use App\Models\WinModels\MatchData;
use App\Models\WinModels\Match;
use App\Models\WinModels\Referee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait SpiderReferee
{

    /**
     * 重爬 matchDatab表里的裁判数据
     */
    public function spiderMatchDataWithReferee(Request $request)
    {
        $mid = $request->input('mid');
        $date = $request->input('date');
        if (isset($mid)) {
            $this->spiderRefereeByMid($mid);
        } else {
            $count = $request->input('count', -1);
            $leagueQuery = $request->input('filter', 'main');
            $matchTime = $this->spiderRefereeByDate($date, $count, $leagueQuery);
            if ($request->input('auto') && strtotime($matchTime) >= strtotime('2014-01-01')) {
                $href = $request->getPathInfo()."?date=$matchTime&auto=1&count=$count&filter=$leagueQuery";
                echo "<script language=JavaScript> location.href='$href';</script>";
                exit;
            }
        }
    }

    /**
     * 根据比赛id重爬 比赛裁判数据
     */
    private function spiderRefereeByMid($mid)
    {
        if (isset($mid)) {
            $matchData = MatchData::query()->find($mid);
            $this->spiderRefereeData($matchData, $mid);
        }
    }

    /**
     * 根据时间 重爬比赛裁判数据
     */
    private function spiderRefereeByDate($date, $count = -1, $leagueQuery = 'main')
    {
        if (isset($date)) {
            $endTime = $date;
        } else {
            $endTime = date('Y-m-d H:i');
        }

        $limitQueryStr = '';
        $startTimeQueryStr = '';
        if ($count > 0) {
            $limitQueryStr = "limit $count";
        } else {
            $startTime = date('Y-m-d H:i', strtotime('-1 day', strtotime($endTime)));
            $startTimeQueryStr = "and time >= '$startTime'";
        }

        $matches = DB::connection('liaogou_match')->select("select m.* from 
        (select matches.* from matches
        left join liaogou_match.leagues as l on l.win_id = matches.lid
        where time < '$endTime' and l.$leagueQuery = 1 $startTimeQueryStr order by time desc $limitQueryStr) as m
        left join match_datas as md on m.id = md.id where (md.referee_id = 0 or md.referee_id is null)");

        if (count($matches) > 0) {
            echo 'match count =' . count($matches) . '<br>';
            foreach ($matches as $match) {
//                $this->spiderRefereeByMid($match->id);
                $this->spiderRefereeTableByMid($match->win_id);
            }
        } else {
            echo 'spider complete!';
        }
        $matchTime = isset($match) ? $match->time : date('Y-m-d H:i:s', strtotime('-30 min', strtotime($endTime)));
        echo "===============^^$matchTime^^==================<br>";

        return $matchTime;
    }

    /**
     * 比赛裁判数据
     */
    private function spiderRefereeData($matchData, $mid, $isReset = false)
    {
        if (!isset($matchData)
            || !isset($mid)
        ) {
            echo 'error:spiderRefereeData matchData id null!.<br>';
            return;
        }
        //同步裁判id
        if (!isset($matchData->referee_id) || $matchData->referee_id == 0) {
            $this->spiderRefereeTableByMid($mid);
        }

        if (isset($matchData->referee_name) && isset($matchData->referee_name_big)) {
            echo 'spiderRefereeData: referee has filled!<br>';
            return;
        }

        $url = "http://txt.win007.com/phone/analysis/".substr($mid, 0, 1)."/".substr($mid, 1, 2)."/cn/$mid.htm?an=iosQiuTan&av=6.5&from=2&r=".time();
        echo $url . '<br>';
        $str = $this->spiderTextFromUrl($url);

        if ($str) {
            $ss = explode("$$", $str);
            $count = count($ss);
            if ($count >= 21) {
                $temp = $ss[21];

                $referee = json_decode($temp, true);
                if ($referee['HasReferee'] == 1) { //如果有裁判才执行
                    $matchData->referee_name = $referee['RefereeNameCn'];
                    $matchData->referee_name_big = $referee['RefereeNameBig'];
                    $matchData->referee_name_en = $referee['RefereeNameEn'];
                    $matchData->referee_h_win = $referee['RefereeWin_h'];
                    $matchData->referee_h_draw = $referee['RefereeDraw_h'];
                    $matchData->referee_h_lose = $referee['RefereeLoss_h'];
                    $matchData->referee_a_win = $referee['RefereeWin_g'];
                    $matchData->referee_a_draw = $referee['RefereeDraw_g'];
                    $matchData->referee_a_lose = $referee['RefereeLoss_g'];
                    $matchData->referee_win_percent = $referee['WinPanPrecent'];
                    $matchData->referee_yellow_avg = $referee['YellowAvg'];

                    \App\Models\LiaoGouModels\MatchData::saveDataWithWinData($matchData);

                    echo 'mid = ' . $mid . ' spider with referee complete!<br>';
                } else {
                    echo 'mid = ' . $mid . ' spider without referee!<br>';
                }
            } else {
                echo 'error: spider count < 7<br>';
            }
        } else {
            echo 'error: spider str is null!<br>';
        }
    }

//    /**
//     * 重爬裁判表数据
//     */
//    public function spiderRefereeTable(Request $request)
//    {
//        $mid = $request->input('mid');
//        $date = $request->input('date');
//        if (isset($mid)) {
//            $this->spiderRefereeTableByMid($mid);
//        } else {
//            $this->spiderRefereeTableByDate($date);
//        }
//    }

//    /**
//     * 根据时间 重爬裁判表数据
//     */
//    private function spiderRefereeTableByDate($date)
//    {
//        if (isset($date)) {
//            $startTime = $date;
//            $date = date_create($date);
//        } else {
//            $date = date_create();
//            $startTime = date_format($date, 'y-m-d');
//        }
//        $endTime = date_format(date_add($date, date_interval_create_from_date_string('1 day')), 'y-m-d');
//        $matches = Match::query()
//            ->leftJoin('match_datas', 'matches.id', '=', 'match_datas.id')
//            ->whereNotNull('match_datas.referee_name')
//            ->whereNotNull('match_datas.referee_name_big')
//            ->where('status', '-1')
//            ->where('time', '>=', $startTime)
//            ->where('time', '<', $endTime)
//            ->orderBy('time', 'desc')
//            ->get();
//        if (count($matches) > 0) {
//            echo 'match count =' . count($matches) . '<br>';
//            foreach ($matches as $match) {
//                $this->spiderRefereeTableByMid($match->id);
//            }
//        } else {
//            echo 'spider complete!';
//        }
//    }

    /**
     * 获取裁判id，并把裁判id保存到matchData表
     */
    private function spiderRefereeTableByMid($mid, $isReset = false)
    {
        if (!isset($mid)) {
            echo 'error:spiderRefereeTableByMid mid is null! <br>';
            return;
        }

        $url = "http://zq.win007.com/referee/".$mid."cn.html";

//        echo $url . '<br>';

        $curl = curl_init();
        //设置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置首标
        curl_setopt($curl, CURLOPT_HEADER, 1);
        //设置cURL参数，要求结果保存到字符串中还是输出到屏幕上。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.98 Safari/537.36");
        //运行cURL，请求网页
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        $html = new \simple_html_dom();
        $html->load($data);

        //裁判数据
        $refereeDivs = $html->find('div.left');
        if (count($refereeDivs) > 0) {
            $trs = $refereeDivs[0]->find('tr');
            $refereeId = NULL;
            if (count($trs) == 4) {
                //裁判id
                $tds1 = $trs[1]->find('td');
                $as1 = $tds1[1]->find('a');
                $href = $as1[0]->getAttribute('href');
                $refereeId = explode('id=', $href)[1];
                //裁判名字
                $refereeName = $as1[0]->plaintext;
                //裁判生日
                $tds2 = $trs[2]->find('td');
                $birth = strip_tags($tds2[1]->plaintext);
                //裁判国籍
                $tds3 = $trs[3]->find('td');
                $nation = strip_tags($tds3[1]->plaintext);

                $ref = Referee::query()->find($refereeId);
                if (!isset($ref)) {
                    $ref = new Referee();
                    $ref->id = $refereeId;
                }
                $ref->name = $refereeName;
                if (isset($birth) && strlen($birth) > 0) {
                    $ref->birth = $birth;
                }
                $ref->nation = $nation;
                $match = Match::query()->find($mid);
                if (isset($match) && isset($match->time)
                    && $ref->last_match_time < $match->time
                ) {
                    $ref->last_match_time = $match->time;
                }
                $ref->save();
                \App\Models\LiaoGouModels\Referee::saveDataWithWinData($ref, $refereeId);

                echo "save referee by mid = $mid; referee_id = $refereeId <br>";
            }
        }
        if (!isset($refereeId)) {
            $refereeId = -1;
        }
        $matchData = MatchData::query()->find($mid);
        if ($refereeId > 0 && !isset($matchData)) {
            $matchData = new MatchData();
            $matchData->id = $mid;

            echo "the matchData of mid = $mid and refereeId = $refereeId is null!<br>";
        }
        if (isset($matchData)) {
            $matchData->referee_id = $refereeId;
            $matchData->save();

            if ($refereeId == -1) {
                echo "the matchData of mid = $mid, refereeId = $refereeId<br>";
            }

            \App\Models\LiaoGouModels\MatchData::saveDataWithWinData($matchData, 0, $mid);
        }
        return;
        //下面的代码暂时没用
        //====================================================================
//
//        $matchData->referee_id = $refereeId;
//        $matchData->save();
//        \App\Models\LiaoGouModels\MatchData::saveDataWithWinData($matchData,$matchData->id);
//
//        $ref = Referee::query()->find($refereeId);
//        //如果上次执行的比赛时间 大于传入比赛 的比赛时间 则退出
//        if (isset($ref) &&
//            (isset($ref->last_match_time) && $ref->last_match_time >= $match->time)
//        ) {
//            if ($isReset) {
//                $ref->last_match_time = $match->time;
//            } else {
//                echo 'error:match time < last match time! <br>';
//                return;
//            }
//        }
//
//        //裁判执法数据
//        $refereeTabs = $html->find('div.right');
//        $refTrs = $refereeTabs[0]->find('tr');
//        if (count($refTrs) >= 4) {
//            $tds1 = $refTrs[1]->find('td');
//            //总场数
//            $spider_count = strip_tags($tds1[1]->plaintext);
//            //主队比赛
//            $h_spans = $tds1[3]->find('span.red');
//            $spider_h_win = strip_tags($h_spans[0]->plaintext); //胜
//            $spider_h_draw = strip_tags($h_spans[1]->plaintext); //平
//            $spider_h_lose = strip_tags($h_spans[2]->plaintext); //负
//            $spider_h_foul = round(strip_tags($tds1[4]->plaintext) * $spider_count); //犯规
//            $spider_h_yellow = round(strip_tags($tds1[5]->plaintext) * $spider_count); //黄牌
//            $spider_h_red = round(strip_tags($tds1[6]->plaintext) * $spider_count); //红牌
//            //客队比赛
//            $tds2 = $refTrs[2]->find('td');
//            $spider_a_foul = round(strip_tags($tds2[2]->plaintext) * $spider_count); //犯规
//            $spider_a_yellow = round(strip_tags($tds2[3]->plaintext) * $spider_count); //黄牌
//            $spider_a_red = round(strip_tags($tds2[4]->plaintext) * $spider_count); //红牌
//        }
//
//        //保存数据到裁判表
//        if (!isset($ref)) {
//            $ref = new Referee();
//            $ref->id = $refereeId;
//            $this->resetReferee($ref, $matchData);
//        }
//        if ($isReset) {
//            $this->resetReferee($ref, $matchData);
//        }
//        if (isset($refereeName) && !isset($ref->name)) $ref->name = $refereeName;
//        if (!empty($birth)) $ref->birth = $birth;
//        if (!empty($nation)) $ref->nation = $nation;
//        if (!empty($spider_count)) $ref->count_spider = $spider_count;
//        if (!empty($spider_h_win)) $ref->win_h_spider = $spider_h_win;
//        if (!empty($spider_h_draw)) $ref->draw_h_spider = $spider_h_draw;
//        if (!empty($spider_h_lose)) $ref->lose_h_spider = $spider_h_lose;
//        if (!empty($spider_h_foul)) $ref->h_foul_spider = $spider_h_foul;
//        if (!empty($spider_h_yellow)) $ref->h_yellow_spider = $spider_h_yellow;
//        if (!empty($spider_h_red)) $ref->h_red_spider = $spider_h_red;
//        if (!empty($spider_a_foul)) $ref->a_foul_spider = $spider_a_foul;
//        if (!empty($spider_a_yellow)) $ref->a_yellow_spider = $spider_a_yellow;
//        if (!empty($spider_a_red)) $ref->a_red_spider = $spider_a_red;
//
//        $this->calculateRefereeData($match, $matchData, $ref);
//
//        $ref->save();
//
//        echo 'referee ID = ' . $refereeId . ' save complete! <br>';
    }

    private function resetReferee($ref, $matchData)
    {
        if (isset($matchData)) {
            $ref->name = $matchData->referee_name;
            $ref->name_big = $matchData->referee_name_big;
            $ref->name_en = $matchData->referee_name_en;
        }
    }

    /**
     * 计算红黄牌 胜平负
     */
    private function calculateRefereeData($match, $matchData, $referee)
    {
        $referee->last_match_time = $match->time;
        if ($match->hscore > $match->ascore) {
            $referee->win_h = $referee->win_h + 1;
        } else if ($match->hscore = $match->ascore) {
            $referee->draw_h = $referee->draw_h + 1;
        } else {
            $referee->lose_h = $referee->lose_h + 1;
        }
        $referee->count = $referee->count + 1;
        $referee->h_red = $referee->h_red + $matchData->h_red;
        $referee->h_yellow = $referee->h_yellow + $matchData->h_yellow;
        $referee->a_red = $referee->a_red + $matchData->a_red;
        $referee->a_yellow = $referee->a_yellow + $matchData->a_yellow;
    }

    public function spiderRefereeCareer(Request $request)
    {
        if ($request->input('auto')) {
            $offset = $request->input('offset', 0);
            $count = $request->input('count', 20);
            $referees = Referee::query()->orderBy('id')->offset($offset)->take($count)->get();
            foreach ($referees as $referee) {
                $this->spiderRefereeCareerById($referee->id);
            }
            if (count($referees) > 0) {
                $nextOffset = $offset + $count;
                $href = $request->getPathInfo() . "?offset=$nextOffset&auto=1";
                echo "<script language=JavaScript> location.href='$href';</script>";
                exit;
            }
        } else {
            $id = $request->input('id');
            $reset = $request->input('reset');
            $this->spiderRefereeCareerById($id, $reset == 1);
        }
    }

    /**
     * 根据裁判id爬取该裁判的所有数据
     */
    private function spiderRefereeCareerById($id, $isReset = false)
    {
        if (isset($id)) {
            $url = "http://zq.win007.com/cn/Team/Referee.aspx?id=" . $id;
            echo $url . '<br>';

//            if ($isReset) {
//                $ref = Referee::query()->find($id);
//                if (isset($ref)) {
//                    $ref->last_match_time = NULL;
//                    $this->resetReferee($ref, NULL);
//                    $ref->save();
//                }
//            }

            //获取页数
            $curl = curl_init();
            //设置你需要抓取的URL
            curl_setopt($curl, CURLOPT_URL, $url);
            //设置首标
            curl_setopt($curl, CURLOPT_HEADER, 1);
            //设置cURL参数，要求结果保存到字符串中还是输出到屏幕上。
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.98 Safari/537.36");
            //运行cURL，请求网页
            $data = curl_exec($curl);
            //关闭URL请求
            curl_close($curl);
            $html = new \simple_html_dom();
            $html->load($data);

            $pageNum = count($html->find('div.AspNetPager')[0]->find('a.AspNetPager'));
            $inputs = $html->find('input');

            $__VIEWSTATE = '';
            $__EVENTVALIDATION = '';
            foreach ($inputs as $input) {
                if ($input->getAttribute('name') == '__EVENTVALIDATION') {
                    $__EVENTVALIDATION = $input->getAttribute('value');
                } else if ($input->getAttribute('name') == '__VIEWSTATE') {
                    $__VIEWSTATE = $input->getAttribute('value');
                }
            }

            echo 'pageNum = ' . $pageNum . '<br>';
            echo '__EVENTVALIDATION = ' . $__EVENTVALIDATION . '<br>';
            echo '__VIEWSTATE = ' . $__VIEWSTATE . '<br>';

            $this->spiderRefereeCareerByPage($id, $url, 1, $__VIEWSTATE, $__EVENTVALIDATION, $isReset);
//            for ($i = $pageNum; $i > 0; $i--) {
//                $this->spiderRefereeCareerByPage($id, $url, $i, $__VIEWSTATE, $__EVENTVALIDATION, $isReset);
//            }
        }
    }

    private function spiderRefereeCareerByPage($refereeId, $url, $page, $__VIEWSTATE, $__EVENTVALIDATION, $isReset = false)
    {
        $data = array("__EVENTTARGET" => "AspNetPager1", "__EVENTARGUMENT" => "$page", "__EVENTVALIDATION" => "$__EVENTVALIDATION", "__VIEWSTATE" => "$__VIEWSTATE");

        $pageData = $this->spiderTextFromUrlByPost($url, $data);
        $html = new \simple_html_dom();
        $html->load($pageData);

        $divRights = $html->find('div.right');
        $tables = $divRights[0]->find('table');
        $trs = $tables[0]->find('tr');

        $count = count($trs);
        echo 'page = ' . $page . ', count = ' . $count . '<br>';

        for ($i = $count - 1; $i > 0; $i--) {
            $this->spiderEachRefereeMatch($refereeId, $trs[$i], $isReset && $i == $count - 1);
        }
    }

    /**
     * 爬每场 裁判执法的比赛
     */
    private function spiderEachRefereeMatch($refereeId, $tr, $isReset = false)
    {
        $tds = $tr->find('td');
        //只有是主裁判的情况才计算
        if (count($tds) < 6 || $tds[5]->plaintext != '主裁判') {
            echo 'error: spiderEachRefereeMatch referee is not chief!<br>';
            return;
        }

        $time = $tds[1]->plaintext;
        $hid = $this->getTeamId($tds[2]->find('a')[0]->getAttribute('href'));
        $aid = $this->getTeamId($tds[2]->find('a')[1]->getAttribute('href'));

        $date = date_create($time);
        $endTime = date_format(date_add($date, date_interval_create_from_date_string('1 day')), 'y-m-d');

        $lg_hid = Team::getTeamIdWithType($hid, 'win_id');
        $lg_aid = Team::getTeamIdWithType($aid, 'win_id');
        //查找比赛
        $lg_match = \App\Models\LiaoGouModels\Match::query()
            ->where('time', '>=', $time)
            ->where('time', '<', $endTime)
            ->where('hid', '=', $lg_hid)
            ->where('aid', '=', $lg_aid)->first();
        if (isset($lg_match)) {
            $mid = $lg_match->win_id;
            echo 'spider by match id=' . $mid . '<br>';
//            $this->spiderRefereeData(MatchData::find($match->id), $match->id, $isReset);
            $matchData = MatchData::query()->find($mid);
            if (!isset($matchData)) {
                $matchData = new MatchData();
                $matchData->id = $mid;
            }
            $matchData->referee_id = $refereeId;
            $matchData->save();

            \App\Models\LiaoGouModels\MatchData::saveDataWithWinData($matchData, 0, $mid);
        } else {
            echo 'spider by match is empty!' . '<br>';
        }
    }

    /**
     * 获取球队id
     */
    private function getTeamId($str)
    {
        $tid = -1;
        if (isset($str)) {
            $temp = explode('.', $str)[0];
            $temps = explode('/', $temp);
            $tid = $temps[count($temps) - 1];
        }
        return $tid;
    }

    /**
     * 计算裁判与球队相关的利好、利空关系
     */
    private function calculateRefereeResult($lg_refereeId) {
        if ($lg_refereeId > 0) {
            $referee = \App\Models\LiaoGouModels\Referee::query()->find($lg_refereeId);
            if (is_null($referee)) {
                echo "refereeId = $lg_refereeId is null!!<br>";
                return;
            }

            $matchDatas = DB::connection('liaogou_match')->select("
            select md.*, m.time, m.hscore, m.ascore from
            (select * from match_datas where referee_id = $lg_refereeId) as md
            left join matches as m on m.id = md.id
            where m.status = -1 order by m.time desc;
            ");
            if (count($matchDatas) <= 0 || $matchDatas[0]->time <= $referee->last_end_match_time) {
                return;
            }
            //保存最新保存的时间
            $referee->last_end_match_time = $matchDatas[0]->time;

            $lately = 10;
            $recent = 30;

            $lately_h_win_count = 0;$recent_h_win_count = 0;$h_win_count = 0;
            $lately_h_lose_count = 0;$recent_h_lose_count = 0;$h_lose_count = 0;
            $lately_draw_count = 0;$recent_draw_count = 0;$draw_count = 0;
            $lately_h_yellow_count = 0;$recent_h_yellow_count = 0;$h_yellow_count = 0;
            $lately_yellow_match_count = 0;$recent_yellow_match_count = 0;$yellow_match_count = 0;
            $lately_a_yellow_count = 0;$recent_a_yellow_count = 0;$a_yellow_count = 0;
            $lately_h_red_count = 0;$recent_h_red_count = 0;$h_red_count = 0;
            $lately_a_red_count = 0;$recent_a_red_count = 0;$a_red_count = 0;
            $lately_h_foul_count = 0;$recent_h_foul_count = 0;$h_foul_count = 0;
            $lately_a_foul_count = 0;$recent_a_foul_count = 0;$a_foul_count = 0;
            $lately_foul_match_count = 0;$recent_foul_match_count = 0;$foul_match_count = 0;
            $lately_match_count = 0;$recent_match_count = 0;$match_count = 0;

            foreach ($matchDatas as $data) {
                $diff = $data->hscore - $data->ascore;
                //裁判执法的近10场比赛的统计数据
                if ($recent_match_count < $lately) {
                    if ($diff > 0) {
                        $lately_h_win_count++;
                    } else if ($diff == 0) {
                        $lately_draw_count++;
                    } else {
                        $lately_h_lose_count++;
                    }
                    if (isset($data->h_yellow) && isset($data->a_yellow)) {
                        $lately_h_yellow_count += $data->h_yellow;
                        $lately_a_yellow_count += $data->a_yellow;
                        $lately_h_red_count += isset($data->h_red) ? $data->h_red : 0;
                        $lately_a_red_count += isset($data->a_red) ? $data->a_red : 0;
                        $lately_yellow_match_count++;
                    }
                    if (isset($data->h_foul) && isset($data->a_foul) && $data->h_foul > 0 && $data->a_foul > 0) {
                        $lately_h_foul_count += $data->h_foul;
                        $lately_a_foul_count += $data->a_foul;
                        $lately_foul_match_count++;
                    }
                    $lately_match_count++;
                }
                //裁判执法的近30场比赛的统计数据
                if ($recent_match_count < $recent) {
                    if ($diff > 0) {
                        $recent_h_win_count++;
                    } else if ($diff == 0) {
                        $recent_draw_count++;
                    } else {
                        $recent_h_lose_count++;
                    }
                    if (isset($data->h_yellow) && isset($data->a_yellow)) {
                        $recent_h_yellow_count += $data->h_yellow;
                        $recent_a_yellow_count += $data->a_yellow;
                        $recent_h_red_count += isset($data->h_red) ? $data->h_red : 0;
                        $recent_a_red_count += isset($data->a_red) ? $data->a_red : 0;
                        $recent_yellow_match_count++;
                    }
                    if (isset($data->h_foul) && isset($data->a_foul) && $data->h_foul > 0 && $data->a_foul > 0) {
                        $recent_h_foul_count += $data->h_foul;
                        $recent_a_foul_count += $data->a_foul;
                        $recent_foul_match_count++;
                    }
                    $recent_match_count++;
                }
                //裁判所有执法比赛的统计数据
                if ($diff > 0) {
                    $h_win_count++;
                } else if ($diff == 0) {
                    $draw_count++;
                } else {
                    $h_lose_count++;
                }
                if (isset($data->h_yellow) && isset($data->a_yellow)) {
                    $h_yellow_count += $data->h_yellow;
                    $a_yellow_count += $data->a_yellow;
                    $h_red_count += isset($data->h_red) ? $data->h_red : 0;
                    $a_red_count += isset($data->a_red) ? $data->a_red : 0;
                    $yellow_match_count++;
                }
                if (isset($data->h_foul) && isset($data->a_foul) && $data->h_foul > 0 && $data->a_foul > 0) {
                    $h_foul_count += $data->h_foul;
                    $a_foul_count += $data->a_foul;
                    $foul_match_count++;
                }
                $match_count++;
            }
            //裁判执法的近10场比赛的统计数据
            $referee->lately_count = $lately_match_count;
            $referee->lately_win = $lately_h_win_count;
            $referee->lately_draw = $lately_draw_count;
            $referee->lately_lose = $lately_h_lose_count;
            $referee->lately_h_foul_avg = $this->getAvg($lately_h_foul_count, $lately_foul_match_count);
            $referee->lately_a_foul_avg = $this->getAvg($lately_a_foul_count, $lately_foul_match_count);
            $referee->lately_h_red_avg = $this->getAvg($lately_h_red_count, $lately_yellow_match_count);
            $referee->lately_a_red_avg = $this->getAvg($lately_a_red_count, $lately_yellow_match_count);
            $referee->lately_h_yellow_avg = $this->getAvg($lately_h_yellow_count, $lately_yellow_match_count);
            $referee->lately_a_yellow_avg = $this->getAvg($lately_a_yellow_count, $lately_yellow_match_count);
            //裁判执法的近30场比赛的统计数据
            $referee->recent_count = $recent_match_count;
            $referee->recent_win = $recent_h_win_count;
            $referee->recent_draw = $recent_draw_count;
            $referee->recent_lose = $recent_h_lose_count;
            $referee->recent_h_foul_avg = $this->getAvg($recent_h_foul_count, $recent_foul_match_count);
            $referee->recent_a_foul_avg = $this->getAvg($recent_a_foul_count, $recent_foul_match_count);
            $referee->recent_h_red_avg = $this->getAvg($recent_h_red_count, $recent_yellow_match_count);
            $referee->recent_a_red_avg = $this->getAvg($recent_a_red_count, $recent_yellow_match_count);
            $referee->recent_h_yellow_avg = $this->getAvg($recent_h_yellow_count, $recent_yellow_match_count);
            $referee->recent_a_yellow_avg = $this->getAvg($recent_a_yellow_count, $recent_yellow_match_count);
            //裁判所有执法比赛的统计数据
            $referee->count = $match_count;
            $referee->win = $h_win_count;
            $referee->draw = $draw_count;
            $referee->lose = $h_lose_count;
            $referee->h_foul_avg = $this->getAvg($h_foul_count, $foul_match_count);
            $referee->a_foul_avg = $this->getAvg($a_foul_count, $foul_match_count);
            $referee->h_red_avg = $this->getAvg($h_red_count, $yellow_match_count);
            $referee->a_red_avg = $this->getAvg($a_red_count, $yellow_match_count);
            $referee->h_yellow_avg = $this->getAvg($h_yellow_count, $yellow_match_count);
            $referee->a_yellow_avg = $this->getAvg($a_yellow_count, $yellow_match_count);

//            dump($referee);
            $referee->save();

            echo "save referee id = $lg_refereeId statistic complete!<br>";
        }
    }

    private function getAvg($count, $total) {
        return $total > 0 ? round($count/$total, 2) : 0;
    }

    private function calculateAllRefereeResult(Request $request) {
        $referees = \App\Models\LiaoGouModels\Referee::query()
            ->whereNull('last_end_match_time')
            ->orderBy('id')->take($request->input('count', 10))->get();

        foreach ($referees as $referee) {
            $this->calculateRefereeResult($referee->id);
        }
        if ($request->input('auto') && count($referees) > 0) {
            echo "<script language=JavaScript>location.reload();</script>";
            exit;
        }
    }

    /**
     * 转换match data表保存错误的裁判id
     */
    private function convertErrorRefereeId() {
        $matchDatas = DB::connection('liaogou_match')->select("
        select md.id, m.time from 
        (select * from liaogou_match.match_datas where referee_id > 0) as md
        left join liaogou_match.referees as ref on md.referee_id = ref.id
        left join liaogou_match.matches as m on m.id = md.id
        where ref.name is null order by m.time desc limit 100;
        ");

        foreach ($matchDatas as $md) {
            $matchData = \App\Models\LiaoGouModels\MatchData::query()->find($md->id);
            $refereeId = \App\Models\LiaoGouModels\Referee::getRefIdWith($matchData->referee_id, 'win_id');
            $matchData->referee_id = $refereeId;
            $matchData->save();
        }

        if (isset($md)) {
            echo "================$md->time================<br>";
        }
    }
}