<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/21
 * Time: 16:56
 */

namespace App\Models\QSK\Article;


use App\Models\QSK\Subject\SubjectLeague;
use Illuminate\Database\Eloquent\Model;

class LArticle extends Model
{

    const kStatusDraft = 2, kStatusPublish = 1;//1：发布，2：草稿。

    public function getArticles($lid, $sport, $size = 10) {
        $query = self::query();
        $query->join('subject_leagues', 'subject_leagues.id', '=', 'l_articles.s_lid');
        $query->where('subject_leagues.status', SubjectLeague::kStatusShow);
        $query->where('subject_leagues.lid', $lid);
        $query->where('subject_leagues.sport', $sport);
        return $query->take($size)->get();
    }

}