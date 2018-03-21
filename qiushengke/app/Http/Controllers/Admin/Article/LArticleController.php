<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/21
 * Time: 17:00
 */

namespace App\Http\Controllers\Admin\Article;


use App\Http\Controllers\UploadTrait;
use App\Models\QSK\Article\LArticle;
use App\Models\QSK\Subject\SubjectLeague;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class LArticleController extends Controller
{
    use UploadTrait;

    /**
     * 资讯专题
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function articles(Request $request) {
        $query = LArticle::query();

        $page = $query->paginate(15);

        $result['s_leagues'] = SubjectLeague::getAllLeagues();
        $result['page'] = $page;
        return view('admin.article.list', $result);
    }

    /**
     * 保存文章
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function saveArticle(Request $request) {
        $id = $request->input('id');
        $title = $request->input('title');
        $link = $request->input('link');
        $status = $request->input('status');
        $s_lid = $request->input('s_lid');

        if (empty($title)) {
            return back()->with('error', '标题不能为空');
        }
        if (mb_strlen($title) > 30) {
            return back()->with('error', '标题不能大于30字');
        }
        if (!is_numeric($s_lid)) {
            return back()->with('error', '请选择专题联赛');
        }
        if (empty($link)) {
            return back()->with('error', '链接不能为空');
        }
        if (!in_array($status, [LArticle::kStatusPublish, LArticle::kStatusDraft])) {
            return back()->with('error', '状态错误');
        }
        $sl = SubjectLeague::query()->find($s_lid);
        if (!isset($sl)) {
            return back()->with('error', '专题联赛不存在');
        }

        if (is_numeric($id)) {
            $article = LArticle::query()->find($id);
        }
        if (!isset($article)) {
            $article = new LArticle();
        }

        try {
            if ($request->hasFile('cover')) {
                $file = $request->file('cover');
                $upload = $this->saveUploadedFile($file, 'cover');
                $cover = $upload->getUrl();
                $article->cover = $cover;
            }
            $article->title = $title;
            $article->status = $status;
            $article->link = $link;
            $article->s_lid = $s_lid;
            $article->save();
        } catch (\Exception $exception) {
            Log::error($exception);
            return back()->with('error', '保存失败');
        }

        return back()->with('success', '保存成功');
    }

    /**
     * 删除文章
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteArticle(Request $request) {
        $id = $request->input('id');
        if (is_numeric($id)) {
            $article = LArticle::query()->find($id);
            if (isset($article)) {
                $article->delete();
            }
        }
        return response()->json(['code'=>200, 'msg'=>'删除成功']);
    }

}