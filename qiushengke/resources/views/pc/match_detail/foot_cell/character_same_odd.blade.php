<?php
$sameOdd = $analyse['sameOdd'];
?>
<div class="sameOdd">
    <p class="title">历史同赔</p>
    @if(isset($sameOdd) && (isset($sameOdd['asia']) || isset($sameOdd['goal']) || isset($sameOdd['ou'])))
        <div class="tabBox">
            <?php
            $index = 0;
            if (!array_key_exists('asia',$sameOdd)){
                $index = 1;
                if (!array_key_exists('goal',$sameOdd)){
                    $index = 2;
                }
            }
            $html = '';
            foreach($sameOdd as $key=>$odds){
                if($key == 'asia')
                    $html = '<button class="on" value="asia">亚盘</button>';
                if($key == 'goal')
                    if($index == 1)
                        $html = $html .'<button class="on" value="goal">大小</button>';
                    else
                        $html = $html .'<button value="goal">大小</button>';
                if($key == 'ou')
                    if($index == 2)
                        $html = $html .'<button class="on" value="europe">欧盘</button>';
                    else
                        $html = $html .'<button value="europe">欧盘</button>';
            }
            ?>
            {!! $html !!}
        </div>
        @foreach($sameOdd as $key=>$odds)
            <?php
            $show = false;
            if ($key == 'asia' && $index == 0)
                $show = true;
            else if ($key == 'goal' && $index == 1)
                $show = true;
            else if ($key == 'ou' && $index == 2)
                $show = true;
            ?>
            @component('pc.match_detail.foot_cell.character_sameodd',['show'=>$show,'key'=>$key,'odds'=>$odds])
            @endcomponent
        @endforeach
    @endif
</div>