<div class="team {{$class}}">
    <p class="number">本场比赛有<b>6</b>名主力首发</p>
    <p class="percent">81.82%<span style="width: 130.91px"></span></p><!--span的值为160*百分比-->
    <ul>
        @if(isset($lineup['first']))
            @foreach($lineup['first'] as $first)
                <li>
                    <p class="name">{{$first['name']}}</p>
                    @if($first['first'])<p class="main">【 主 】</p>@endif
                    <p class="jerseys">{{$first['num']}}</p>
                </li>
            @endforeach
        @endif
        @if(isset($lineup['back']))
            @foreach($lineup['back'] as $back)
                <li>
                    <p class="name">{{$back['name']}}</p>
                    @if($first['first'])<p class="main">【 主 】</p>@endif
                    <p class="jerseys reserve">{{$back['num']}}</p>
                </li>
            @endforeach
        @endif
    </ul>
</div>