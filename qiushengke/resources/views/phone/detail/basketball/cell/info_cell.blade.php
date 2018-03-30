<div id="Navigation">
    <div class="banner">
        <!-- <a href="index.html" class="home"></a> -->
        @if($match['live'])
            <a class="team" href="video.html" style="opacity: 0;"><!--有直播就用a标签，如果没有就用div-->
                <p class="host">{{$match['hname']}}</p>
                <p class="score">@if($match['status'] == 0 || $match['status'] < -1) VS @else {{$match['hscore'].'-'.$match['ascore']}} @endif<span>[直播]</span></p><!--有直播就用加span-->
                <p class="away">{{$match['aname']}}</p>
            </a>
        @else
            <div class="team" style="opacity: 0;"><!--有直播就用a标签，如果没有就用div-->
                <p class="host">{{$match['hname']}}</p>
                <p class="score">@if($match['status'] == 0 || $match['status'] < -1) VS @else {{$match['hscore'].'-'.$match['ascore']}} @endif</p>
                <p class="away">{{$match['aname']}}</p>
            </div>
        @endif
        {{$match['league']}}
    </div>
</div>
<div id="Info">
    <div class="team">
        <p class="img"><img src="{{$match['hicon']}}" onerror="this.src='{{$cdn}}/pc/img/icon_teamDefault.png'"></p>
        <p class="name">{{$match['hname']}}</p>
        <!--@if(!empty($match['hrank']))<p class="rank">排名：{{$match['hrank']}}</p>@endif -->
    </div>
    <div class="info">
        <p class="minute">{{$match['live_time_str']}}</p>
        <p class="score">
            @if($match["status"] > 0 || $match["status"] == -1)
                <span class="host">{{$match['hscore']}}</span>
                <span class="away">{{$match['ascore']}}</span>
            @endif
        </p>
        @if($match['live'])<a href="video.html" class="live">正在直播</a>@endif
    </div>
    <div class="team">
        <p class="img"><img src="{{$match['aicon']}}" onerror="this.src='{{$cdn}}/pc/img/icon_teamDefault.png'"></p>
        <p class="name">{{$match['aname']}}</p>
        <!--@if(!empty($match['arank']))<p class="rank">排名：{{$match['arank']}}</p>@endif -->
    </div>
</div>