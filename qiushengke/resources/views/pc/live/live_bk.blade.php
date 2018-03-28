@extends('pc.layout.live_base')

@section('live_data')
    <?php
    $hicon = strlen($match['hicon']) > 0 ? $match['hicon'] : '/pc/img/icon_teamDefault.png';
    $aicon = strlen($match['aicon']) > 0 ? $match['aicon'] : '/pc/img/icon_teamDefault.png';
    $isShowScore = $match['status'] > 0 || $match['status'] == -1;
    ?>
    <div class="data">
        <dl>
            <dt>VS</dt>
            <dd class="host">
                @if($isShowScore)
                    @if(isset($match['h_ot']))
                        @foreach($match['h_ot'] as $key=>$ot)
                            <p class="item bk on">{{$ot}}</p>
                        @endforeach
                    @endif
                    <p class="item bk @if(!isset($match['h_ot'])) on @endif">{{isset($match['hscore_4th']) ? $match['hscore_4th'] : "-"}}</p>
                    <p class="item bk @if(!isset($match['hscore_4th'])) on @endif">{{isset($match['hscore_3rd']) ? $match['hscore_3rd'] : "-"}}</p>
                    <p class="item bk @if(!isset($match['hscore_3rd'])) on @endif">{{isset($match['hscore_2nd']) ? $match['hscore_2nd'] : "-"}}</p>
                    <p class="item bk @if(!isset($match['hscore_2nd'])) on @endif">{{isset($match['hscore_1st']) ? $match['hscore_1st'] : "-"}}</p>
                @else
                    <p class="item bk">-</p>
                    <p class="item bk">-</p>
                    <p class="item bk">-</p>
                    <p class="item bk">-</p>
                @endif
                <p class="team"><img src="{{$hicon}}">{{$match['hname']}}</p>
            </dd>
            <dd class="away">
                @if($isShowScore)
                    <p class="item bk @if(!isset($match['hscore_2nd'])) on @endif">{{isset($match['ascore_1st']) ? $match['ascore_1st'] : "-"}}</p>
                    <p class="item bk @if(!isset($match['hscore_3rd'])) on @endif">{{isset($match['ascore_2nd']) ? $match['ascore_2nd'] : "-"}}</p>
                    <p class="item bk @if(!isset($match['hscore_4th'])) on @endif">{{isset($match['ascore_3rd']) ? $match['ascore_3rd'] : "-"}}</p>
                    <p class="item bk @if(!isset($match['h_ot'])) on @endif">{{isset($match['ascore_4th']) ? $match['ascore_4th'] : "-"}}</p>
                    @if(isset($match['a_ot']))
                        @foreach($match['a_ot'] as $key=>$ot)
                            <p class="item bk on">{{$ot}}</p>
                        @endforeach
                    @endif
                @else
                    <p class="item bk">-</p>
                    <p class="item bk">-</p>
                    <p class="item bk">-</p>
                    <p class="item bk">-</p>
                @endif
                <p class="team"><img src="{{$aicon}}">{{$match['aname']}}</p>
            </dd>
        </dl>
        <ul>
            <button class="open"></button>
            @foreach($tech as $t)
                @if((strlen($t['name']) < 10) && ($t['h'] > 0 || $t['a'] > 0))
                    <li>
                        <p class="host">
                            <b>{{$t['h']}}</b>
                            <span><em></em></span>
                        </p>
                        <p class="item">{{$t['name']}}</p>
                        <p class="away">
                            <b>{{$t['a']}}</b>
                            <span><em></em></span>
                        </p>
                    </li>
            @endif
        @endforeach
        <!--li的数量必须是3的倍数，不够使用空li-->
        </ul>
    </div>
@endsection

@section('live_content')
    <div id="Score">
        <p class="title">比分统计</p>
        <div class="con">
            @component('pc.match_detail.basket_cell.match_score_table',['match'=>$match,'isShowScore'=>$isShowScore])
            @endcomponent
        </div>
    </div>
    <div id="BKPlayer">
        <p class="title">球员数据</p>
        <div class="con">
            <div class="tab">
                <p class="host"><button class="on">{{$match['hname']}}</button></p>
                <p class="away"><button>{{$match['aname']}}</button></p>
            </div>
            @if(isset($players['home']))
                @component('pc.match_detail.basket_cell.match_players_table',['players'=>$players['home'],'key'=>'host'])
                @endcomponent
            @endif
            @if(isset($players['away']))
                @component('pc.match_detail.basket_cell.match_players_table',['show'=>0,'players'=>$players['away'],'key'=>'away'])
                @endcomponent
            @endif
        </div>
    </div>
@endsection

@section('live_js')
    <script type="text/javascript">
        //刷新比赛
        function refreshMatch() {
            var mid = '{{$match['mid']}}';
            var first = mid.substr(0,2);
            var second = mid.substr(2,2);
            var url = '/static/terminal/2/'+ first +'/'+ second +'/'+mid+'/match.json';
            url = '/test?url=' + '{{env('MATCH_URL')}}' + url;
            $.ajax({
                'url': url,
                'success': function (json) {
                    //比分
                    if (json['status'] > 0 || json['status'] == -1) {
                        $('p.score span.host').html(json['hscore']);
                        $('p.score span.away').html(json['ascore']);
                        $('div.mbox p.time').html(json['live_time_str']);
                    }
                    if (json['status'] == -1){
                        $('div.mbox p.time').html('已结束');
                    }
                }
            });
        }
        if ('{{$match['status']}}' == -1){

        }
        else{
            window.setInterval('refreshMatch()', 5000);
        }
    </script>
@endsection

@section('navContent')
    @component('pc.layout.nav_content',['type'=>1])
    @endcomponent
@endsection