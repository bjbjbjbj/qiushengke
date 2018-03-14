@extends('pc.layout.matchdetail_base')
@section('navContent')
    <div class="home"><p class="abox"><a href="index.html"><img src="/pc/img/logo_image_n.png"></a></p></div>
    <div class="Column">
        <a class="on">足球</a>
        <a href="/match/basket/immediate_t.html">篮球</a>
        <a href="">主播</a>
        <a href="">手机APP</a>
    </div>
    @component('pc.cell.top_leagues',['links'=>$footLeagues])
    @endcomponent
@endsection
@section('content')
    <div id="Con">
        @component('pc.match_detail.foot_cell.head',['match'=>$match,'analyse'=>$analyse,'rank'=>$analyse['rank']])
        @endcomponent
        @component('pc.match_detail.foot_cell.base',['match'=>$match,'rank'=>$analyse['rank'],'tech'=>$tech,'lineup'=>$lineup])
        @endcomponent
        @component('pc.match_detail.foot_cell.character',['match'=>$match,'analyse'=>$analyse])
        @endcomponent
        @component('pc.match_detail.foot_cell.data',['match'=>$match,'analyse'=>$analyse])
        @endcomponent
        @component('pc.match_detail.foot_cell.corner',['match'=>$match,'analyse'=>$analyse])
        @endcomponent
    </div>
    <div id="Play">
        <div class="abox">
            <ul>
                <li class="on" target="Match">比赛赛况</li>
                <li target="Character">特色数据</li>
                <li target="Data">数据分析</li>
                <li target="Corner">角球数据</li>
            </ul>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="/pc/js/match.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            setPage();
        }
    </script>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="/pc/css/match.css">
@endsection