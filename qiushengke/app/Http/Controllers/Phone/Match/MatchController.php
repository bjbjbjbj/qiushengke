<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/28
 * Time: 下午6:02
 */
namespace App\Http\Controllers\Phone\Match;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Controllers\PC\FileTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MatchController extends BaseController{
    public function immediate_f(Request $request){
        return $this->immediate($request,'foot');
    }

    private function immediate(Request $request,$sport,$order = 't'){
        if ('basket' == $sport) {
            $sport = 2;
        } else{
            $sport = 1;
        }

        if ($order == 't'){
            $order = 'all';
        }
        else{
            $order = 'league';
        }

        $startDate = date('Ymd');
        $nextDate = date('Ymd',strtotime('1 day'));
        $lastDate = date('Ymd', strtotime('-1 day'));

        $pc_json = FileTool::matchListDataJson($startDate,$sport);
        if (!empty($pc_json)) {
            $result['total'] = count($pc_json['matches']);
            $sortData = $this->sortMatch($pc_json,$sport,$order);
            $result = array_merge($result,$sortData);
            $result['sport'] = $sport;
            $result['nextDate'] = $nextDate;
            $result['lastDate'] = $lastDate;
            $result['currDate'] = date('Y-m-d');
            $this->html_var = array_merge($this->html_var,$result);
            if ($sport == 1)
                return view('phone.match.immediate',$this->html_var);
            else
                return view('phone.match.immediate_bk',$this->html_var);
        }
        else {
//            return abort(500);
        }
    }

    private function sortMatch($pc_json,$sport,$order){
        if ($sport == 1)
        {
            return self::_sortMatch($pc_json);
        }
        else{
            return self::_sortMatchBK($pc_json,$order);
        }
    }

    private function _sortMatchBK($pc_json,$order){
        //比赛列表用
        $matches = array();
        if ($order == 'league'){
            foreach ($pc_json['l_matches'] as $match){
                $matches[] = $match;
            }
        }
        else{
            foreach ($pc_json['matches'] as $match){
                $matches[] = $match;
            }
        }
        $result['matches'] = $matches;
        //赛事
        $filter = array();
        $o_leagues = $pc_json['filter'][0]['data'];
        foreach ($o_leagues as $item){
            $py = $item['py'];
            $lid = $item['id'];
            $name = $item['name'];
            $count = $item['count'];
            $isNBA = isset($item['isFive'])?$item['isFive']:0;
            if (!isset($leagues[$py][$lid])) {
                $filter[$py][$lid] = ["id" => $lid, "name" => $name, "count" => $count, "isNBA" => $isNBA];
            }
        }
        $result['filter'] = $filter;
        return $result;
    }

    private function _sortMatch($pc_json){
        //比赛列表用
        $matches = array();
        foreach ($pc_json['matches'] as $match){
            $matches[] = $match;
        }
        $result['matches'] = $matches;
        //赛事
        $filter = array();
        $o_leagues = $pc_json['filter'][0]['data'];
        foreach ($o_leagues as $item){
            $py = $item['py'];
            $lid = $item['id'];
            $name = $item['name'];
            $count = $item['count'];
            $isFive = isset($item['isFive'])?$item['isFive']:0;
            $isFirst = isset($item['genre'])?(($item['genre'] >> 1 & 1) == 1):0;
            if (!isset($leagues[$py][$lid])) {
                $filter[$py][$lid] = ["id" => $lid, "name" => $name, "count" => $count, "isFive" => $isFive, "isFirst" => $isFirst];
            }
        }
        $result['filter'] = $filter;
        $result['odd'] = $pc_json['odd'];
        return $result;
    }
}