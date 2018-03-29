@if(isset($items))
    <?php
    $strengthCount = isset($items['strengths']) ? count($items['strengths']) : 0;
    $weaknessCount = isset($items['weaknesses']) ? count($items['weaknesses']) : 0;
    $stylesCount = isset($items['styles']) ? count($items['styles']) : 0;
    ?>
    <dl>
        <dt>强弱项</dt>
        @for($i = 0 ; $i < $strengthCount + $weaknessCount; $i++)
            <?php
            $item = null;
            if ($strengthCount > $i)
                $item = $items['strengths'][$i];
            else if ($weaknessCount > ($i - $strengthCount))
                $item = $items['weaknesses'][$i - $strengthCount];
            ?>
            @if(!is_null($item))
                <dd class="teamStr">
                    <p>{{$item['name']}}</p>
                    <div class="part">
                        <p @if(5 == $item['level']) class="on" @endif >非常强</p>
                        <p @if(4 == $item['level']) class="on" @endif >很强</p>
                        <p @if(3 == $item['level']) class="on" @endif >强</p>
                        <p @if(2 == $item['level']) class="on" @endif >弱</p>
                        <p @if(1 == $item['level']) class="on" @endif >很弱</p>
                    </div>
                </dd>
            @endif
        @endfor
        <dt>风格</dt>
        @if(isset($items['styles']))
            @foreach($items['styles'] as $style)
                <dd class="teamStyle">{{$style['name']}}</dd>
            @endforeach
        @endif
    </dl>
@endif