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
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

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
        $query = Anchor::query();
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
        $page = $query->paginate(self::default_page_size);
        return view('admin.anchor.room.list', ['page'=>$page]);
    }

    /**
     * 保存直播间信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveRoom(Request $request) {
        $name = $request->input('name');
        $anchor_id = $request->input('anchor_id');
        $type = $request->input('type');
        $status = $request->input('status');

        return response()->json();
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


}