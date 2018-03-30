@if(isset($bankers) && isset($odds))

    <div id="Asia" class="asia default childNode" style="display: ;">
        @if(count($odds) > 0)
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
        @endif
        <div class="nolist">暂无数据</div>
    </div>
    <div id="Europe" class="asia default childNode" style="display: none;">
        @if(count($odds) > 0)
            <table>
                <thead>
                <tr>
                    <th>公司</th>
                    <th></th>
                    <th>主胜</th>
                    <th>平局</th>
                    <th>主负</th>
                </tr>
                </thead>
                <tbody>
                @foreach($odds as $index=>$odd)
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
                        <td>
                            <p>{{$odd['ou']['middle1']}}</p>
                            <p
                                    @if($odd['ou']['middle2'] > $odd['ou']['middle1']) class="red" @endif
                            @if($odd['ou']['middle2'] < $odd['ou']['middle1']) class="green" @endif
                            >{{$odd['ou']['middle2']}}</p>
                        </td>
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
        @endif
        <div class="nolist">暂无数据</div>
    </div>
    <div id="Goal" class="asia default childNode" style="display: none;">
        @if(count($odds) > 0)
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
                            <p>{{$odd['goal']['middle1']}}</p>
                            <p
                                    @if($odd['goal']['middle2'] > $odd['goal']['middle1']) class="red" @endif
                            @if($odd['goal']['middle2'] < $odd['goal']['middle1']) class="green" @endif
                            >{{$odd['goal']['middle2']}}</p>
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
            @endif
            <div class="nolist">暂无数据</div>
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