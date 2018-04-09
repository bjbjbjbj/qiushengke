@extends('phone.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/phone/css/immediate.css">
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/phone/css/league.css">
@endsection
@section('content')
    <div id="Navigation">
        <div class="banner">{{$league['name']}}</div>
        <div class="tab">
            <input type="radio" name="tab" id="Tab_Match" value="List" checked><label for="Tab_Match">赛程赛果</label><!--都有-->
            <input type="radio" name="tab" id="Tab_Group" value="Group"><label for="Tab_Group">小组赛</label><!--杯赛-->
            <input type="radio" name="tab" id="Tab_Playoffs" value="Playoffs"><label for="Tab_Playoffs">淘汰赛</label><!--杯赛-->
        </div>
    </div>
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
    <div id="Group" style="display: none;">
        <?php
        $stage = null;
        foreach ($stages as $item){
            if (isset($item['groupMatch'])){
                $stage = $item;
                break;
            }
        }
        ?>
        @if(isset($stage))
            @foreach($stage['groupMatch'] as $key=>$item)
                <table>
                    <tbody>
                    <tr>
                        <th>{{$key}}组</th>
                        <th>球队</th>
                        <th>赛</th>
                        <th>胜/平/负</th>
                        <th>得/失</th>
                        <th>净</th>
                        <th>积</th>
                    </tr>
                    <?php
                    $scores = $item['scores'];
                    ?>
                    @for($i = 0 ; $i < count($scores) ; $i++)
                        <?php
                        $score = $scores[$i];
                        ?>
                        <tr>
                            <td><span>{{$i+1}}</span></td>
                            <td><a href="team.html">{{$score['tname']}}</a></td>
                            <td>{{$score['count']}}</td>
                            <td>{{$score['win']}}/{{$score['draw']}}/{{$score['lose']}}</td>
                            <td>{{$score['goal']}}/{{$score['fumble']}}</td>
                            <td>{{$score['goal'] - $score['fumble']}}</td>
                            <td>{{$score['score']}}</td>
                        </tr>
                    @endfor
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="7"><a href="/wap/cup_league/foot/{{$league['id']}}/{{$key}}.html">查看小组赛程赛果</a></td>
                    </tr>
                    </tfoot>
                </table>
            @endforeach
        @endif
    </div>
    <div id="Playoffs" style="display: none;">
        <?php
        //处理数据,因为无直接标识16强之类,用小组赛后面的都当16强做
        $afterGroup = false;

        //16强
        $sixth = array();
        //8强
        $eightth = array();
        //4强
        $fouth = array();
        //总决赛
        $final = array();
        for ($i = 0 ; $i < count($stages) ; $i++){
            $stage = $stages[$i];
            if (isset($stage['group'])){
                $afterGroup = true;
            }
            //小组赛后
            if ($afterGroup){
                if (isset($stage['combo'])){
                    if (count($sixth) == 0){
                        $sixth = $stage['combo'];
                    }
                    else if (count($eightth) == 0){
                        $eightth = $stage['combo'];
                    }
                    else if (count($fouth) == 0){
                        $fouth = $stage['combo'];
                    }
                    else if (count($final) == 0){
                        $final = $stage['combo'];
                    }
                }
            }
        }
        //排序整理,从总决赛开始
        if (count($final) > 0){
            $tmp = $fouth;
            $fouth = array();
            foreach ($final as $o_key=>$item){
                $hid = explode('_',$o_key)[0];
                $aid = explode('_',$o_key)[1];
                foreach ($tmp as $key=>$value){
                    if (stristr($key,$hid)){
                        $fouth[$key] = $value;
                        break;
                    }
                }
                foreach ($tmp as $key=>$value){
                    if (stristr($key,$aid)){
                        $fouth[$key] = $value;
                        break;
                    }
                }
            }
        }
        if (count($fouth) > 0){
            $tmp = $eightth;
            $eightth = array();
            foreach ($fouth as $o_key => $item){
                $hid = explode('_',$o_key)[0];
                $aid = explode('_',$o_key)[1];
                foreach ($tmp as $key=>$value){
                    if (stristr($key,$hid)){
                        $eightth[$key] = $value;
                        break;
                    }
                }
                foreach ($tmp as $key=>$value){
                    if (stristr($key,$aid)){
                        $eightth[$key] = $value;
                        break;
                    }
                }
            }
        }
        if (count($eightth) > 0){
            $tmp = $sixth;
            $sixth = array();
            foreach ($eightth as $o_key=>$item){
                $hid = explode('_',$o_key)[0];
                $aid = explode('_',$o_key)[1];
                foreach ($tmp as $key=>$value){
                    if (stristr($key,$hid)){
                        $sixth[$key] = $value;
                        break;
                    }
                }
                foreach ($tmp as $key=>$value){
                    if (stristr($key,$aid)){
                        $sixth[$key] = $value;
                        break;
                    }
                }
            }
        }
        ?>
        <div class="line green"><!--8强，比分写在连线区域-->
            @if(count($sixth) > 0)
                @for($i = 0 ; $i < 4 ; $i++)
                    <?php
                    $key = array_keys($sixth)[$i];
                    $item = $sixth[$key];
                    $matches = $item['matches'];
                    if (count($matches) > 0){
                        $hicon = $matches[0]['hicon'];
                        $aicon = $matches[0]['aicon'];
                    }
                    else{
                        $hicon = '';
                        $aicon = '';
                    }
                    ?>
                    @component('phone.league.football.cup_league_playoffs_default',['item'=>$item,'matches'=>$matches,'cdn'=>$cdn,'up'=>1])
                    @endcomponent
                @endfor
            @else
                @component('phone.league.football.cup_league_playoffs_default',['cdn'=>$cdn,'up'=>1])
                @endcomponent
                @component('phone.league.football.cup_league_playoffs_default',['cdn'=>$cdn,'up'=>1])
                @endcomponent
                @component('phone.league.football.cup_league_playoffs_default',['cdn'=>$cdn,'up'=>1])
                @endcomponent
                @component('phone.league.football.cup_league_playoffs_default',['cdn'=>$cdn,'up'=>1])
                @endcomponent
            @endif
        </div>
        <div class="line green">
            @if(count($eightth) > 0)
                @for($i = 0 ; $i < 2 ; $i++)
                    <?php
                    $key = array_keys($eightth)[$i];
                    $item = $eightth[$key];
                    $matches = $item['matches'];
                    if (count($matches) > 0){
                        $hicon = $matches[0]['hicon'];
                        $aicon = $matches[0]['aicon'];
                    }
                    else{
                        $hicon = '';
                        $aicon = '';
                    }
                    ?>
                    @component('phone.league.football.cup_league_playoffs_default',['item'=>$item,'matches'=>$matches,'cdn'=>$cdn,'up'=>1,'score'=>1])
                    @endcomponent
                @endfor
            @else
                @component('phone.league.football.cup_league_playoffs_default',['cdn'=>$cdn,'up'=>1,'score'=>1])
                @endcomponent
                @component('phone.league.football.cup_league_playoffs_default',['cdn'=>$cdn,'up'=>1,'score'=>1])
                @endcomponent
            @endif
        </div>
        <div class="line green">
            @if(count($fouth) > 0)
                @for($i = 0 ; $i < 1 ; $i++)
                    <?php
                    $key = array_keys($fouth)[$i];
                    $item = $fouth[$key];
                    $matches = $item['matches'];
                    if (count($matches) > 0){
                        $hicon = $matches[0]['hicon'];
                        $aicon = $matches[0]['aicon'];
                    }
                    else{
                        $hicon = '';
                        $aicon = '';
                    }
                    ?>
                    @component('phone.league.football.cup_league_playoffs_default',['item'=>$item,'matches'=>$matches,'cdn'=>$cdn,'up'=>1,'score'=>1])
                    @endcomponent
                @endfor
            @else
                @component('phone.league.football.cup_league_playoffs_default',['cdn'=>$cdn,'up'=>1,'score'=>1])
                @endcomponent
            @endif
        </div>
        <div class="line finals">
            @if(count($final) > 0)
                <?php
                $key = array_keys($final)[$i];
                $item = $fouth[$key];
                $matches = $item['matches'];
                if (count($matches) > 0){
                    $hicon = $matches[0]['hicon'];
                    $aicon = $matches[0]['aicon'];
                }
                else{
                    $hicon = '';
                    $aicon = '';
                }
                ?>
                <div class="part">
                    @if(count($matches) > 0 && $matches[0]['status'] == -1)
                        {{$matches[0]['hscore']}}-{{$matches[0]['ascore']}}
                    @endif
                    @if(count($matches) > 1 && $matches[1]['status'] == -1)
                        <br/>{{$matches[1]['hscore']}}-{{$matches[1]['ascore']}}
                    @endif
                    <p class="team"><img src="{{$hicon}}" onerror="{{$cdn}}/phone/img/icon_teamDefault.png">{{$item['hname']}}</p>
                    <p class="team"><img src="{{$aicon}}" onerror="{{$cdn}}/phone/img/icon_teamDefault.png">{{$item['aname']}}</p>
                </div>
            @else
                <div class="part">
                    <p class="team"><img src="{{$cdn}}/phone/img/icon_teamDefault.png">-</p>
                    <p class="team"><img src="{{$cdn}}/phone/img/icon_teamDefault.png">-</p>
                </div>
            @endif
        </div>
        <div class="line orange">
            @if(count($fouth) > 0)
                @for($i = 1 ; $i < 2 ; $i++)
                    <?php
                    $key = array_keys($fouth)[$i];
                    $item = $fouth[$key];
                    $matches = $item['matches'];
                    if (count($matches) > 0){
                        $hicon = $matches[0]['hicon'];
                        $aicon = $matches[0]['aicon'];
                    }
                    else{
                        $hicon = '';
                        $aicon = '';
                    }
                    ?>
                    @component('phone.league.football.cup_league_playoffs_default',['item'=>$item,'matches'=>$matches,'cdn'=>$cdn,'up'=>0,'score'=>1])
                    @endcomponent
                @endfor
            @else
                @component('phone.league.football.cup_league_playoffs_default',['cdn'=>$cdn,'up'=>0,'score'=>1])
                @endcomponent
            @endif
        </div>
        <div class="line orange">
            @if(count($eightth) > 0)
                @for($i = 2 ; $i < 4 ; $i++)
                    <?php
                    $key = array_keys($eightth)[$i];
                    $item = $eightth[$key];
                    $matches = $item['matches'];
                    if (count($matches) > 0){
                        $hicon = $matches[0]['hicon'];
                        $aicon = $matches[0]['aicon'];
                    }
                    else{
                        $hicon = '';
                        $aicon = '';
                    }
                    ?>
                    @component('phone.league.football.cup_league_playoffs_default',['item'=>$item,'matches'=>$matches,'cdn'=>$cdn,'up'=>0,'score'=>1])
                    @endcomponent
                @endfor
            @else
                @component('phone.league.football.cup_league_playoffs_default',['cdn'=>$cdn,'up'=>0,'score'=>1])
                @endcomponent
                @component('phone.league.football.cup_league_playoffs_default',['cdn'=>$cdn,'up'=>0,'score'=>1])
                @endcomponent
            @endif
        </div>
        <div class="line orange">
            @if(count($sixth) > 0)
                @for($i = 4 ; $i < 8 ; $i++)
                    <?php
                    $key = array_keys($sixth)[$i];
                    $item = $sixth[$key];
                    $matches = $item['matches'];
                    if (count($matches) > 0){
                        $hicon = $matches[0]['hicon'];
                        $aicon = $matches[0]['aicon'];
                    }
                    else{
                        $hicon = '';
                        $aicon = '';
                    }
                    ?>
                    @component('phone.league.football.cup_league_playoffs_default',['item'=>$item,'matches'=>$matches,'cdn'=>$cdn,'up'=>0])
                    @endcomponent
                @endfor
            @else
                @component('phone.league.football.cup_league_playoffs_default',['cdn'=>$cdn,'up'=>0])
                @endcomponent
                @component('phone.league.football.cup_league_playoffs_default',['cdn'=>$cdn,'up'=>0])
                @endcomponent
                @component('phone.league.football.cup_league_playoffs_default',['cdn'=>$cdn,'up'=>0])
                @endcomponent
                @component('phone.league.football.cup_league_playoffs_default',['cdn'=>$cdn,'up'=>0])
                @endcomponent
            @endif
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{$cdn}}/phone/js/immediate.js"></script>
    <script type="text/javascript" src="{{$cdn}}/phone/js/league.js"></script>
    <script type="text/javascript">
        function clickRound(round) {
            var trs = $('div#List a[isMatch=1]');
            for (var i = 0 ; i < trs.length ;i++){
                $(trs[i]).addClass('hide');
                $(trs[i]).removeClass('show');
                if(parseInt(trs[i].getAttribute('round')) == parseInt(round)){
                    $(trs[i]).addClass('show');
                }
            }
        }
    </script>
@endsection