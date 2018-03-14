<dl class="{{$className}}" @if(isset($show)&& $show == 0)style="display: none;"@endif>
    <?php
    $tmp = $sortData[$key][$key2];
    ?>
    @for($i = 0 ; $i < min(count($tmp),10) ; $i++)
        <?php
        $item = $tmp[$i];
        ?>
        @if($item == 3)
            <dd result="win"></dd>
        @elseif($item == 1)
            <dd result="draw"></dd>
        @else
            <dd result="lose"></dd>
        @endif
        @endfor
        <dt class="win">主胜</dt>
        <dt class="draw">平局</dt>
        <dt class="lose">主负</dt>
        <svg>
            <defs>
                <linearGradient id="fillColor{{$fill_key}}" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" style="stop-color:rgb(43,153,104); stop-opacity:0.5"/>
                    <stop offset="100%" style="stop-color:rgb(43,153,104); stop-opacity:0"/>
                </linearGradient>
            </defs>
        </svg>
        <svg num="10" style="display: ;">
            <?php
            $tmp = $sortData[$key][$key2];
            $pointStr = '';
            $i = 0;
            for ($i = 0 ; $i < min(10,count($tmp)) ; $i){
                $bj = $tmp[$i];
                if ($i == 0)
                {
                    $pointStr = 25*($i) . ','.($bj == 3?'0':($bj == 1?'60':'120'));
                }
                else{
                    $pointStr = $pointStr . ' ' . 25*($i) . ','.($bj == 3?'0':($bj == 1?'60':'120'));
                }
                $i++;
            }
            $pointStr = $pointStr .' '. $i*25 . ',120 0,120';
            ?>
            @for($i = 0 ; $i < 10 - 1 ; $i++)
                <?php
                if(count($tmp) > ($i + 1)){
                    $item = $tmp[$i];
                    $item2 = $tmp[$i + 1];
                }else{
                    $item2 = null;
                }
                ?>
                @if(isset($item2))
                    <line x1="{{11.1*$i}}%" y1="{{$item == 3 ? 0 :($item == 1?50:100)}}%" x2="{{11.1*($i+1)}}%" y2="{{$item2 == 3 ? 0 :($item2 == 1?50:100)}}%" style="stroke:#2b9968;stroke-width:1px;position:relative;"/>
                @else
                    <line x1="{{11.1*$i}}%" y1="0%" x2="{{11.1*($i+1)}}%" y2="0%" display="none" style="stroke:#a8a8a8;stroke-width:2px;position:relative;"/>
                @endif
            @endfor
            <polygon points="{{$pointStr}}" style="fill:url(#fillColor{{$fill_key}})"/>
        </svg>
        <svg num="5" style="display: none;">
            <?php
            $tmp = $sortData[$key][$key2];
            $pointStr = '';
            $i = 0;
            for ($i = 0 ; $i < min(5,count($tmp)) ; $i){
                $bj = $tmp[$i];
                if ($i == 0)
                {
                    $pointStr = 56*($i) . ','.($bj == 3?'0':($bj == 1?'60':'120'));
                }
                else{
                    $pointStr = $pointStr . ' ' . 56*($i) . ','.($bj == 3?'0':($bj == 1?'60':'120'));
                }
                $i++;
            }
            $pointStr = $pointStr .' '. $i*56 . ',120 0,120';
            ?>
            @for($i = 0 ; $i < 5 - 1 ; $i++)
                <?php
                if(count($tmp) > ($i + 1)){
                    $item = $tmp[$i];
                    $item2 = $tmp[$i + 1];
                }else{
                    $item2 = null;
                }
                ?>
                @if(isset($item2))
                    <line x1="{{25*$i}}%" y1="{{$item == 3 ? 0 :($item == 1?50:100)}}%" x2="{{25*($i+1)}}%" y2="{{$item2 == 3 ? 0 :($item2 == 1?50:100)}}%" style="stroke:#2b9968;stroke-width:1px;position:relative;"/>
                @else
                    <line x1="{{25*$i}}%" y1="0%" x2="{{25*($i+1)}}%" y2="0%" display="none" style="stroke:#a8a8a8;stroke-width:2px;position:relative;"/>
                @endif
            @endfor
            <polygon points="{{$pointStr}}" style="fill:url(#fillColor{{$fill_key}})"/>
        </svg>
</dl>