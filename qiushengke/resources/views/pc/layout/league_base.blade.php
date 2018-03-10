@extends('pc.layout.base')
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
@section('js')
    <script type="text/javascript" src="/pc/js/league.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            setPage();
        }
    </script>
    @yield('league_js')
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="/pc/css/league.css">
@endsection
@section('content')
    <div id="Con">
        <div class="right">
            <div class="rbox" id="Info">
                <p class="title">赛事介绍</p>
                <div class="con">
                    <p class="info"><img src="/pc/img/icon_teamDefault.png">英格兰超级联赛<span>2017-2018赛季</span></p>
                    <p class="text">英格兰足球超级联赛共由20支球队组成，采取双循环赛制（每支球队分别以主、客场身份和其他球队交锋两次）。单场比赛积分计算方法是胜者得3分、负者得0分、平局则双方各得1分，赛季末按累计积分高低排名。积分相同的球队由淨胜球和总进球数等来决定排名，如果争冠球队通过以上条件仍不分上下就需要进行附加赛。</p>
                    <p class="text">联赛前三名直接参加下赛季冠军联赛小组赛，第四名取得参加下赛季冠军联赛外围赛的资格，第五名参加下赛季欧霸杯（英格兰足总盃冠军和联赛盃冠军也参加欧霸杯，如果足总盃冠军已经取得欧战资格，则其名额给足总盃亚军，而如果联赛盃冠军已经取得欧战资格，则其名额给联赛中排名靠前的球队）。另外，本赛季英超联赛规定升3降3，联赛排名榜尾的3支球队下赛季将降到英冠。</p>
                </div>
                <button class="open">查看详情</button>
            </div>
            <div class="rbox" id="Video">
                <p class="title">精彩视频</p>
                <ul>
                    <a class="li" href="">
                        <p class="img"><img src="/pc/img/img_demo.png"></p>
                        <p class="text"><span>奥巴梅扬进球助攻完美首秀</span></p>
                    </a>
                    <a class="li" href="">
                        <p class="img"><img src="/pc/img/img_demo.png"></p>
                        <p class="text"><span>奥巴梅扬进球助攻完美首秀</span></p>
                    </a>
                    <a class="li" href="">
                        <p class="img"><img src="/pc/img/img_demo.png"></p>
                        <p class="text"><span>奥巴梅扬进球助攻完美首秀</span></p>
                    </a>
                    <a class="li" href="">
                        <p class="img"><img src="/pc/img/img_demo.png"></p>
                        <p class="text"><span>奥巴梅扬进球助攻完美首秀</span></p>
                    </a>
                    <a class="li" href="">
                        <p class="img"><img src="/pc/img/img_demo.png"></p>
                        <p class="text"><span>奥巴梅扬进球助攻完美首秀</span></p>
                    </a>
                    <a class="li" href="">
                        <p class="img"><img src="/pc/img/img_demo.png"></p>
                        <p class="text"><span>奥巴梅扬进球助攻完美首秀</span></p>
                    </a>
                    <a class="li" href="">
                        <p class="img"><img src="/pc/img/img_demo.png"></p>
                        <p class="text"><span>奥巴梅扬进球助攻完美首秀</span></p>
                    </a>
                    <a class="li" href="">
                        <p class="img"><img src="/pc/img/img_demo.png"></p>
                        <p class="text"><span>奥巴梅扬进球助攻完美首秀</span></p>
                    </a>
                    <a class="li" href="">
                        <p class="img"><img src="/pc/img/img_demo.png"></p>
                        <p class="text"><span>奥巴梅扬进球助攻完美首秀</span></p>
                    </a>
                    <a class="li" href="">
                        <p class="img"><img src="/pc/img/img_demo.png"></p>
                        <p class="text"><span>奥巴梅扬进球助攻完美首秀</span></p>
                    </a>
                </ul>
            </div>
        </div>
        @yield('league_content')
    </div>
@endsection