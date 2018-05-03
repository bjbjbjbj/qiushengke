<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/17
 * Time: 15:03
 */

namespace App\Http\Controllers\Admin\Anchor;


use App\Models\QSK\Anchor\Anchor;
use App\Models\QSK\Anchor\AnchorRoom;
use App\Models\QSK\Anchor\AnchorRoomMatches;
use App\Models\QSK\Match\BasketLeague;
use App\Models\QSK\Match\BasketMatch;
use App\Models\QSK\Match\League;
use App\Models\QSK\Match\Match;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * 比赛相关内容
 * Class MatchController
 * @package App\Http\Controllers\Admin\Anchor
 */
class MatchController extends Controller
{
    const default_page_size = 20;
    const kSportFootball = 1, kSportBasketball = 2;

    /**
     * 足球联赛列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function leagues(Request $request) {
        $sport = self::kSportFootball;
        $page = $this->getLeaguesPage($request, $sport);
        //已选择显示的赛事
        $books = League::query()->where('status', League::kStatusBook)->get();
        $result = ['page'=>$page, 'books'=>$books, 'sport'=>$sport];
        return view('admin.anchor.league.list', $result);//获取联赛
    }

    /**
     * 篮球联赛列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function basketLeagues(Request $request) {
        $sport = self::kSportBasketball;
        $page = $this->getLeaguesPage($request, $sport);
        //已选择显示的赛事
        $books = BasketLeague::query()->where('status', BasketLeague::kStatusBook)->get();
        $result = ['page'=>$page, 'books'=>$books, 'sport'=>$sport];
        return view('admin.anchor.league.list', $result);//获取联赛
    }



    /**
     * 设置预约
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Request $request) {
        $status = $request->input('status');//设置状态
        $id = $request->input('id');//联赛主键
        $sport = $request->input('sport');
        if (!in_array($status, [League::kStatusHide, League::kStatusBook]) || !is_numeric($id)
            || !in_array($sport, [self::kSportFootball, self::kSportBasketball])) {
            return response()->json(['code'=>401 , 'msg'=>'参数错误']);
        }
        if ($sport == self::kSportFootball) {
            $league = League::query()->find($id);
        } else {
            $league = BasketLeague::query()->find($id);
        }
        if (!isset($league)) {
            return response()->json(['code'=>403 , 'msg'=>'赛事不存在']);
        }
        $msg = $status == League::kStatusBook ? '设置' : '取消';
        $league->status = $status;
        $league->save();
        return response()->json(['code'=>200 , 'msg'=>$msg . '预约成功']);
    }

    /**
     * 联赛列表
     * @param Request $request
     * @param $sport
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function getLeaguesPage(Request $request, $sport) {
        $name = $request->input('name');
        $type = $request->input('type');//1：主流，2：热门
        if ($sport == 1) {
            $query = League::query();
        } else {
            $query = BasketLeague::query();
        }
        if (!empty($name)) {
            $query->where('name', 'like', '%' . $name . '%');
        }
        if ($type == 1) {
            $query->where('main', League::kMain);
        } else if ($type == 2) {
            $query->where('hot', League::kHot);
        }
        $page = $query->paginate(self::default_page_size);
        $page->appends($request->all());
        return $page;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * 足球比赛预约
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function matches(Request $request) {
        $sport = self::kSportFootball;
        $page = $this->getMatchesPage($request, $sport);

        //已选择显示的赛事
        $books = League::query()->where('status', League::kStatusBook)->get();
        $rooms = Anchor::bookRooms();

        $result['page'] = $page;
        $result['books'] = $books;
        $result['rooms'] = $rooms;
        $result['sport'] = $sport;
        return view('admin.anchor.match.list', $result);
    }

    /**
     * 篮球比赛预约
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function basketMatches(Request $request) {
        $sport = self::kSportBasketball;
        $page = $this->getMatchesPage($request, $sport);

        //已选择显示的赛事
        $books = BasketLeague::query()->where('status', League::kStatusBook)->get();
        $rooms = Anchor::bookRooms();

        $result['page'] = $page;
        $result['books'] = $books;
        $result['rooms'] = $rooms;
        $result['sport'] = $sport;
        return view('admin.anchor.match.list', $result);
    }

    /**
     * 获取足球比赛信息
     * @param Request $request
     * @param $sport
     * @param int $size
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function getMatchesPage(Request $request, $sport, $size = 10) {
        $name = $request->input('name');
        $lid = $request->input('lid');

        $start_date = date('Y-m-d H:i:s', strtotime('-3 hours'));
        $end_date = date('Y-m-d H:i:s', strtotime('+3 days'));

        if ($sport == self::kSportFootball) {
            $query = Match::query();
        } else {
            $query = BasketMatch::query();
        }

        if (!empty($name)) {
            $query->where(function ($orQuery) use ($name) {
                $orQuery->where('hname', 'like', '%' .$name.'%');
                $orQuery->orWhere('aname', 'like', '%' .$name.'%');
            });
        }
        if (is_numeric($lid)) {
            $query->where('lid', $lid);
        } else {
            $book_ids = $sport == self::kSportFootball ? League::getBookLids() : BasketLeague::getBookLids();
            $query->whereIn('lid', $book_ids);
        }
        $query->whereBetween('time', [$start_date, $end_date]);
        $page = $query->paginate($size);
        $page->appends($request->all());
        return $page;
    }

    /**
     * 主播 预约 直播比赛
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function anchorBook(Request $request) {
        $id = $request->input('id');//预约id
        $room_id = $request->input('room_id');
        $match_id = $request->input('match_id');//比赛ID
        $sport = $request->input('sport');//竞技类型
        $od = $request->input('od');//排序
        $start_time = $request->input('start_time');//开播时间

        if (!is_numeric($room_id) || !is_numeric($match_id) || !in_array($sport, [AnchorRoomMatches::kSportFootball, AnchorRoomMatches::kSportBasketball])) {
            return response()->json(['code'=>401, 'msg'=>'参数错误']);
        }

        $room = AnchorRoom::query()->find($room_id);
        if (!isset($room)) {
            return response()->json(['code'=>403, 'msg'=>'无此直播间']);
        }
        if ($room->status == AnchorRoom::kStatusHide) {
            return response()->json(['code'=>403, 'msg'=>'直播间已被隐藏，请刷新后重新选择']);
        }
        if (!empty($od) && !is_numeric($od)) {
            return response()->json(['code'=>403, 'msg'=>'直播间排序只能填写正整数']);
        }
        $match = self::getMatch($match_id, $sport);
        if (!isset($match)) {
            return response()->json(['code'=>403, 'msg'=>'比赛不存在']);
        }

        if (is_numeric($id)) {
            $anm = AnchorRoomMatches::query()->find($id);
        }

        $query = AnchorRoomMatches::query()->where('room_id', $room_id)->where('mid', $match_id)->where('sport', $sport);
        if (isset($anm)) {
            $query->where('id', '<>', $id);
        } else {
            $anm = new AnchorRoomMatches();
        }

        $hasAnm = $query->first();
        if (isset($hasAnm)) {
            return response()->json(['code'=>403, 'msg'=>'该主播已预约过本场比赛了。']);
        }

        $anm->room_id = $room_id;
        $anm->mid = $match_id;
        $anm->sport = $sport;
        $anm->od = $od;
        $anm->start_time = $start_time;
        $anm->match_time = $match->time;
        $anm->save();

        AnchorController::updateJson($room_id.'-'.$match_id.'-'.$sport,$sport,$match_id);

        return response()->json(['code'=>200, 'msg'=>'预约成功']);
    }

    /**
     * 取消预约直播
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelBook(Request $request) {
        $id = $request->input('id');
        if (!is_numeric($id)) {
            return response()->json(['code'=>401, 'msg'=>'参数错误。']);
        }
        $arm = AnchorRoomMatches::query()->find($id);
        if (isset($arm)) {
            AnchorController::updateJson(null,$arm->sport,$arm->mid);
            $arm->delete();
        }
        return response()->json(['code'=>200, 'msg'=>'取消预约成功']);
    }

    /**
     * 获取比赛
     * @param $mid
     * @param $sport
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public static function getMatch($mid, $sport) {
        if ($sport == AnchorRoomMatches::kSportFootball) {
            return Match::query()->find($mid);
        } else if ($sport == AnchorRoomMatches::kSportBasketball) {
            return BasketMatch::query()->find($mid);
        }
        return null;
    }

}