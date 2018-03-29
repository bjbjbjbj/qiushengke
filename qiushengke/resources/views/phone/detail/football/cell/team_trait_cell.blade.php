<div id="Trait" class="childNode" style="display: ;">
    @if(isset($attribute))
    <div class="strength default" ha="0" le="0">
        <div class="title">
            攻防能力<button class="close"></button>
            <div class="labelbox">
                <label for="Strength_HA"><input type="checkbox" name="battle" value="ha" id="Strength_HA"><span></span>同主客</label>
                <label for="Strength_LE"><input type="checkbox" name="battle" value="le" id="Strength_LE"><span></span>同赛事</label>
            </div>
        </div>
        {{-- 全部 0 0， 同主客 1 0， 同赛事 0 1，同主客 同赛事 1 1 --}}
        @component('phone.detail.football.cell.team_trait_strength_cell',
            ['match'=>$match, 'ha'=>0, 'le'=>0
            , 'home'=>isset($attribute['home']['all']) ? $attribute['home']['all'] : null
            , 'away'=>isset($attribute['away']['all']) ? $attribute['away']['all'] : null ]) @endcomponent
        @component('phone.detail.football.cell.team_trait_strength_cell',
            ['match'=>$match, 'ha'=>1, 'le'=>0
            , 'home'=>isset($attribute['home']['host']) ? $attribute['home']['host'] : null
            , 'away'=>isset($attribute['away']['host']) ? $attribute['away']['host'] : null ]) @endcomponent
        @component('phone.detail.football.cell.team_trait_strength_cell',
            ['match'=>$match, 'ha'=>0, 'le'=>1
            , 'home'=>isset($attribute['home']['league']) ? $attribute['home']['league'] : null
            , 'away'=>isset($attribute['away']['league']) ? $attribute['away']['league'] : null ]) @endcomponent
        @component('phone.detail.football.cell.team_trait_strength_cell',
            ['match'=>$match, 'ha'=>1, 'le'=>1
            , 'home'=>isset($attribute['home']['both']) ? $attribute['home']['both'] : null
            , 'away'=>isset($attribute['away']['both']) ? $attribute['away']['both'] : null ]) @endcomponent
    </div>
    @endif
    @if(isset($ws))
    @component('phone.detail.football.cell.team_trait_style_cell', [
        'match'=>$match, 'ws'=>$ws
    ]) @endcomponent
    @endif
</div>