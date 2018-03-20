<?php
$week = array('周日','周一','周二','周三','周四','周五','周六');
$hicon = strlen($match['hicon']) > 0 ? $match['hicon'] : '/pc/img/icon_teamDefault.png';
$aicon = strlen($match['aicon']) > 0 ? $match['aicon'] : '/pc/img/icon_teamDefault.png';
?>
<div id="Info" class="football">
    <p class="info">{{$match['league']}}<br/>比赛时间：{{date('Y-m-d  H:i',$match['time'])}}  {{$week[date('w',$match['time'])]}}</p>
    <p class="team host"><span class="img"><img src="{{$hicon}}"></span>{{$match['hname']}}
        @if($rank['leagueRank']['hLeagueRank'] > 0)
            <em>【{{$rank['leagueRank']['hLeagueName']}}{{$rank['leagueRank']['hLeagueRank']}}】</em>
        @endif
    </p>
    <p class="team away"><span class="img"><img src="{{$aicon}}"></span>{{$match['aname']}}
        @if($rank['leagueRank']['aLeagueRank'] > 0)
            <em>【{{$rank['leagueRank']['aLeagueName']}}{{$rank['leagueRank']['aLeagueRank']}}】</em>
        @endif
    </p>
    </p>
    @if($match['status'] > 0 || $match['status'] == -1)
        <p class="score">{{$match['hscore']}} - {{$match['ascore']}}</p>
    @else
        <p class="score">VS</p>
    @endif
    <p class="sameOdd">主胜：{{$analyse['sameOdd']['asia']['win']}}%&nbsp;&nbsp;平局：{{$analyse['sameOdd']['asia']['draw']}}%&nbsp;&nbsp;客胜：{{$analyse['sameOdd']['asia']['lose']}}%<span>历史同赔统计</span></p>
    <div class="odd">
        @if(isset($match['asiamiddle2']))
            <p>亚：{{\App\Http\Controllers\PC\CommonTool::float2Decimal($match['asiaup2'])}}&nbsp;&nbsp;{{\App\Http\Controllers\PC\CommonTool::float2Decimal($match['asiamiddle2'], true)}}&nbsp;&nbsp;{{\App\Http\Controllers\PC\CommonTool::float2Decimal($match['asiadown2'])}}</p>
        @else
            <p>亚：-&nbsp;&nbsp;-&nbsp;&nbsp;-</p>
        @endif
        @if(isset($match['oumiddle2']))
            <p>欧：{{\App\Http\Controllers\PC\CommonTool::float2Decimal($match['ouup2'])}}&nbsp;&nbsp;{{\App\Http\Controllers\PC\CommonTool::float2Decimal($match['oumiddle2'])}}&nbsp;&nbsp;{{\App\Http\Controllers\PC\CommonTool::float2Decimal($match['oudown2'])}}</p>
        @else
            <p>欧：-&nbsp;&nbsp;-&nbsp;&nbsp;-</p>
        @endif
        @if(isset($match['goalmiddle2']))
            <p>大：{{\App\Http\Controllers\PC\CommonTool::float2Decimal($match['goalup2'])}}&nbsp;&nbsp;{{\App\Http\Controllers\PC\CommonTool::getHandicapCn($match['goalmiddle2'], '', \App\Http\Controllers\PC\CommonTool::k_odd_type_ou)}}&nbsp;&nbsp;{{\App\Http\Controllers\PC\CommonTool::float2Decimal($match['goaldown2'])}}</p>
        @else
            <p>大：-&nbsp;&nbsp;-&nbsp;&nbsp;-</p>
        @endif
    </div>
</div>
<div id="Prediction">
    <p class="name">赛果概率</p>
    <div class="pbox">
        (历史交锋)
        <?php
        $winh = 0;
        $drawh = 0;
        $loseh = 0;
        $wina = 0;
        $drawa = 0;
        $losea = 0;
        $hid = $match['hid'];
        $aid = $match['aid'];
        if (isset($analyse['historyBattle']['historyBattle']['nhnl'])){
            foreach($analyse['historyBattle']['historyBattle']['nhnl'] as $match){
                if($match['hscore'] > $match['ascore'])
                    if($match['hid'] == $hid)
                        $winh++;
                    else
                        $loseh++;
                elseif($match['hscore'] < $match['ascore'])
                    if($match['hid'] == $hid)
                        $loseh++;
                    else
                        $winh++;
                else
                    $drawh++;
            }

            foreach($analyse['historyBattle']['historyBattle']['nhnl'] as $match){
                if($match['hscore'] > $match['ascore'])
                    if($match['hid'] == $aid)
                        $wina++;
                    else
                        $losea++;
                elseif($match['hscore'] < $match['ascore'])
                    if($match['hid'] == $aid)
                        $losea++;
                    else
                        $wina++;
                else
                    $drawa++;
            }
        }


        $homePer = ($winh + $drawa + $wina) > 0 ? round(100*($winh/($winh+$drawa+$wina)),0) : 0;
        $awayPer = ($winh + $drawa + $wina) > 0 ? round(100*($wina/($winh+$drawa+$wina)),0) : 0;

        ?>
        <p class="wdl host"><span>{{$winh}}</span>胜<span>{{$drawh}}</span>平<span>{{$loseh}}</span>负</p>
        <p class="wdl away"><span>{{$wina}}</span>胜<span>{{$drawa}}</span>平<span>{{$losea}}</span>负</p>
        <p class="percent host">{{$homePer}}%</p>
        <p class="percent away">{{$awayPer}}%</p>
        <div class="line">
            <p class="host" style="width: {{$homePer}}%;"></p>
            <p class="away" style="width: {{$awayPer}}%;"></p>
        </div>
    </div>
    <div class="pbox">
        <?php
        $winh = 0;
        $drawh = 0;
        $loseh = 0;
        $wina = 0;
        $drawa = 0;
        $losea = 0;
        $hid = $match['hid'];
        if (isset($analyse['recentBattle']['home'])){
            foreach($analyse['recentBattle']['home']['all'] as $match){
                if($match['hscore'] > $match['ascore'])
                    if($match['hid'] == $hid)
                        $winh++;
                    else
                        $loseh++;
                elseif($match['hscore'] < $match['ascore'])
                    if($match['hid'] == $hid)
                        $loseh++;
                    else
                        $winh++;
                else
                    $drawh++;
            }
        }
        if (isset($analyse['recentBattle']['away'])){
            foreach($analyse['recentBattle']['away']['all'] as $match){
                if($match['hscore'] > $match['ascore'])
                    if($match['hid'] == $aid)
                        $wina++;
                    else
                        $losea++;
                elseif($match['hscore'] < $match['ascore'])
                    if($match['hid'] == $aid)
                        $losea++;
                    else
                        $wina++;
                else
                    $drawa++;
            }
        }

        $homePer = ($winh +$drawh + $loseh) > 0 ? round(100*($winh/($winh+$loseh+$drawh)),0) : 0;
        $awayPer = ($wina + $drawa + $losea) > 0 ? round(100*($wina/($wina+$drawa+$losea)),0) : 0;

        ?>
        (近期战绩)
        <p class="wdl host"><span>{{$winh}}</span>胜<span>{{$drawh}}</span>平<span>{{$loseh}}</span>负</p>
        <p class="wdl away"><span>{{$wina}}</span>胜<span>{{$drawa}}</span>平<span>{{$losea}}</span>负</p>
        <p class="percent host">{{$homePer}}%</p>
        <p class="percent away">{{$awayPer}}%</p>
        <div class="line">
            <p class="host" style="width: {{($homePer+$awayPer) == 0 ? 0 : (100*$homePer/($homePer+$awayPer))}}%;"></p>
            <p class="away" style="width: {{($homePer+$awayPer) == 0 ? 0 : (100*$awayPer/($homePer+$awayPer))}}%;"></p>
        </div>
    </div>
    <?php
    if (!is_null($match['oumiddle1'])){
        if(!is_null($match['ouup1']) && !is_null($match['oumiddle1']) && !is_null($match['oudown1'])){
            $up = $match['ouup1'] > 0 ? round(90/$match['ouup1'],0) : 0;
            $middle = $match['oumiddle1'] > 0 ? round(90/$match['oumiddle1'],0) : 0;
        }
    }
    ?>
    <div class="cbox host">
        <canvas width="114px" height="114px" value="{{isset($middle)?($middle+$up):0}}" color="#2b9968"></canvas>
        <div class="cover">
            <p><b>{{isset($middle)?($middle + $up):'-'}}%</b></p>
            <p>不败</p>
        </div>
    </div>
    <div class="cbox away">
        <canvas width="114px" height="114px" value="{{isset($middle)?(100 - $middle - $up):0}}" color="#fabd36"></canvas>
        <div class="cover">
            <p><b>{{isset($middle)?(100 - $middle - $up):'-'}}%</b></p>
            <p>客胜</p>
        </div>
    </div>
</div>