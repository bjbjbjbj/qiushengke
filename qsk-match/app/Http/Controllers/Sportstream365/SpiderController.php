<?php
namespace App\Http\Controllers\Sportstream365;
use App\Http\Controllers\TTZB\SpiderTTZBController;
use App\Models\LiaoGouModels\BasketLeague;
use App\Models\LiaoGouModels\BasketMatch;
use App\Models\LiaoGouModels\BasketMatchesAfter;
use App\Models\LiaoGouModels\BasketTeam;
use App\Models\LiaoGouModels\League;
use App\Models\LiaoGouModels\LiaogouAlias;
use App\Models\LiaoGouModels\LiveAlias;
use App\Models\LiaoGouModels\MatchAnalyse;
use App\Models\LiaoGouModels\MatchesAfter;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\MatchLiveChannel;
use App\Models\LiaoGouModels\Team;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use QL\QueryList;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/29
 * Time: 16:59
 */
class SpiderController extends Controller
{
    public function index($action, Request $request)
    {
        if (method_exists($this, $action)) {
            $this->$action($request);
        } else {
            echo "Error: Not Found action 'SpiderISportController->$action()'";
        }
    }

    function spider365() {
        $this->spiderBySport(LiaogouAlias::kSportTypeFootball);
    }

    /**
     * 抓取篮球直播赛事
     */
    function spider365BasketBall() {
        $this->spiderBySport(LiaogouAlias::kSportTypeBasket);
    }

    /**
     * 抓取篮球、足球直播
     * @param int $sport  默认足球
     */
    protected function spiderBySport($sport = LiaogouAlias::kSportTypeFootball) {
        $lng = 'cn';
        if ($sport == LiaogouAlias::kSportTypeFootball) {
            $sports = 1;
            $game = 'football';
        } else if ($sport == LiaogouAlias::kSportTypeBasket) {
            $sports = 3;
            $game = 'basketball';
        } else {
            return;
        }
        $opt = [
            'Referer'=>'https://sportstream365.com/' . $game,
            'Cookie'=>'hide_mirrors=1;lng=' . $lng,
            'User-Agent'=>'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36',
            'X-Requested-With'=>'XMLHttpRequest'
        ];
        $ql = QueryList::get('https://sportstream365.com/LiveFeed/GetLeftMenuShort', 'sports=' . $sports . '&lng=' . $lng . '&partner=24', $opt);
        $html = $ql->getHtml();
        if (empty($html)) {
            return;
        }
        $json = json_decode($html);
        if (!$json->Success) {
            return;
        }
        $matches = $json->Value;
        foreach ($matches as $match) {
            $leagueName = $match->Liga;
            $teamName1 = $match->Opp1;
            $teamName2 = $match->Opp2;
            $vi = $match->VI;
            $time = $match->Start/1000;
            $time = date("Y-m-d H:i:s",$time);
            $content = "SportStream365：$time $leagueName $teamName1 VS $teamName2";
            $lgTeam1 = $this->saveAlias($teamName1, $content, $sport);
            $lgTeam2 = $this->saveAlias($teamName2, $content, $sport);

            if (isset($lgTeam1) || isset($lgTeam2)) {//
                if ($sport == LiaogouAlias::kSportTypeFootball) {
                    $matchQuery = MatchesAfter::query();
                } else {
                    $matchQuery = BasketMatchesAfter::query();
                }

                if (isset($lgTeam1)) {
                    $matchQuery->where(function ($orQuery) use ($lgTeam1) {
                        $orQuery->where('hid', $lgTeam1->lg_id);
                        $orQuery->orWhere('aid', $lgTeam1->lg_id);
                    });
                } else if (isset($lgTeam2)) {
                    $matchQuery->where(function ($orQuery) use ($lgTeam2) {
                        $orQuery->where('hid', $lgTeam2->lg_id);
                        $orQuery->orWhere('aid', $lgTeam2->lg_id);
                    });
                }
                $matchQuery->where('time', '>', date('Y-m-d', strtotime('-2 hour')));
                $matchQuery->where('time', '<', date('Y-m-d', strtotime('+1 days')) . ' 10:00:00');
                $lgMatch = $matchQuery->first();
                if (isset($lgMatch)) {
                    if ($sport == LiaogouAlias::kSportTypeFootball) {
                        $isPrivate = in_array($lgMatch->lid, SpiderTTZBController::football_private_array) ? 2 : 1;
                    } else if ($sport == LiaogouAlias::kSportTypeBasket) {
                        $isPrivate = in_array($lgMatch->lid, SpiderTTZBController::basketball_private_array) ? 2 : 1;
                    }
                    MatchLiveChannel::saveSpiderChannel($lgMatch->id, $sport, MatchLiveChannel::kTypeSS365, $vi, 999, MatchLiveChannel::kPlatformPC, MatchLiveChannel::kPlayerIFrame, "", MatchLiveChannel::kHide, $isPrivate);
                }
            }
        }
        echo $html;
    }

    /**
     * @param $teamName
     * @param $content
     * @param $sport
     * @param $lgFrom
     * @param $laFrom
     * @return LiaogouAlias|\Illuminate\Database\Eloquent\Model|null|void|static
     */
    public function saveAlias($teamName, $content, $sport = LiaogouAlias::kSportTypeFootball, $lgFrom = LiaogouAlias::kFromSportStream365, $laFrom = LiveAlias::kFromSportStream365) {
        if (empty($teamName)) {
            return;
        }
        $lgTeam = LiaogouAlias::query()->where('target_name', $teamName)->where('type', LiaogouAlias::kTypeTeam)
            ->where('from', $lgFrom)->where('sport', $sport)->first();
        if (!isset($lgTeam)) {
            if ($sport == LiaogouAlias::kSportTypeFootball) {
                $team = Team::query()->where('name', $teamName)->first();
                $lg_name = isset($team) ? $team->name : '';
            } else {
                $team = BasketTeam::query()->where('name_china', $teamName)->first();
                $lg_name = isset($team) ? $team->name_china : '';
            }
            $liveTeam = LiveAlias::query()->where('name', $teamName)
                        ->where('type', LiveAlias::kTypeTeam)
                        ->where('from', $laFrom)
                        ->where('sport', $sport)
                        ->first();
            if (isset($team)) {
                $lgTeam = new LiaogouAlias();
                $lgTeam->target_name = $teamName;
                $lgTeam->lg_name = $lg_name;
                $lgTeam->type = LiaogouAlias::kTypeTeam;
                $lgTeam->from = $lgFrom;
                $lgTeam->sport = $sport;
                $lgTeam->lg_id = $team->id;
                $lgTeam->save();
                if (isset($liveTeam)) {
                    $liveTeam->delete();
                }
            } else {
                if (!isset($liveTeam)) {
                    $liveTeam = new LiveAlias();
                    $liveTeam->name = $teamName;
                    $liveTeam->type = LiveAlias::kTypeTeam;
                    $liveTeam->from = $laFrom;
                    $liveTeam->sport = $sport;
                    $liveTeam->content = $content;
                    $liveTeam->save();
                }
            }
        }
        return $lgTeam;
    }

    /**
     * 抓取低调看的多线路比赛。
     * @param Request $request
     */
    public function didiaokan(Request $request) {
        $host = 'http://m.didiaokan.com/';
        $ql = QueryList::getInstance()->get('http://m.didiaokan.com/d/js/js/1458573304.js');
        $lis = $ql->find('li')->htmls();//列表的html；
        $year = date('Y');
        if (isset($lis) && count($lis) > 0) {
            foreach ($lis as $li) {
                $li = preg_replace('/[\\\\|\t|\n|\']/', '', $li);
                $href = $this->getAHref($li);
                $league = $this->getLeague($li);
                $time = $this->getTime($li);
                if (empty($time) || preg_match('/[备用网址|斯诺克|排球]/', $league)) {
                    continue;
                }
                $start_time = strtotime($year . '-' . $time);
                if ($start_time < strtotime('-3 hours')) {
                    continue;
                }
                //是否篮球
                $basket = BasketLeague::hasLeague($league);
                if ($basket) {//是篮球赛事
                    $sport = MatchLive::kSportBasketball;
                } else {
                    $football = League::hasLeague($league);
                    if (isset($football) && $football) {
                        $sport = MatchLive::kSportFootball;
                    } else {
                        continue;
                    }
                }

                $host_name = $this->getMatchHostTeamName($li);//主队名称
                $away_name = $this->getMatchAwayTeamName($li);//客队名称
                $content = '低调看：' . $league . ' ' . $host_name . 'VS' . $away_name . '（' . $time . '）';

                $lgTeam1 = $this->saveAlias($host_name, $content, $sport, LiaogouAlias::kFromDDK, LiveAlias::kFromDDK);
                $lgTeam2 = $this->saveAlias($away_name, $content, $sport, LiaogouAlias::kFromDDK, LiveAlias::kFromDDK);

                if (!empty($href)) {
                    $href = str_replace('//', '/', $href);
                    $href = str_replace('&amp;', '&', $href);
                }
                if (preg_match('/qq.com/', $href)) {
                    continue;
                }

                if (isset($lgTeam1) || isset($lgTeam2)) {//
                    if ($sport == LiaogouAlias::kSportTypeFootball) {
                        $matchQuery = MatchesAfter::query();
                    } else {
                        $matchQuery = BasketMatchesAfter::query();
                    }

                    if (isset($lgTeam1)) {
                        $matchQuery->where(function ($orQuery) use ($lgTeam1) {
                            $orQuery->where('hid', $lgTeam1->lg_id);
                            $orQuery->orWhere('aid', $lgTeam1->lg_id);
                        });
                    } else if (isset($lgTeam2)) {
                        $matchQuery->where(function ($orQuery) use ($lgTeam2) {
                            $orQuery->where('hid', $lgTeam2->lg_id);
                            $orQuery->orWhere('aid', $lgTeam2->lg_id);
                        });
                    }
                    $matchQuery->where('time', '>', date('Y-m-d', strtotime('-2 hour')));
                    $matchQuery->where('time', '<', date('Y-m-d', strtotime('+1 days')) . ' 10:00:00');
                    $lgMatch = $matchQuery->first();
                    if (isset($lgMatch)) {
                        $links = $this->ddkLiveListMethod($href);//抓取列表里面的直播链接。
                        if (count($links) > 0) {
                            foreach ($links as $link) {
                                MatchLiveChannel::saveSpiderChannel($lgMatch->id, $sport, MatchLiveChannel::kTypeDDK, $link, 100, MatchLiveChannel::kPlatformPC, MatchLiveChannel::kPlayerIFrame, "");
                            }
                        } else {
                            MatchLiveChannel::saveSpiderChannel($lgMatch->id, $sport, MatchLiveChannel::kTypeDDK, $href, 100, MatchLiveChannel::kPlatformPC, MatchLiveChannel::kPlayerIFrame, "");
                        }
                    }
                }
                echo $league . ' ' . $host_name . 'VS' . $away_name . '（' . $time . '）' . $href . '<br/>';
                echo '<br/>';
            }
        }
    }

    /**
     * 获取赛事
     * @param $html
     * @return string
     */
    protected function getLeague($html) {
        preg_match('/<div class="desc"><font color="red">(.*)<\/font><\/div[^>]*>/', $html, $match);
        if (count($match) == 2 && !empty($match[1])) {
            return $match[1];
        } else {
            return "";
        }
    }

    /**
     * 获取比赛名称
     * @param $html
     * @return string
     */
    protected function getMatchHostTeamName($html) {
        //<img.*><span.*>(.*)<\/span>
        $pattern = '/<div.*class="team has-hover1"?><img.*><span title=".*">(.*)<\/span><\/div[^>]*>/';
        preg_match($pattern, $html, $match);
        if (count($match) == 2 && !empty($match[1])) {
            return strip_tags($match[1]);
        } else {
            return "";
        }
    }

    /**
     * 获取比赛名称
     * @param $html
     * @return string
     */
    protected function getMatchAwayTeamName($html) {
        $pattern = '/<div.*class="team right has-hover1"?><span>(.*)<\/span><img.*><\/div[^>]*>/';
        preg_match($pattern, $html, $match);
        if (count($match) == 2 && !empty($match[1])) {
            return strip_tags($match[1]);
        } else {
            return "";
        }
    }

    /**
     * 获取列表中的赛事链接。
     * @param $html
     * @return string
     */
    protected function getAHref($html) {
        $pattern = '/href="%5C%22(.*\?classid=\d+&amp;id=\d+)%5C%22">/';
        preg_match($pattern, $html, $match);
        if (count($match) == 2 && !empty($match[1])) {
            return strip_tags($match[1]);
        } else {
            return "";
        }
    }

    /**
     * 获取比赛时间
     * @param $html
     * @return string
     */
    protected function getTime($html) {
        $pattern = '/<div class="time short"?>(\d{2}-\d{2}\s*\d{2}:\d{2}(:\d{2})?)<\/div[^>]*>/';
        preg_match($pattern, $html, $match);
        if (count($match) >= 2 && !empty($match[1])) {
            return strip_tags($match[1]);
        } else {
            return "";
        }
    }

    /**
     * 深入获取地点看视频列表的url
     * @param $request
     */
    public function ddkLiveList(Request $request) {
        //$url = 'http://www.didiaokan.com/a/100209400?classid=1&id=4081';
        //$url = 'http://www.didiaokan.com//pop/1.html';
        //$url = 'http://www.didiaokan.com/a/100209400/p.html';
        $url = 'http://www.didiaokan.com/a/100204100-b/p.html';
        $ql = QueryList::getInstance()->get($url);
        $html = $ql->getHtml();

        if (preg_match('/^<script/', $html)) {
            $url = preg_replace('/(.*)\?.*/', '$1/p.html', $url);
            $ql = QueryList::getInstance()->get($url);
        }

        $as = $ql->find('div.mv_action a');
        $href = $as->attrs('href');
        $names = $as->texts();

        foreach ($names as $index=>$name) {
            $link = $href[$index];
            if (preg_match('/sports\.qq\.com/', $href[$index])) continue;
            echo $name . '：' . $href[$index]. '<br/>';

            $sp_ql = QueryList::getInstance()->get($link);
            $iFrame = $sp_ql->find("iframe");
            $url = $iFrame->attr('src');
            if (!empty($url)) {
                if (preg_match('/str=rtmp:/', $url)) {
                    preg_match('/str=(.*)&/', $url, $match);
                    if (isset($match) && count($match) == 2) {
                        dump($match[1]);
                    }
                }
            } else {
                $script = $sp_ql->find('script');
                $src = $script->attrs('src');
                foreach ($src as $sr) {
                    if (preg_match('/ckplayer.js/', $sr)) {//是否使用ckPlayer
                        $eval = $sp_ql->find('script:last')->text();
                        $js = (new BJ())->unpack($eval);
                        preg_match('/f:\'(.*)\',c/', $js, $match);
                        if (isset($match) && count($match) == 2) {
                            dump($match[1]);
                        }
                    }
                }
            }
        }
    }

    /**
     * 深入获取地点看视频列表的url
     * @param $url
     * @return array
     */
    public function ddkLiveListMethod($url) {
        $array = [];
        if (!empty($url)) {
            $url = str_replace('//', '/', $url);
            $url = str_replace('&amp;', '&', $url);
        }

        $ql = QueryList::getInstance()->get($url);
        $html = $ql->getHtml();
        if (preg_match('/^<script/', $html)) {
            $url = preg_replace('/(.*)\?.*/', '$1/p.html', $url);
            $ql = QueryList::getInstance()->get($url);
        }

        $title = $ql->find("title")->text();
        if(!preg_match('/线路选择/', $title)) {
            return $array;
        }
        $as = $ql->find('div.mv_action a');
        $href = $as->attrs('href');
        $names = $as->texts();
        foreach ($names as $index=>$name) {
            if (preg_match('/sports\.qq\.com/', $href[$index])) continue;
            $array[] = [$name=>$href[$index]];
            echo $name . '：' . $href[$index]. '<br/>';
        }
        return $array;
    }

}