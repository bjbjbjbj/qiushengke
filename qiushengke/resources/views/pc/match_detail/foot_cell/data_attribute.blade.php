<div class="attack">
    <p class="title">攻防能力</p>
    <?php
    $attribute = $analyse['attribute'];
    ?>
    @if(0&&array_key_exists('matches',$attribute['home']['all']))
        <div class="tabBox">
            <button class="on" value="10">近10场</button><button value="5">近5场</button>

        </div>
    @endif
    @component('pc.match_detail.foot_cell.data_attribute_item',['key'=>'10','show'=>1,'match'=>$match,'attribute'=>$attribute])
    @endcomponent
    @if(0&&array_key_exists('matches',$attribute['home']['all']))
        @component('pc.match_detail.foot_cell.data_attribute_item',['key'=>'5','show'=>0,'match'=>$match,'attribute'=>$attribute,'matches'=>1])
        @endcomponent
    @endif
</div>