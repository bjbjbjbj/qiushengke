<div id="List" style="display: ;">
    @for($i = 0 ; $i < count($stages) ; $i++)
        <?php
        $tmp = $stages[$i];
        $matches = array();
        if (isset($tmp['combo'])){
            foreach ($tmp['combo'] as $key=>$value){
                foreach ($value['matches'] as $match){
                    $match['stage_id'] = $tmp['id'];
                    $matches[] = $match;
                }
            }
        }
        elseif (isset($tmp['groupMatch'])){
            foreach ($tmp['groupMatch'] as $key=>$value){
                foreach ($value['matches'] as $match){
                    $match['stage_id'] = $tmp['id'];
                    $matches[] = $match;
                }
            }
        }
        if ($tmp['status'] == 1)
            $isCurr = true;
        else
            $isCurr = false;
        ?>
        @foreach($matches as $match)
            @component('phone.cell.league_match_list_cell',['match'=>$match,'sport'=>$sport,'cdn'=>$cdn,'round'=>$match['stage_id'],'isCurr'=>$isCurr])
            @endcomponent
        @endforeach
    @endfor
    <div id="Round"><!--切换轮次对应函数后端写-->
        <?php
        $lis = array();
        for($i = 0 ; $i < count($stages) ; $i++){
            $tmp = $stages[$i];

            if ($tmp['status'] == 1)
                $isCurr = true;
            else
                $isCurr = false;
            $tmp['checked'] = $isCurr;
            $lis[] = $tmp;
        }
        ?>
        @foreach($lis as $item)
            <input onclick="clickRound('{{$item['id']}}')" type="radio" name="round" id="Round_{{$item['id']}}" {{$item['checked']?'checked':''}}><label for="Round_{{$item['id']}}">{{$item['name']}}</label>
        @endforeach
    </div>
</div>