<?php
$hasTech = false;
if (isset($tech) && isset($tech['tech'])){
    $tech = $tech['tech'];
    foreach($tech as $t)
        if($t['h'] > 0 || $t['a'] > 0)
            $hasTech = true;
}
?>
@if($hasTech)
    <div class="total">
        <p class="title">技术统计</p>
        <ul>
            @foreach($tech as $t)
                @if($t['h'] > 0 || $t['a'] > 0)
                    <li>
                        {{$t['name']}}
                        <div class="line host">
                            <p>{{$t['h_p']*100}}%</p>
                            <span style="width: {{$t['h_p']*100}}%;"></span>
                        </div>
                        <div class="line away">
                            <p>{{$t['a_p']*100}}%</p>
                            <span style="width: {{$t['a_p']*100}}%;"></span>
                        </div>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
@endif