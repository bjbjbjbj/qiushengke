<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/14
 * Time: 16:56
 */

namespace App\Http\Controllers\Admin\Anchor;



use App\Models\QSK\Anchor\LivePlatform;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LivePlatformController extends Controller
{

    /**
     * 直播间平台列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function platforms(Request $request) {
        $query = LivePlatform::query();

        $page = $query->paginate(20);
        return view('admin.anchor.platform.list', ['page'=>$page]);
    }

    /**
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function savePlatform(Request $request) {
        $id = $request->input('id');
        $name = $request->input('name');
        $status = $request->input('status');

        if (empty($name) || !in_array($status, [LivePlatform::kStatusShow, LivePlatform::kStatusHide])) {
            return response()->json(['code'=>401, 'msg'=>'参数错误']);
        }

        if (is_numeric($id)) {
            $platform = LivePlatform::query()->find($id);
        }
        if (!isset($platform)) {
            $platform = new LivePlatform();
        }

        try {
            $platform->name = $name;
            $platform->status = $status;
            $platform->save();
        } catch (\Exception $exception) {
            return response()->json(['code'=>500, 'msg'=>'保存失败' . $exception->getMessage()]);
        }
        return response()->json(['code'=>200, 'msg'=>'保存成功']);
    }

}