<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/13
 * Time: 16:19
 */

namespace App\Http\Controllers\Admin;


use App\Models\QSK\Link;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LinkController extends Controller
{
    const pageSizeDefault = 20;

    /**
     * 友情链接列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function links(Request $request) {
        $query = Link::query();
        $page = $query->paginate(self::pageSizeDefault);
        return view('admin.link.list', ['links'=>$page]);
    }

    /**
     * 保存/修改 友链
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveLink(Request $request) {
        $id = $request->input('id');//主键
        $name = $request->input('name');//名称
        $url = $request->input('url');//链接
        $od = $request->input('od');//排序
        $show = $request->input('show');//是否显示

        if (empty($name) || empty($url) || !in_array($show, [Link::kShow, Link::kHide])
            || (!empty($od) && !is_numeric($od)) ) {
            return response()->json(['code'=>401, 'msg'=>'参数错误']);
        }

        if (is_numeric($id)) {
            $link = Link::query()->find($id);
        }
        $link = isset($link) ? $link : new Link();

        $link->id = $id;
        $link->name = $name;
        $link->url = $url;
        $link->od = $od;
        $link->show = $show;
        $link->save();

        return response()->json(['code'=>200, 'msg'=>'保存成功']);
    }

    /**
     * 删除友链
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteLink(Request $request) {
        $id = $request->input('id');
        if (is_numeric($id)) {
            $link = Link::query()->find($id);
            if (isset($link)) {
                $link->delete();
            }
        }
        return back()->with('success', '删除成功');
    }

}