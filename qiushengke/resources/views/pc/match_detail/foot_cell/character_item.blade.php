<div class="part {{$key}}">
    <p class="name">{{$tname}}
        @if($rank > 0)
            <span>[{{$rankLeague}}{{$rank}}]</span>
        @endif
    </p>
    <?php
    $strengthCount = isset($items['strengths']) ? count($items['strengths']) : 0;
    $weaknessCount = isset($items['weaknesses']) ? count($items['weaknesses']) : 0;
    $stylesCount = isset($items['styles']) ? count($items['styles']) : 0;
    ?>
    @if($strengthCount > 0 || $weaknessCount > 0)
        <p class="item">强/弱项：</p>
        <ul>
            @for($i = 0 ; $i < ($strengthCount + $weaknessCount); $i++)
                <?php
                $item = null;
                $style = null;
                if ($strengthCount > $i)
                    $item = $items['strengths'][$i];
                else if ($weaknessCount > ($i - $strengthCount))
                    $item = $items['weaknesses'][$i - $strengthCount];
                if ($stylesCount > $i){
                    $style = $items['styles'][$i];
                }
                ?>
                @if(!is_null($item))
                    <li>
                        {{$item['name']}}
                        <p class="level" level="{{$item['level']}}">
                            <span level="1"></span><span level="2"></span><span level="3"></span><span level="4"></span><span level="5"></span>
                        </p>
                    </li>
                @endif
            @endfor
            @for($i = 0 ; $i < ($count - ($strengthCount + $weaknessCount)) ; $i++)
                <li></li>
            @endfor
        </ul>
    @endif
    @if($stylesCount > 0)
        <p class="item">球队风格：</p>
        <ul class="style">
            @foreach($items['styles'] as $item)
                <li>{{$item['name']}}</li>
            @endforeach
                @for($i = 0 ; $i < ($scount - $stylesCount) ; $i++)
                    <li></li>
                @endfor
        </ul>
    @endif
</div>