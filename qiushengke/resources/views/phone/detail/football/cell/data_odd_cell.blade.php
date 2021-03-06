<div class="title">
    <select>
        @foreach($odds as $odd)
        <option value="Data_Odd_{{$odd['id']}}">{{$odd['name']}}</option>
        @endforeach
    </select>
    <button class="close"></button>
</div>
@foreach($odds as $index=>$odd)
<table id="Data_Odd_{{$odd['id']}}" style="display: @if($index > 1) none; @endif">
    <thead>
    <tr>
        <th></th>
        <th>初赔</th>
        <th>即赔</th>
    </tr>
    </thead>
    <tbody>
    @if(isset($odd['asia']) && count($odd['asia']) > 0)
        <tr>
            <td>亚盘</td>
            <td>
                <p>{{$odd['asia']['up1']}}</p>
                <p>{{\App\Http\Controllers\PC\CommonTool::getOddMiddleString($odd['asia']['middle1'])}}</p>
                <p>{{$odd['asia']['down1']}}</p>
            </td>
            <td>
                <p class="{{\App\Http\Controllers\PC\CommonTool::colorOfWapUpDown($odd['asia']['up1'], $odd['asia']['up2'])}}">{{$odd['asia']['up2']}}</p>
                <p class="gambling {{\App\Http\Controllers\PC\CommonTool::colorOfWapUpDown($odd['asia']['middle1'], $odd['asia']['middle2'])}}">{{\App\Http\Controllers\PC\CommonTool::getOddMiddleString($odd['asia']['middle2'])}}</p>
                <p class="{{\App\Http\Controllers\PC\CommonTool::colorOfWapUpDown($odd['asia']['down1'], $odd['asia']['down2'])}}">{{$odd['asia']['down2']}}</p>
            </td>
        </tr>
    @else
        <tr>
            <td>亚盘</td>
            <td><p>-</p><p>-</p><p>-</p></td>
            <td><p>-</p><p>-</p><p>-</p></td>
        </tr>
    @endif
    @if(isset($odd['ou']) && count($odd['ou']) > 0)
        <tr>
            <td>欧赔</td>
            <td><p>{{$odd['ou']['up1']}}</p>@if(isset($odd['ou']['middle1']))<p>{{$odd['ou']['middle1']}}</p>@endif<p>{{$odd['ou']['down1']}}</p></td>
            <td>
                <p class="{{\App\Http\Controllers\PC\CommonTool::colorOfWapUpDown($odd['ou']['up1'], $odd['ou']['up2'])}}">{{$odd['ou']['up2']}}</p>
                @if(isset($odd['ou']['middle1']))<p class="gambling {{\App\Http\Controllers\PC\CommonTool::colorOfWapUpDown($odd['ou']['middle1'], $odd['ou']['middle2'])}}">{{$odd['ou']['middle2']}}</p>@endif
                <p class="{{\App\Http\Controllers\PC\CommonTool::colorOfWapUpDown($odd['ou']['down1'], $odd['ou']['down2'])}}">{{$odd['ou']['down2']}}</p>
            </td>
        </tr>
    @else
        <tr>
            <td>欧赔</td>
            <td><p>-</p><p>-</p><p>-</p></td>
            <td><p>-</p><p>-</p><p>-</p></td>
        </tr>
    @endif
    @if(isset($odd['goal']) && count($odd['goal']) > 0)
        <tr>
            <td>大小球</td>
            <td><p>{{$odd['goal']['up1']}}</p><p>{{$odd['goal']['middle1']}}</p><p>{{$odd['goal']['down1']}}</p></td>
            <td>
                <p class="{{\App\Http\Controllers\PC\CommonTool::colorOfWapUpDown($odd['goal']['up1'], $odd['goal']['up2'])}}">{{$odd['goal']['up2']}}</p>
                <p class="gambling {{\App\Http\Controllers\PC\CommonTool::colorOfWapUpDown($odd['goal']['middle1'], $odd['goal']['middle2'])}}">{{$odd['goal']['middle2']}}</p>
                <p class="{{\App\Http\Controllers\PC\CommonTool::colorOfWapUpDown($odd['goal']['down1'], $odd['goal']['down2'])}}">{{$odd['goal']['down2']}}</p>
            </td>
        </tr>
    @else
        <tr>
            <td>大小球</td>
            <td><p>-</p><p>-</p><p>-</p></td>
            <td><p>-</p><p>-</p><p>-</p></td>
        </tr>
    @endif
    </tbody>
</table>
@endforeach