<div class="track default">
    <div class="title">
        赛事盘路<button class="close"></button>
    </div>
    @if(isset($home))
    <p class="teamName"><span>{{$match['hname']}}</span></p>
    <table>
        <thead>
        <tr>
            <th>全场</th>
            <th>赢/走/输</th>
            <th>赢盘率</th>
            <th>大球</th>
            <th>大球率</th>
            <th>小球</th>
            <th>小球率</th>
        </tr>
        </thead>
        <tbody>
        @if(isset($home['all']))
            @component('phone.detail.football.cell.data_track_item_cell', ['name'=>'总', 'odd'=>$home['all']]) @endcomponent
        @endif
        @if(isset($home['home']))
            @component('phone.detail.football.cell.data_track_item_cell', ['name'=>'主', 'odd'=>$home['home']]) @endcomponent
        @endif
        @if(isset($home['away']))
            @component('phone.detail.football.cell.data_track_item_cell', ['name'=>'客', 'odd'=>$home['away']]) @endcomponent
        @endif
        @if(isset($home['six']))
        <tr>
            <td>近6</td>
            @if(isset($home['six']['asia']))
            <td colspan="3">
                @foreach($home['six']['asia'] as $sa)
                    @if($sa == 3)
                        <p class="win asia">赢</p>
                    @elseif($sa == 1)
                        <p class="draw asia">走</p>
                    @elseif($sa == 0)
                        <p class="lose asia">输</p>
                    @endif
                @endforeach
            </td>
            @endif
            @if(isset($home['six']['goal']))
            <td colspan="3">
                @foreach($home['six']['goal'] as $sg)
                    @if($sg == 3)
                        <p class="big goal">大</p>
                    @elseif($sg == 1)
                        <p class="draw goal">走</p>
                    @elseif($sg == 0)
                        <p class="small goal">小</p>
                    @endif
                @endforeach
            </td>
            @endif
        </tr>
        @endif
        </tbody>
    </table>
    @endif
    @if(isset($away))
    <p class="teamName"><span>{{$match['aname']}}</span></p>
    <table>
        <thead>
        <tr>
            <th>全场</th>
            <th>赢/走/输</th>
            <th>赢盘率</th>
            <th>大球</th>
            <th>大球率</th>
            <th>小球</th>
            <th>小球率</th>
        </tr>
        </thead>
        <tbody>
        @if(isset($away['all']))
            @component('phone.detail.football.cell.data_track_item_cell', ['name'=>'总', 'odd'=>$away['all']]) @endcomponent
        @endif
        @if(isset($away['home']))
            @component('phone.detail.football.cell.data_track_item_cell', ['name'=>'主', 'odd'=>$away['home']]) @endcomponent
        @endif
        @if(isset($away['away']))
            @component('phone.detail.football.cell.data_track_item_cell', ['name'=>'客', 'odd'=>$away['away']]) @endcomponent
        @endif
        @if(isset($away['six']))
            <tr>
                <td>近6</td>
                @if(isset($away['six']['asia']))
                    <td colspan="3">
                        @foreach($away['six']['asia'] as $sa)
                            @if($sa == 3)
                                <p class="win asia">赢</p>
                            @elseif($sa == 1)
                                <p class="draw asia">走</p>
                            @elseif($sa == 0)
                                <p class="lose asia">输</p>
                            @endif
                        @endforeach
                    </td>
                @endif
                @if(isset($away['six']['goal']))
                    <td colspan="3">
                        @foreach($away['six']['goal'] as $sg)
                            @if($sg == 3)
                                <p class="big goal">大</p>
                            @elseif($sg == 1)
                                <p class="draw goal">走</p>
                            @elseif($sg == 0)
                                <p class="small goal">小</p>
                            @endif
                        @endforeach
                    </td>
                @endif
            </tr>
        @endif
        </tbody>
    </table>
    @endif
</div>