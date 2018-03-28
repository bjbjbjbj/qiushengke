@extends('pc.layout.base')
@section('navContent')
    @component('pc.layout.nav_content',['type'=>2])
    @endcomponent
@endsection
@section('content')
    <div id="Con">
        <div class="right">
            <div class="rbox" id="Match">
                <p class="title">赛事推荐</p>
                <div class="tabLine">
                    <button class="on" value="0">全部</button>
                    @foreach($leagues as $key=>$item)
                        <button value="{{'l_'.$item['id']}}">{{$item['name']}}</button>
                    @endforeach
                </div>
                <ul id="0">
                    @foreach($matches as $match)
                        <?php
                        $liveUrl = \App\Http\Controllers\PC\CommonTool::matchLivePathWithId($match['mid'],$match['sport']);
                        ?>
                        <li>
                            <p class="time"><span>{{$match['lname']}}{{$match['round'] > 0 ? '第'.$match['round'].'轮':''}}</span>{{date('m.d H:i',$match['time'])}}</p>
                            <div class="team">
                                @if($match['status'] > 0)
                                    <a href="video.html">直播中</a>
                                @else
                                    <a href="video.html">未开始</a>
                                @endif
                                <?php
                                $hicon = strlen($match['h_icon']) > 0 ? $match['h_icon'] :'/pc/img/icon_teamDefault.png';
                                $aicon = strlen($match['a_icon']) > 0 ? $match['a_icon'] :'/pc/img/icon_teamDefault.png';
                                ?>
                                <p class="host"><img src="{{$hicon}}"><span>{{$match['hname']}}</span></p>
                                <p class="away"><img src="{{$aicon}}"><span>{{$match['aname']}}</span></p>
                            </div>
                            <p class="anchor">
                                @foreach($match['anchors'] as $anchor)
                                    <a href="{{$liveUrl}}" target="_blank">{{$anchor['name']}}</a>
                                @endforeach
                            </p>
                        </li>
                    @endforeach
                </ul>
                @foreach($leagues as $key=>$item)
                    <ul id="{{'l_'.$item['id']}}">
                        @foreach($item['matches'] as $match)
                            <?php
                            $liveUrl = \App\Http\Controllers\PC\CommonTool::matchLivePathWithId($match['mid'],$match['sport']);
                            ?>
                            <li>
                                <p class="time"><span>{{$match['lname']}}{{$match['round'] > 0 ? '第'.$match['round'].'轮':''}}</span>{{date('m.d H:i',$match['time'])}}</p>
                                <div class="team">
                                    @if($match['status'] > 0)
                                        <a href="{{$liveUrl}}" target="_blank">直播中</a>
                                    @else
                                        <a href="{{$liveUrl}}" target="_blank">未开始</a>
                                    @endif
                                    <?php
                                        $hicon = strlen($match['h_icon']) > 0 ? $match['h_icon'] :'/pc/img/icon_teamDefault.png';
                                        $aicon = strlen($match['a_icon']) > 0 ? $match['a_icon'] :'/pc/img/icon_teamDefault.png';
                                        ?>
                                    <p class="host"><img src="{{$hicon}}"><span>{{$match['hname']}}</span></p>
                                    <p class="away"><img src="{{$aicon}}"><span>{{$match['aname']}}</span></p>
                                </div>
                                <p class="anchor">
                                    @foreach($match['anchors'] as $anchor)
                                        <a href="video.html">{{$anchor['name']}}</a>
                                    @endforeach
                                </p>
                            </li>
                            @endforeach
                    </ul>
                @endforeach
            </div>
        </div>
        @if(isset($anchors) && count($anchors) > 0)
            <div class="lbox" id="Live">
                <div class="title"><p>正在直播</p></div>
                <ul>
                    @foreach($livings as $living)
                        <?php
                        $anchor = $living->anchor;
                        ?>
                        <a class="li" target="_blank" href="{{$living['link']}}">
                            <div class="img"><img src="{{isset($living['cover']) ? $living['cover'] : '/pc/img/img_demo.png'}}"></div>
                            <img alt="{{$anchor['name']}}" src="{{isset($anchor['icon'])?$anchor['icon']:'/pc/img/icon_teamDefault.png'}}" class="face">
                            <p>{{$anchor['name']}}</p>
                        </a>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(isset($anchors) && count($anchors) > 0)
            <div class="lbox" id="Anchor">
                <div class="title"><p>主播推荐</p></div>
                <ul>
                    @foreach($anchors as $anchor)
                        <a class="li">
                            <img src="{{$anchor['icon']}}">
                            <p><span>{{$anchor['name']}}</span></p>
                        </a>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    <div id="Totop">
        <div class="abox">
            <a class="totop" href="javascript:void(0)"></a>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="/pc/css/anchor.css">
@endsection

@section('js')
    <script type="text/javascript" src="/pc/js/anchor.js"></script>
    <script type="text/javascript">
        window.onload = function () {
             setPage();
        }
    </script>
@endsection