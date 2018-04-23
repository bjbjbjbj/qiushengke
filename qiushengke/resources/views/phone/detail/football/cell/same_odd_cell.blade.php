<div id="SameOdd" class="content" style="display: @if(!isset($show)) none @endif;">
    @if(isset($sameOdd))
        @component("phone.detail.football.cell.same_odd_item_cell", ['type'=>1, 'odd'=> (isset($sameOdd['asia']) ? $sameOdd['asia'] : null) ]) @endcomponent
        @component("phone.detail.football.cell.same_odd_item_cell", ['type'=>3, 'odd'=> (isset($sameOdd['asia']) ? $sameOdd['ou'] : null) ]) @endcomponent
        @component("phone.detail.football.cell.same_odd_item_cell", ['type'=>2, 'odd'=> (isset($sameOdd['asia']) ? $sameOdd['goal'] : null) ]) @endcomponent
    @endif
    @if(isset($sameOdd['asia']) || isset($sameOdd['ou']) || isset($sameOdd['goal']))
    <div class="bottom">
        <div class="btn">
            @if(isset($sameOdd['asia']))
            <input type="radio" name="SameOdd" id="SameOdd_Asia_Tab" value="SameOdd_Asia" checked>
            <label for="SameOdd_Asia_Tab">亚盘</label>
            @endif
            @if(isset($sameOdd['ou']))
            <input type="radio" name="SameOdd" id="SameOdd_Europe_Tab" value="SameOdd_Europe" @if(!isset($sameOdd['asia'])) checked @endif >
            <label for="SameOdd_Europe_Tab">欧赔</label>
            @endif
            @if(isset($sameOdd['goal']))
            <input type="radio" name="SameOdd" id="SameOdd_Goal_Tab" value="SameOdd_Goal" @if(!isset($sameOdd['asia']) && !isset($sameOdd['ou'])) checked @endif >
            <label for="SameOdd_Goal_Tab">大小球</label>
            @endif
        </div>
    </div>
    @endif
</div>