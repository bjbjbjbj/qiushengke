@if(isset($odds) && count($odds) > 0)
    <div class="odd">
        <p class="title">赔率指数</p>
        {{--<p class="abox"><a href="odd.html">【亚】</a><a href="odd.html">【欧】</a><a href="odd.html">【大】</a></p>--}}
        <table>
            <colgroup>
                <col num="1" width="">
                <col num="2" width="6%">
                <col num="3" width="10%">
                <col num="4" width="10%">
                <col num="5" width="10%">
                <col num="6" width="10%">
                <col num="7" width="10%">
                <col num="8" width="10%">
                <col num="9" width="10%">
                <col num="10" width="10%">
            </colgroup>
            <thead>
            <tr>
                <th colspan="2"></th>
                <th colspan="2">欧洲指数</th>
                <th colspan="3">亚洲指数</th>
                <th colspan="3">大小分指数</th>
            </tr>
            <tr>
                <th>公司</th>
                <th></th>
                <th>主胜</th>
                <th>客胜</th>
                <th>主队</th>
                <th>让分</th>
                <th>客队</th>
                <th>大分</th>
                <th>盘口</th>
                <th>小分</th>
            </tr>
            </thead>
            <tbody>
            @foreach($odds as $odd)
                <tr>
                    <td rowspan="2">{{$odd['name']}}</td>
                    <td>初盘</td>
                    @if(isset($odd['ou']))
                        <td>{{$odd['ou']['up2']}}</td>
                        <td>{{$odd['ou']['down2']}}</td>
                    @else
                        <td>-</td>
                        <td>-</td>
                    @endif
                    @if(isset($odd['asia']))
                        <td>{{$odd['asia']['up1']}}</td>
                        <td>{{$odd['asia']['middle1']}}</td>
                        <td>{{$odd['asia']['down1']}}</td>
                    @else
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    @endif
                    @if(isset($odd['goal']))
                        <td>{{$odd['goal']['up1']}}</td>
                        <td>{{$odd['goal']['middle1']}}</td>
                        <td>{{$odd['goal']['down1']}}</td>
                    @else
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    @endif
                </tr>
                <tr>
                    <td>终盘</td>
                    @if(isset($odd['ou']))
                        <td>{{$odd['ou']['up2']}}</td>
                        <td>{{$odd['ou']['down2']}}</td>
                    @else
                        <td>-</td>
                        <td>-</td>
                    @endif
                    @if(isset($odd['asia']))
                        <td>{{$odd['asia']['up2']}}</td>
                        <td>{{$odd['asia']['middle2']}}</td>
                        <td>{{$odd['asia']['down2']}}</td>
                    @else
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    @endif
                    @if(isset($odd['goal']))
                        <td>{{$odd['goal']['up2']}}</td>
                        <td>{{$odd['goal']['middle2']}}</td>
                        <td>{{$odd['goal']['down2']}}</td>
                    @else
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif