<div class="part">
    @if($up != 1)
        <?php
        $scoreStr = '';
        if (isset($matches)){
            if(count($matches) > 0 && $matches[0]['status'] == -1)
                $scoreStr = $matches[0]['hscore'].'-'.$matches[0]['ascore'];
            if(count($matches) > 1 && $matches[1]['status'] == -1)
                $scoreStr = $scoreStr . '<br/>' .$matches[1]['hscore'].'-'.$matches[1]['ascore'];
        }
        ?>
        <p class="advanced">
            @if(!isset($score) || $score == 0)
                {!! $scoreStr !!}
            @endif
        </p>
        @if(isset($score) && $score == 1)
            <p class="score">
                {!! $scoreStr !!}
            </p>
        @endif
    @endif
    @if(isset($matches))
        <?php
        $hicon = $matches[0]['hicon'];
        $aicon = $matches[0]['aicon'];
        ?>
        <p class="team"><img src="{{$hicon}}" onerror="{{$cdn}}/phone/img/icon_teamDefault.png">{{$item['hname']}}</p>
        <p class="team"><img src="{{$aicon}}" onerror="{{$cdn}}/phone/img/icon_teamDefault.png">{{$item['aname']}}</p>
    @else
        <p class="team"><img src="{{$cdn}}/phone/img/icon_teamDefault.png">-</p>
        <p class="team"><img src="{{$cdn}}/phone/img/icon_teamDefault.png">-</p>
    @endif
    @if($up == 1)
        <?php
        $scoreStr = '';
        if (isset($matches)){
            if(count($matches) > 0 && $matches[0]['status'] == -1)
                $scoreStr = $matches[0]['hscore'].'-'.$matches[0]['ascore'];
            if(count($matches) > 1 && $matches[1]['status'] == -1)
                $scoreStr = $scoreStr . '<br/>' .$matches[1]['hscore'].'-'.$matches[1]['ascore'];
        }
        ?>
        <p class="advanced">
            @if(!isset($score) || $score == 0)
                {!! $scoreStr !!}
            @endif
        </p>
        @if(isset($score) && $score == 1)
            <p class="score">
                {!! $scoreStr !!}
            </p>
        @endif
    @endif
</div>