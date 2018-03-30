@if(isset($ws['home']) || isset($ws['away']))
    <div class="style default">
        <div class="title">球队风格<button class="close"></button></div>
        @if(isset($ws['home']))
        <p class="teamName"><span>{{$match['hname']}}</span></p>
        @component('phone.detail.football.cell.team_trait_style_item_cell', ['items'=>$ws['home']]) @endcomponent
        @endif
        @if(isset($ws['away']))
        <p class="teamName"><span>{{$match['aname']}}</span></p>
            @component('phone.detail.football.cell.team_trait_style_item_cell', ['items'=>$ws['away']]) @endcomponent
        @endif
    </div>
@endif
@if(isset($ws) && isset($ws['case']))
    <div class="prediction default">
        <div class="title">场面预测<button class="close"></button></div>
        <dl>
            @foreach($ws['case'] as $item)
                <?php
                $oc = '';
                $twc = '';
                $thc = '';
                if (3 <= $item['score']) {
                    $oc = 'on';
                } else if (2 == $item['score']){
                    $twc = 'on';
                } else if (1 >= $item['score']) {
                    $thc = 'on';
                }
                ?>
                <dd class="teamStr">
                    <p>{{$item['sentence']}}</p>
                    <div class="part">
                        <p class="{{$oc}}">非常可能</p>
                        <p class="{{$twc}}" >很可能</p>
                        <p class="{{$thc}}">可能</p>
                    </div>
                </dd>
            @endforeach
        </dl>
    </div>
@endif