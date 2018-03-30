@extends('phone.layout.live_base')
@section('live_tab')
    <div class="tab">
        <input type="radio" name="tab" id="Tab_Info" value="Info" checked><label for="Tab_Info">比赛信息</label>
        <input type="radio" name="tab" id="Tab_Event" value="Event"><label for="Tab_Event">比赛事件</label>
    </div>
    <div id="Info">
        <div class="info">
            <div class="team">
                <div class="imgbox"><img src="{{$match['hicon']}}" onerror="this.src='{{$cdn}}/pc/img/icon_teamDefault.png'"></div>
                <p>{{$match['hname']}}</p>
            </div>
            @if($match['status'] == 0)
                <div class="score"><p>vs</p></div>
            @else
                <div class="score"><p>{{$match['hscore']}} - {{$match['ascore']}}</p><button onclick="clickHideScore(this)">隐藏比分</button></div>
            @endif
            <div class="team">
                <div class="imgbox"><img src="{{$match['aicon']}}" onerror="this.src='{{$cdn}}/pc/img/icon_teamDefault.png'"></div>
                <p>{{$match['aname']}}</p>
            </div>
        </div>
        <ul>
            @foreach($tech as $item)
                @if((isset($item['h_p']) && $item['h_p'] != 0) || (isset($item['a_p']) && $item['a_p'] != 0))
                    <?php
                    $hname = empty($item['h']) ? 0 : $item['h'];
                    if (str_contains($hname, "(")) {
                        $hname = explode("(",$hname)[0];
                    }
                    $aname = empty($item['a']) ? 0 : $item['a'];
                    if (str_contains($aname, "(")) {
                        $aname = explode("(",$aname)[0];
                    }
                    ?>
                    <li>
                        <p class="val">{{$hname}}</p>
                        <p class="line"><span style="width: {{108 * $item['h_p']}}%;"></span></p>
                        <p class="item">{{$item['name']}}</p>
                        <p class="line"><span style="width: {{108 * $item['a_p']}}%;"></span></p>
                        <p class="val">{{$aname}}</p>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
@endsection