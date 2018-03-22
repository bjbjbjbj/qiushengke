<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 17/2/27
 * Time: 下午3:12
 */
namespace App\Http\Controllers\LiaogouMatch;

use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchData;
use App\Models\LiaoGouModels\MatchEvent;
use App\Models\LiaoGouModels\MatchLineup;
use App\Models\LiaoGouModels\Odd;
use App\Models\LiaoGouModels\Score;
use App\Models\LiaoGouModels\Season;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use Monolog\Handler\IFTTTHandler;

class SpiderController extends Controller
{
    public function index($action, Request $request)
    {
        if (method_exists($this, $action)) {
            $this->$action($request);
        } else {
            echo "Error: Not Found action 'SpiderController->$action()'";
        }
    }

    public function deleteArchiveMatch(Request $request){
        $seasons = Season::selectRaw('count(*) ,seasons.lid')
            ->whereNull('archive')
            ->groupBy('lid')
            ->havingRaw('count(*) > 2')
            ->take(1)
            ->get();
        dump($seasons);
        $current = $seasons[0];

        $seasons = Season::where('lid',$current->lid)
            ->whereNull('archive')
            ->orderby('name','asc')->take(1)->get();
        dump($seasons);
        foreach ($seasons as $season){
            //比赛
            $matches = Match::where('lid',$current->lid)
                ->where('season',$season->name)
                ->take(50)
                ->get();
            dump(count($matches));
            foreach ($matches as $match){
                $tmp = new Match();
                $odds = Odd::where('mid',$match->id)->get();
                foreach ($odds as $odd){
                    $odd->delete();
                }

                $odds = MatchLineup::where('id',$match->id)->get();
                foreach ($odds as $odd){
                    $odd->delete();
                }

                $odds = MatchData::where('id',$match->id)->get();
                foreach ($odds as $odd){
                    $odd->delete();
                }

                $odds = MatchEvent::where('mid',$match->id)->get();
                foreach ($odds as $odd){
                    $odd->delete();
                }

                $odds = Score::where('lid',$match->lid)
                    ->where('season',$match->season)
                    ->get();
                foreach ($odds as $odd){
                    $odd->delete();
                }

                $tmp->delete();
            }
            //player

            if (count($matches) == 0){
                $season->archive = 1;
                $season->save();
                dump($season);
            }
        }
    }

    /**
     * 归档比赛
     * @param Request $request
     */
    public function archiveMatch(Request $request){
//        $match = new Match();
//        $match->changeConnection('liaogou_archive');
//        $match->hid = 3;
//        $match->save();
//        return;
        $seasons = Season::selectRaw('count(*) ,seasons.lid')
            ->whereNull('archive')
            ->groupBy('lid')
            ->havingRaw('count(*) > 2')
            ->take(1)
            ->get();
        dump($seasons);
        $current = $seasons[0];

        $seasons = Season::where('lid',$current->lid)->orderby('name','desc')->get();
        foreach ($seasons as $season){
            //比赛
            $matches = Match::where('lid',$current->lid)
                ->where('season',$season->name)
                ->take(50)
                ->get();
            foreach ($matches as $match){
                $tmp = new Match();
                $tmp->changeConnection('liaogou_archive');
                foreach ($match->getAttributes() as $key=>$value){
                    $tmp[$key] = $value;
                }
                $tmp->save();

                $odds = Odd::where('mid',$match->id)->get();
                foreach ($odds as $odd){
                    $tmp = new Odd();
                    $tmp->changeConnection('liaogou_archive');
                    foreach ($odd->getAttributes() as $key=>$value){
                        $tmp[$key] = $value;
                    }
                    $tmp->save();
                }

                $odds = MatchLineup::where('id',$match->id)->get();
                foreach ($odds as $odd){
                    $tmp = new MatchLineup();
                    $tmp->changeConnection('liaogou_archive');
                    foreach ($odd->getAttributes() as $key=>$value){
                        $tmp[$key] = $value;
                    }
                    $tmp->save();
                }

                $odds = MatchData::where('id',$match->id)->get();
                foreach ($odds as $odd){
                    $tmp = new MatchData();
                    $tmp->changeConnection('liaogou_archive');
                    foreach ($odd->getAttributes() as $key=>$value){
                        $tmp[$key] = $value;
                    }
                    $tmp->save();
                }

                $odds = MatchEvent::where('mid',$match->id)->get();
                foreach ($odds as $odd){
                    $tmp = new MatchEvent();
                    $tmp->changeConnection('liaogou_archive');
                    foreach ($odd->getAttributes() as $key=>$value){
                        $tmp[$key] = $value;
                    }
                    $tmp->save();
                }

                $odds = Score::where('lid',$match->lid)
                    ->where('season',$match->season)
                    ->get();
                foreach ($odds as $odd){
                    $tmp = new Score();
                    $tmp->changeConnection('liaogou_archive');
                    foreach ($odd->getAttributes() as $key=>$value){
                        $tmp[$key] = $value;
                    }
                    $tmp->save();
                }
            }
            //player

            if (count($matches) == 0){
                $season->archive = 1;
//                $season->save();
            }
        }
    }
}