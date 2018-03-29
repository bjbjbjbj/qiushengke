<dl id="Bottom">
    @if($index == 0)
        <dd class="on"><a><img src="{{$cdn}}/phone/img/tab_0_seleted.png"><p>足球</p></a></dd>
    @else
        <dd><a href="/wap/match/foot/schedule/immediate.html"><img src="{{$cdn}}/phone/img/tab_0_normal.png"><p>足球</p></a></dd>
    @endif
    @if($index == 1)
        <dd class="on"><a><img src="{{$cdn}}/phone/img/tab_2_seleted.png"><p>篮球</p></a></dd>
    @else
        <dd><a href="/wap/match/basket/schedule/immediate_t.html"><img src="{{$cdn}}/phone/img/tab_2_normal.png"><p>篮球</p></a></dd>
    @endif
    @if($index == 2)
        <dd class="on"><a><img src="{{$cdn}}/phone/img/tab_1_seleted.png"><p>主播</p></a></dd>
    @else
        <dd><a href="anchor.html"><img src="{{$cdn}}/phone/img/tab_1_normal.png"><p>主播</p></a></dd>
    @endif
</dl>