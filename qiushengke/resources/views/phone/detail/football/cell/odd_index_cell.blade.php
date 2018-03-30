@if(isset($odds))
    <div id="Asia" class="asia default childNode" style="display: ;">
        <table>
            <thead>
            <tr>
                <th>公司</th>
                <th></th>
                <th>主队</th>
                <th>让球</th>
                <th>客队</th>
            </tr>
            </thead>
            <tbody>
            @foreach($odds as $index=>$odd)
                @if(!isset($odd['asia'])) @continue @endif
                <tr>
                    <td>{{$odd['name']}}</td>
                    <td>
                        <p class="gray">初</p>
                        <p class="gray">即</p>
                    </td>
                    <td>
                        <p>{{$odd['asia']['up1']}}</p>
                        <p
                                @if($odd['asia']['up2'] > $odd['asia']['up1']) class="red" @endif
                        @if($odd['asia']['up2'] < $odd['asia']['up1']) class="green" @endif
                        >{{$odd['asia']['up2']}}</p>
                    </td>
                    <td>
                        <p>{{$odd['asia']['middle1']}}</p>
                        <p
                                @if($odd['asia']['middle2'] > $odd['asia']['middle1']) class="red" @endif
                        @if($odd['asia']['middle2'] < $odd['asia']['middle1']) class="green" @endif
                        >{{$odd['asia']['middle2']}}</p>
                    </td>
                    <td>
                        <p>{{$odd['asia']['down1']}}</p>
                        <p
                                @if($odd['asia']['down2'] > $odd['asia']['down1']) class="red" @endif
                        @if($odd['asia']['down2'] < $odd['asia']['down1']) class="green" @endif
                        >{{$odd['asia']['down2']}}</p>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div id="Europe" class="asia default childNode" style="display: none;">
        <table>
            <thead>
            <tr>
                <th>公司</th>
                <th></th>
                <th>主胜</th>
                @if(!isset($isBasket))
                    <th>平局</th>
                @endif
                <th>主负</th>
            </tr>
            </thead>
            <tbody>
            @foreach($odds as $index=>$odd)
                @if(!isset($odd['ou'])) @continue @endif
                <tr>
                    <td>{{$odd['name']}}</td>
                    <td>
                        <p class="gray">初</p>
                        <p class="gray">即</p>
                    </td>
                    <td>
                        <p>{{$odd['ou']['up1']}}</p>
                        <p
                                @if($odd['ou']['up2'] > $odd['ou']['up1']) class="red" @endif
                        @if($odd['ou']['up2'] < $odd['ou']['up1']) class="green" @endif
                        >{{$odd['ou']['up2']}}</p>
                    </td>
                    @if(!isset($isBasket))
                        <td>
                            <p>{{number_format($odd['ou']['middle1'],2)}}</p>
                            <p
                                    @if($odd['ou']['middle2'] > $odd['ou']['middle1']) class="red" @endif
                            @if($odd['ou']['middle2'] < $odd['ou']['middle1']) class="green" @endif
                            >{{number_format($odd['ou']['middle2'],2)}}</p>
                        </td>
                    @endif
                    <td>
                        <p>{{$odd['ou']['down1']}}</p>
                        <p
                                @if($odd['ou']['down2'] > $odd['ou']['down1']) class="red" @endif
                        @if($odd['ou']['down2'] < $odd['ou']['down1']) class="green" @endif
                        >{{$odd['ou']['down2']}}</p>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div id="Goal" class="asia default childNode" style="display: none;">
        <table>
            <thead>
            <tr>
                <th>公司</th>
                <th></th>
                <th>大球</th>
                <th>盘口</th>
                <th>小球</th>
            </tr>
            </thead>
            <tbody>
            @foreach($odds as $index=>$odd)
                @if(!isset($odd['goal'])) @continue @endif
                <tr>
                    <td>{{$odd['name']}}</td>
                    <td>
                        <p class="gray">初</p>
                        <p class="gray">即</p>
                    </td>
                    <td>
                        <p>{{$odd['goal']['up1']}}</p>
                        <p
                                @if($odd['goal']['up2'] > $odd['goal']['up1']) class="red" @endif
                        @if($odd['goal']['up2'] < $odd['goal']['up1']) class="green" @endif
                        >{{$odd['goal']['up2']}}</p>
                    </td>
                    <td>
                        <p>{{\App\Http\Controllers\PC\CommonTool::getOddMiddleString($odd['goal']['middle1'])}}</p>
                        <p
                                @if($odd['goal']['middle2'] > $odd['goal']['middle1']) class="red" @endif
                        @if($odd['goal']['middle2'] < $odd['goal']['middle1']) class="green" @endif
                        >{{\App\Http\Controllers\PC\CommonTool::getOddMiddleString($odd['goal']['middle2'])}}</p>
                    </td>
                    <td>
                        <p>{{$odd['goal']['down1']}}</p>
                        <p
                                @if($odd['goal']['down2'] > $odd['goal']['down1']) class="red" @endif
                        @if($odd['goal']['down2'] < $odd['goal']['down1']) class="green" @endif
                        >{{$odd['goal']['down2']}}</p>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="bottom">
        <div class="btn">
            <input type="radio" name="Odd" id="Odd_Asia" value="Asia" checked>
            <label for="Odd_Asia">亚盘</label>
            <input type="radio" name="Odd" id="Odd_Europe" value="Europe">
            <label for="Odd_Europe">欧赔</label>
            <input type="radio" name="Odd" id="Odd_Goal" value="Goal">
            <label for="Odd_Goal">大小球</label>
            {{--<input type="radio" name="Odd" id="Odd_Kaili" value="Kaili">--}}
            {{--<label for="Odd_Kaili">凯利</label>--}}
        </div>
    </div>
@endif