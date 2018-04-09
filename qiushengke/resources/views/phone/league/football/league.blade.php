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
            <input type="radio" name="tab" id="Tab_Rank" value="Rank"><label for="Tab_Rank">积分榜</label><!--联赛-->
            {{--<input type="radio" name="tab" id="Tab_Group" value="Group"><label for="Tab_Group">小组赛</label><!--杯赛-->--}}
            {{--<input type="radio" name="tab" id="Tab_Playoffs" value="Playoffs"><label for="Tab_Playoffs">淘汰赛</label><!--杯赛-->--}}
        </div>
    </div>
    <div id="List" style="display: ;">
        @for($i = 0 ; $i < count($schedule) ; $i++)
            <?php
            $matches = $schedule[$i + 1];
            $round = $i + 1;
            if ($season['curr_round'] == $round)
                $isCurr = true;
            else
                $isCurr = false;
            ?>
            @foreach($matches as $match)
                @component('phone.cell.league_match_list_cell',['match'=>$match,'sport'=>$sport,'cdn'=>$cdn,'round'=>$round,'isCurr'=>$isCurr])
                @endcomponent
            @endforeach
        @endfor
        <div id="Round"><!--切换轮次对应函数后端写-->
            <?php
            $lis = array();
            for($i= 0 ; $i < $season['total_round'] ; $i++){
                if($season['curr_round'] == $i+1)
                    $lis[] = array('name'=>$i+1,'checked'=>true);
                else
                    $lis[] = array('name'=>$i+1,'checked'=>false);
            }
            ?>
            @foreach($lis as $item)
                <input onclick="clickRound('{{$item['name']}}')" type="radio" name="round" id="Round_{{$item['name']}}" {{$item['checked']?'checked':''}}><label for="Round_{{$item['name']}}">第{{$item['name']}}轮</label>
            @endforeach
        </div>
    </div>
    <div id="Rank" style="display: none;">
        <table class="head">
            <thead>
            <tr>
                <th></th>
                <th>球队</th>
                <th>赛</th>
                <th>胜/平/负</th>
                <th>得/失</th>
                <th>净</th>
                <th>积</th>
            </tr>
            </thead>
        </table>
        <table>
            <tbody>
            @foreach($score as $item)
                @component('phone.cell.league_list_score',['score'=>$item])
                @endcomponent
            @endforeach
            </tbody>
        </table>
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