<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/14
 * Time: 18:05
 */

namespace App\Http\Controllers\Admin\Anchor;


use App\Http\Controllers\UploadTrait;
use App\Models\QSK\Anchor\Anchor;
use App\Models\QSK\Anchor\AnchorRoom;
use App\Models\QSK\Anchor\LivePlatform;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class AnchorController extends Controller
{
    use UploadTrait;

    const default_page_size = 20;

    /**
     * 主播列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function anchors(Request $request) {
        $status = $request->input('status', Anchor::kStatusValid);
        $query = Anchor::query();
        if (is_numeric($status)) {
            $query->where('anchors.status', $status);
        }
        $page = $query->paginate(self::default_page_size);
        return view('admin.anchor.list', ['page'=>$page]);
    }

    /**
     * 保存主播信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveAnchor(Request $request) {
        $id = $request->input('id');
        $name = $request->input('name');
        $intro = $request->input('intro');

        if (empty($name)) {
            return back()->with('error', '主播名称不能为空');
        }
        if (mb_strlen($name) > 50) {
            return back()->with('error', '主播名称不能大于50字');
        }
        if (!empty($intro) && mb_strlen($intro) > 255) {
            return back()->with('error', '主播简介不能大于255字');
        }

        if (is_numeric($id)) {
            $anchor = Anchor::query()->find($id);
        }

        $isNew = !isset($anchor);
        if ($isNew && !$request->hasFile('icon')) {
            return back()->with('error', '必须上传主播头像');
        }
        if (!isset($anchor)) $anchor = new Anchor();

        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $upload = $this->saveUploadedFile($file, Anchor::icon_disk);
            $icon = $upload->getUrl();
            $anchor->icon = $icon;
        }

        try {
            $anchor->name = $name;
            $anchor->intro = $intro;
            $anchor->save();
        } catch (\Exception $exception) {
            return back()->with('error', '保存失败');
        }
        return back()->with('success', '保存成功');
    }

    /**
     * 改变主播状态
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeStatus(Request $request) {
        $status = $request->input('status');
        if (!in_array($status, [Anchor::kStatusUnValid, Anchor::kStatusValid])) {
            return back()->with('error', '参数错误');
        }
        $id = $request->input('id');
        if (is_numeric($id)) {
            $anchor = Anchor::query()->find($id);
            if (isset($anchor)) {
                $anchor->status = $status;
                $anchor->save();
            }
        }
        return back()->with('success', '');
    }


    //====================================================================================================//

    /**
     * 直播间列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function rooms(Request $request) {
        $query = AnchorRoom::query();
        $query->join('anchors', 'anchors.id', '=', 'anchor_rooms.anchor_id');
        $query->where('anchors.status', Anchor::kStatusValid);
        $query->selectRaw('anchor_rooms.*');
        $page = $query->paginate(self::default_page_size);

        $anchors = Anchor::query()->where('status', Anchor::kStatusValid)->get();
        $types = LivePlatform::query()->where('status', LivePlatform::kStatusShow)->get();

        return view('admin.anchor.room.list', ['page'=>$page, 'anchors'=>$anchors, 'types'=>$types]);
    }

    /**
     * 保存直播间信息
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveRoom(Request $request) {
        $id = $request->input('id');
        $name = $request->input('name');
        $anchor_id = $request->input('anchor_id');
        $type = $request->input('type');
        $link = $request->input('link');
//        $status = $request->input('status');

        $inputs = is_numeric($id) ? [] : $request->all();

        //参数判断 开始
        if(!is_numeric($anchor_id) ) {
            return back()->withInput($inputs)->with('error', '请选择主播');
        }
        if(!is_numeric($type) ) {
            return back()->withInput($inputs)->with('error', '请选择平台');
        }
        if (empty($name)) {
            return back()->withInput($inputs)->with('error', '直播间名称不能为空');
        }
        if (empty($link)) {
            return back()->withInput($inputs)->with('error', '直播间链接/源不能为空');
        }

        $anchor = Anchor::query()->find($anchor_id);
        if (!isset($anchor)) {
            return back()->withInput($inputs)->with('error', '主播不存在');
        }

        $platform = LivePlatform::query()->find($type);
        if (!isset($platform)) {
            return back()->withInput($inputs)->with('error', '平台不存在');
        }
        //参数判断 结束

        if (is_numeric($id)) {
            $room = AnchorRoom::query()->find($id);
        }
        if (!isset($room)) {
            $room = new AnchorRoom();
        }

        try {
            if ($request->hasFile('cover')) {
                $file = $request->file('cover');
                $upload = $this->saveUploadedFile($file, 'cover');
                $room->cover = $upload->getUrl();
            }
            $room->name = $name;
            $room->anchor_id = $anchor_id;
            $room->type = $type;
            $room->link = $link;
            $room->save();
        } catch (\Exception $exception) {
            Log::error($exception);
            return back()->with('error', '保存失败');
        }

        $this->updateJson($room->id);

        return back()->with('success', '保存成功');
    }

    /**
     * 删除直播间
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteRoom(Request $request) {
        $id = $request->input('id');

        return response()->json();
    }

    /**
     * 改变房间状态//设置未：待开播、正在播放、删除 等状态。
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeRoom(Request $request) {

        return response()->json();
    }

    /**
     * 刷新静态文件
     * @param $rid
     * @param $sport
     * @param $mid
     */
    public static function updateJson($rid = null,$sport = null,$mid = null){
        //直播间json
        if (isset($rid)) {
            $ch = curl_init();
            $url = asset('/api/static/live/channel/detail/' . $rid);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);//8秒超时
            curl_exec($ch);
            curl_close($ch);
        }

        if (isset($mid)) {
            //比赛json
            $ch = curl_init();
            $url = asset('/api/static/live/detail/' . $sport . '/' . $mid);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);//8秒超时
            curl_exec($ch);
            curl_close($ch);

            //比赛页面
            $ch = curl_init();
            $url = asset('/api/static/live/' . $sport . '/' . $mid);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);//8秒超时
            curl_exec($ch);
            curl_close($ch);
        }
    }
}