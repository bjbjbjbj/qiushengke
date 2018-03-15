<?php $isShowScore = $match['status'] > 0 || $match['status'] == -1; ?>
<div class="score">
    <p class="title">比分统计</p>
    @component('pc.match_detail.basket_cell.match_score_table',['match'=>$match,'isShowScore'=>$isShowScore])
    @endcomponent
</div>