<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/20
 * Time: 16:27
 */

namespace App\Http\Controllers\Admin\Video;


use App\Models\QSK\Subject\SubjectLeague;
use App\Models\QSK\Video\HotVideo;
use App\Models\QSK\Video\HotVideoType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class VideoController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function videos(Request $request) {
        $query = HotVideo::query();
        $query->selectRaw('*, ifNull(od, 999) as n_od');
        $query->orderBy('status')->orderBy('n_od');
        $page = $query->paginate(20);

        $result['page'] = $page;

//        $type_array = [];
//        $types = HotVideoType::getAllTypes();
//        foreach ($types as $type) {
//            $type_array[$type->id] = $type->name;
//        }
//
//        $result['types'] = $type_array;
        $league_array = [];
        $leagues = SubjectLeague::getAllLeagues();
        foreach ($leagues as $league) {
            $league_array[$league->id] = $league->getName();
        }
        $result['leagues'] = $league_array;
        $result['players'] = HotVideo::kPlayerArray;

        return view('admin.video.videos', $result);
    }

    /**
     * 新建修改录像页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function videoEdit(Request $request) {
        $id = $request->input('id');
        $result = [];
        if (is_numeric($id)) {
            $video = HotVideo::query()->find($id);
            $result['video'] = $video;
        }
        //$result['types'] = HotVideoType::getAllTypes();
        $result['leagues'] = SubjectLeague::getAllLeagues();
        $result['players'] = HotVideo::kPlayerArray;
        return view('admin.video.videos_edit', $result);
    }

    /**
     * 保存录像信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveVideo(Request $request) {
        $id = $request->input('id');
        $title = $request->input('title');//标题
//        $type_id = $request->input('type_id');//类型
        $s_lid = $request->input('s_lid');//专题联赛
        $content = $request->input('content');//源链接
        $player = $request->input('player');//播放方式
        $status = $request->input('status');//显示/隐藏
        $od = $request->input('od');//排序
        $cover = $request->input('cover');//封面

        //判断参数
        if (empty($title)) {
            return response()->json(['code'=>401, 'msg'=>'标题不能为空']);
        }
        if (mb_strlen($title) > 30) {
            return response()->json(['code'=>401, 'msg'=>'标题不能超过30字']);
        }
//        if (!is_numeric($type_id)) {
//            return response()->json(['code'=>401, 'msg'=>'类型不能为空']);
//        }
        if (!is_numeric($s_lid)) {
            return response()->json(['code'=>401, 'msg'=>'专题联赛填写错误']);
        }
        if (empty($content)) {
            return response()->json(['code'=>401, 'msg'=>'源链接不能为空']);
        }
        if (!isset(HotVideo::kPlayerArray[$player])) {
            return response()->json(['code'=>401, 'msg'=>'播放方式选择错误']);
        }
        if (!in_array($status, [HotVideo::kStatusShow, HotVideo::kStatusHide])) {
            return response()->json(['code'=>401, 'msg'=>'显示参数错误']);
        }
        if (!empty($od) && !is_numeric($od)) {
            return response()->json(['code'=>401, 'msg'=>'排序错误']);
        }
//        $type = HotVideoType::query()->find($type_id);
//        if (!isset($type) || $type->status != HotVideoType::kStatusShow) {
//            return response()->json(['code'=>401, 'msg'=>'类型不存在']);
//        }
        $sj = SubjectLeague::query()->find($s_lid);
        if (!isset($sj) || $sj->status != SubjectLeague::kStatusShow) {
            return response()->json(['code'=>401, 'msg'=>'专题联赛不存在']);
        }
        //判断参数 结束

        if (is_numeric($id)) {
            $video = HotVideo::query()->find($id);
        }
        if (!isset($video)) {
            $video = new HotVideo();
        }

        try {
            $video->title = $title;
//            $video->type_id = $type_id;
            $video->s_lid = $s_lid;
            $video->content = $content;
            $video->player = $player;
            $video->status = $status;
            $video->od = $od;
            $video->cover = $cover;
            $video->save();
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['code'=>500, 'msg'=>'保存失败']);
        }

        return response()->json(['code'=>200, 'msg'=>'保存成功']);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * 录像分类
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function types(Request $request) {
        $query = HotVideoType::query();
        $query->selectRaw('*, ifNull(od, 999) as n_od');
        $query->orderBy('status')->orderBy('n_od');
        $page = $query->paginate(2);
        $result['page'] = $page;
        return view('admin.video.types', $result);
    }

    /**
     * 保存热门分类
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveType(Request $request) {
        $id = $request->input('id');
        $name = $request->input('name');
        $status = $request->input('status');
        $od = $request->input('od');

        if (!in_array($status, [HotVideoType::kStatusShow, HotVideoType::kStatusHide])) {
            return response()->json(['code'=>401, 'msg'=>'参数错误']);
        }
        if (empty($name)) {
            return response()->json(['code'=>401, 'msg'=>'分类名称不能为空']);
        }
        if (!empty($od) && !is_numeric($od)) {
            return response()->json(['code'=>401, 'msg'=>'排序填写错误']);
        }

        if (is_numeric($id)) {
               $type = HotVideoType::query()->find($id);
        }
        if (!isset($type)) {
            $type = new HotVideoType();
        }
        try {
            $type->name = $name;
            $type->status = $status;
            $type->od = $od;
            $type->save();
        } catch (\Exception $exception) {
            echo ($exception->getMessage());
            return response()->json(['code'=>500, 'msg'=>'保存失败']);
        }
        return response()->json(['code'=>200, 'msg'=>'保存成功']);
    }

}