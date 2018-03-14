@if(isset($tech))
    <div class="total">
        <p class="title">技术统计</p>
        <ul>
            @foreach($tech as $item)
                <?php
                    $hname = $item['h'];
                    $aname = $item['a'];
                    if (str_contains($hname, "(")) {
                        $hname = str_replace(')','',explode("(", $hname)[1]);
                    }
                    if (str_contains($aname, "(")) {
                        $aname = str_replace(')','',explode("(", $aname)[1]);
                    }
                ?>
                <li>
                    {{$item['name']}}
                    <div class="line host">
                        <p>{{$hname}}</p>
                        <span style="width: {{$item['h_p']*100}}%;"></span>
                    </div>
                    <div class="line away">
                        <p>{{$aname}}</p>
                        <span style="width: {{$item['a_p']*100}}%;"></span>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endif