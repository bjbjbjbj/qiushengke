<div class="team {{$class}}">
    @if(isset($lineup['h_lineup_per']))
        <p class="number">本场比赛有<b>{{number_format($lineup['h_lineup_per']*0.01*11,0)}}</b>名主力首发</p>
        <?php
        $width = 160*round($lineup['h_lineup_per'],2);
        ?>
        <p class="percent">{{round($lineup['h_lineup_per'],2)}}%<span style="width: '{{$width}}'px"></span></p><!--span的值为160*百分比-->
    @endif
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