<?php

namespace App\Models\AnalyseModels;

use App\Models\LiaoGouModels\BasketMatch;
use Illuminate\Database\Eloquent\Model;

class BasketTeam extends Model
{
    protected $connection = 'analyse_match';

    //===============比赛接口相关===========================
    public static function getIconByTid($tid) {
        $team = BasketTeam::query()->find($tid);
        if (isset($team)) {
            $icon = $team->icon;
        }
        if (isset($icon) && strlen($icon) > 0 && !str_contains($icon, '/files/team/noflag.gif')) {
            if (str_contains($icon, '.gif') && str_contains($icon, 'team/images/2005')) {
                return "";
            }
            return 'http://nba.win007.com'.$icon;
        } else {
            return "";
        }
    }

    public static function getIcon($icon) {
        if (isset($icon) && strlen($icon) > 0 && !str_contains($icon, '/files/team/noflag.gif')) {
            if (str_contains($icon, '.gif') && str_contains($icon, 'team/images/2005')) {
                return "";
            }
            return 'http://nba.win007.com'.$icon;
        } else {
            return "";
        }
    }
}
