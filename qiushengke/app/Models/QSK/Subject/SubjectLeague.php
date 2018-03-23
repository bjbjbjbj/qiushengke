<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/21
 * Time: 18:26
 */

namespace App\Models\QSK\Subject;


use Illuminate\Database\Eloquent\Model;

/**
 * 专题赛事
 * Class SubjectLeague
 * @package App\Models\QSK\Subject
 */
class SubjectLeague extends Model
{
    const kStatusShow = 1, kStatusHide = 2;
    const kSportFootball = 1, kSportBasketball = 2;
    //const kType TODO 待添加

    protected $connection = 'qsk';

    /**
     * 获取所有显示的专题联赛
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getAllLeagues() {
        $query = self::query()->where('status', self::kStatusShow);
        $query->orderBy('sport');
        return $query->get();
    }

    /**
     * 获取名称
     * @return string
     */
    public function getName() {
        $sportCn = $this->sportCn();
        $sportCn = $sportCn == '' ? '' : $sportCn . '：';
        return $sportCn . $this->name;
    }

    /**
     * 类型中文
     * @return string
     */
    public function sportCn() {
        $sport = $this->sport;
        $sportCn = '';
        if ($sport == self::kSportFootball) {
            $sportCn = '足球';
        } else if ($sport == self::kSportBasketball) {
            $sportCn = '篮球';
        }
        return $sportCn;
    }

    /**
     *
     * @return mixed
     */
    public function contentHtml() {
        $content = $this->content;
        if (!empty($content)) {
            $content = str_replace(' ', '&nbsp;', $content);
            $content = str_replace("\n", '<br/>', $content);
        }
        return $content;
    }

}