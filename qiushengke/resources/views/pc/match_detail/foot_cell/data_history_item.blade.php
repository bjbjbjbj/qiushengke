<div class="con {{$key}}" ma="{{$ma}}" ha="{{$ha}}" @if($show == 0) style="display: none;" @endif>
    <div class="svgBox" num="10">
        <dl class="europe start">
            <dd result="win"></dd>
            <dd result="draw"></dd>
            <dd result="lose"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dt class="win">主胜</dt>
            <dt class="draw">平局</dt>
            <dt class="lose">主负</dt>
            <svg>
                <defs>
                    <linearGradient id="fillColor{{$fill_key}}" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" style="stop-color:rgb(43,153,104); stop-opacity:0.5"/>
                        <stop offset="100%" style="stop-color:rgb(43,153,104); stop-opacity:0"/>
                    </linearGradient>
                </defs>
            </svg>
            <svg num="10" style="display: ;">
                <polygon points="0,0 25,60 50,120 75,0 100,0 125,0 150,0 175,0 200,0 225,0 225,120 0,120" style="fill:url(#fillColor{{$fill_key}})"/>
                <line x1="0%" y1="0%" x2="11.1%" y2="50%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="11.1%" y1="50%" x2="22.2%" y2="100%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="22.2%" y1="100%" x2="33.3%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="33.3%" y1="0%" x2="44.4%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="44.4%" y1="0%" x2="55.5%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="55.5%" y1="0%" x2="66.6%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="66.6%" y1="0%" x2="77.7%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="77.7%" y1="0%" x2="88.8%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="88.8%" y1="0%" x2="100%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
            </svg>
            <svg num="5" style="display: none;">
                <polygon points="0,0 56,60 112,120 168,0 224,0 224,120 0,120" style="fill:url(#fillColor{{$fill_key}})"/>
                <line x1="0%" y1="0%" x2="25%" y2="50%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="25%" y1="50%" x2="50%" y2="100%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="50%" y1="100%" x2="75%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="75%" y1="0%" x2="100%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
            </svg>
        </dl>
        <dl class="europe end" style="display: none;">
            <dd result="win"></dd>
            <dd result="draw"></dd>
            <dd result="lose"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dt class="win">主胜</dt>
            <dt class="draw">平局</dt>
            <dt class="lose">主负</dt>
            <svg num="10" style="display: ;">
                <polygon points="0,0 25,60 50,120 75,0 100,0 125,0 150,0 175,0 200,0 225,0 225,120 0,120" style="fill:url(#fillColor{{$fill_key}})"/>
                <line x1="0%" y1="0%" x2="11.1%" y2="50%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="11.1%" y1="50%" x2="22.2%" y2="100%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="22.2%" y1="100%" x2="33.3%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="33.3%" y1="0%" x2="44.4%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="44.4%" y1="0%" x2="55.5%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="55.5%" y1="0%" x2="66.6%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="66.6%" y1="0%" x2="77.7%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="77.7%" y1="0%" x2="88.8%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="88.8%" y1="0%" x2="100%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
            </svg>
            <svg num="5" style="display: none;">
                <polygon points="0,0 56,60 112,120 168,0 224,0 224,120 0,120" style="fill:url(#fillColor{{$fill_key}})"/>
                <line x1="0%" y1="0%" x2="25%" y2="50%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="25%" y1="50%" x2="50%" y2="100%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="50%" y1="100%" x2="75%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="75%" y1="0%" x2="100%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
            </svg>
        </dl>
        <dl class="asia start">
            <dd result="win"></dd>
            <dd result="draw"></dd>
            <dd result="lose"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dt class="win">主赢</dt>
            <dt class="draw">走水</dt>
            <dt class="lose">主输</dt>
            <svg num="10" style="display: ;">
                <polygon points="0,0 25,60 50,120 75,0 100,0 125,0 150,0 175,0 200,0 225,0 225,120 0,120" style="fill:url(#fillColor{{$fill_key}})"/>
                <line x1="0%" y1="0%" x2="11.1%" y2="50%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="11.1%" y1="50%" x2="22.2%" y2="100%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="22.2%" y1="100%" x2="33.3%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="33.3%" y1="0%" x2="44.4%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="44.4%" y1="0%" x2="55.5%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="55.5%" y1="0%" x2="66.6%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="66.6%" y1="0%" x2="77.7%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="77.7%" y1="0%" x2="88.8%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="88.8%" y1="0%" x2="100%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
            </svg>
            <svg num="5" style="display: none;">
                <polygon points="0,0 56,60 112,120 168,0 224,0 224,120 0,120" style="fill:url(#fillColor{{$fill_key}})"/>
                <line x1="0%" y1="0%" x2="25%" y2="50%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="25%" y1="50%" x2="50%" y2="100%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="50%" y1="100%" x2="75%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="75%" y1="0%" x2="100%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
            </svg>
        </dl>
        <dl class="asia end" style="display: none;">
            <dd result="win"></dd>
            <dd result="draw"></dd>
            <dd result="lose"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dt class="win">主赢</dt>
            <dt class="draw">走水</dt>
            <dt class="lose">主输</dt>
            <svg num="10" style="display: ;">
                <polygon points="0,0 25,60 50,120 75,0 100,0 125,0 150,0 175,0 200,0 225,0 225,120 0,120" style="fill:url(#fillColor{{$fill_key}})"/>
                <line x1="0%" y1="0%" x2="11.1%" y2="50%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="11.1%" y1="50%" x2="22.2%" y2="100%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="22.2%" y1="100%" x2="33.3%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="33.3%" y1="0%" x2="44.4%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="44.4%" y1="0%" x2="55.5%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="55.5%" y1="0%" x2="66.6%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="66.6%" y1="0%" x2="77.7%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="77.7%" y1="0%" x2="88.8%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="88.8%" y1="0%" x2="100%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
            </svg>
            <svg num="5" style="display: none;">
                <polygon points="0,0 56,60 112,120 168,0 224,0 224,120 0,120" style="fill:url(#fillColor{{$fill_key}})"/>
                <line x1="0%" y1="0%" x2="25%" y2="50%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="25%" y1="50%" x2="50%" y2="100%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="50%" y1="100%" x2="75%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="75%" y1="0%" x2="100%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
            </svg>
        </dl>
        <dl class="goal start">
            <dd result="win"></dd>
            <dd result="draw"></dd>
            <dd result="lose"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dt class="win">大球</dt>
            <dt class="draw">走水</dt>
            <dt class="lose">小球</dt>
            <svg num="10" style="display: ;">
                <polygon points="0,0 25,60 50,120 75,0 100,0 125,0 150,0 175,0 200,0 225,0 225,120 0,120" style="fill:url(#fillColor{{$fill_key}})"/>
                <line x1="0%" y1="0%" x2="11.1%" y2="50%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="11.1%" y1="50%" x2="22.2%" y2="100%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="22.2%" y1="100%" x2="33.3%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="33.3%" y1="0%" x2="44.4%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="44.4%" y1="0%" x2="55.5%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="55.5%" y1="0%" x2="66.6%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="66.6%" y1="0%" x2="77.7%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="77.7%" y1="0%" x2="88.8%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="88.8%" y1="0%" x2="100%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
            </svg>
            <svg num="5" style="display: none;">
                <polygon points="0,0 56,60 112,120 168,0 224,0 224,120 0,120" style="fill:url(#fillColor{{$fill_key}})"/>
                <line x1="0%" y1="0%" x2="25%" y2="50%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="25%" y1="50%" x2="50%" y2="100%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="50%" y1="100%" x2="75%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="75%" y1="0%" x2="100%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
            </svg>
        </dl>
        <dl class="goal end" style="display: none;">
            <dd result="win"></dd>
            <dd result="draw"></dd>
            <dd result="lose"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dd result="win"></dd>
            <dt class="win">大球</dt>
            <dt class="draw">走水</dt>
            <dt class="lose">小球</dt>
            <svg num="10" style="display: ;">
                <polygon points="0,0 25,60 50,120 75,0 100,0 125,0 150,0 175,0 200,0 225,0 225,120 0,120" style="fill:url(#fillColor{{$fill_key}})"/>
                <line x1="0%" y1="0%" x2="11.1%" y2="50%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="11.1%" y1="50%" x2="22.2%" y2="100%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="22.2%" y1="100%" x2="33.3%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="33.3%" y1="0%" x2="44.4%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="44.4%" y1="0%" x2="55.5%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="55.5%" y1="0%" x2="66.6%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="66.6%" y1="0%" x2="77.7%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="77.7%" y1="0%" x2="88.8%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="88.8%" y1="0%" x2="100%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
            </svg>
            <svg num="5" style="display: none;">
                <polygon points="0,0 56,60 112,120 168,0 224,0 224,120 0,120" style="fill:url(#fillColor{{$fill_key}})"/>
                <line x1="0%" y1="0%" x2="25%" y2="50%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="25%" y1="50%" x2="50%" y2="100%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="50%" y1="100%" x2="75%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
                <line x1="75%" y1="0%" x2="100%" y2="0%" style="stroke:#2b9968;stroke-width:1px;position:relative;"></line>
            </svg>
        </dl>
    </div>
    <table>
        <colgroup>
            <col num="1" width="90px">
            <col num="2" width="70px">
            <col num="3" width="">
            <col num="4" width="50px">
            <col num="3" width="">
            <col num="5" width="4.5%">
            <col num="6" width="4.5%">
            <col num="7" width="4.5%">
            <col num="8" width="4.5%">
            <col num="9" width="100px">
            <col num="10" width="4.5%">
            <col num="11" width="4.5%">
            <col num="12" width="4.5%">
            <col num="13" width="4.5%">
            <col num="14" width="4.7%">
            <col num="15" width="4.7%">
            <col num="16" width="4.7%">
        </colgroup>
        <thead>
        <tr>
            <th rowspan="2">赛事</th>
            <th rowspan="2">日期</th>
            <th rowspan="2">主队</th>
            <th rowspan="2">比分<br/>（半场）</th>
            <th rowspan="2">客队</th>
            <th colspan="3">
                <select class="europe">
                    <option value="start">初盘</option>
                    <option value="end">终盘</option>
                </select>
            </th>
            <th colspan="3">
                <select class="asia">
                    <option value="start">初盘</option>
                    <option value="end">终盘</option>
                </select>
            </th>
            <th colspan="3">
                <select class="goal">
                    <option value="start">初盘</option>
                    <option value="end">终盘</option>
                </select>
            </th>
            <th rowspan="2">胜负</th>
            <th rowspan="2">让球</th>
            <th rowspan="2">大小</th>
        </tr>
        <tr>
            <th class="yellow">胜</th>
            <th class="yellow">平</th>
            <th class="yellow">负</th>
            <th class="green">主赢</th>
            <th class="green">盘口</th>
            <th class="green">主输</th>
            <th class="yellow">大球</th>
            <th class="yellow">盘口</th>
            <th class="yellow">小球</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><p class="line" style="background: #8652de;"></p>亚洲杯</td>
            <td>17.11.08</td>
            <td>大分三神</td>
            <td>2-1<p class="half">(0-0)</p></td>
            <td>马特斯宝</td>
            <td class="europe">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="europe">
                <p class="start">3/3.5</p>
                <p class="end" style="display: none;">3/3.5</p>
            </td>
            <td class="europe">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="asia">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="asia">
                <p class="start">让半球/一球</p>
                <p class="end" style="display: none;">让半球/一球</p>
            </td>
            <td class="asia">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="goal">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="goal">
                <p class="start">2.5/3</p>
                <p class="end" style="display: none;">2.5/3</p>
            </td>
            <td class="goal">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="green">胜</td>
            <td class="asia">
                <p class="start green">赢</p>
                <p class="end green" style="display: none;">赢</p>
            </td>
            <td class="goal">
                <p class="start green">大</p>
                <p class="end green" style="display: none;">大</p>
            </td>
        </tr>
        <tr>
            <td><p class="line" style="background: #8652de;"></p>亚洲杯</td>
            <td>17.11.08</td>
            <td>大分三神</td>
            <td>2-1<p class="half">(0-0)</p></td>
            <td>马特斯宝</td>
            <td class="europe">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="europe">
                <p class="start">3/3.5</p>
                <p class="end" style="display: none;">3/3.5</p>
            </td>
            <td class="europe">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="asia">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="asia">
                <p class="start">让半球/一球</p>
                <p class="end" style="display: none;">让半球/一球</p>
            </td>
            <td class="asia">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="goal">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="goal">
                <p class="start">2.5/3</p>
                <p class="end" style="display: none;">2.5/3</p>
            </td>
            <td class="goal">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="gray">平</td>
            <td class="asia">
                <p class="start gray">走</p>
                <p class="end gray" style="display: none;">走</p>
            </td>
            <td class="goal">
                <p class="start gray">走</p>
                <p class="end gray" style="display: none;">走</p>
            </td>
        </tr>
        <tr>
            <td><p class="line" style="background: #8652de;"></p>亚洲杯</td>
            <td>17.11.08</td>
            <td>大分三神</td>
            <td>2-1<p class="half">(0-0)</p></td>
            <td>马特斯宝</td>
            <td class="europe">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="europe">
                <p class="start">3/3.5</p>
                <p class="end" style="display: none;">3/3.5</p>
            </td>
            <td class="europe">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="asia">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="asia">
                <p class="start">让半球/一球</p>
                <p class="end" style="display: none;">让半球/一球</p>
            </td>
            <td class="asia">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="goal">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="goal">
                <p class="start">2.5/3</p>
                <p class="end" style="display: none;">2.5/3</p>
            </td>
            <td class="goal">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="blue">负</td>
            <td class="asia">
                <p class="start blue">输</p>
                <p class="end blue" style="display: none;">输</p>
            </td>
            <td class="goal">
                <p class="start yellow">小</p>
                <p class="end yellow" style="display: none;">小</p>
            </td>
        </tr>
        <tr>
            <td><p class="line" style="background: #8652de;"></p>亚洲杯</td>
            <td>17.11.08</td>
            <td>大分三神</td>
            <td>2-1<p class="half">(0-0)</p></td>
            <td>马特斯宝</td>
            <td class="europe">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="europe">
                <p class="start">3/3.5</p>
                <p class="end" style="display: none;">3/3.5</p>
            </td>
            <td class="europe">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="asia">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="asia">
                <p class="start">让半球/一球</p>
                <p class="end" style="display: none;">让半球/一球</p>
            </td>
            <td class="asia">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="goal">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="goal">
                <p class="start">2.5/3</p>
                <p class="end" style="display: none;">2.5/3</p>
            </td>
            <td class="goal">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="green">胜</td>
            <td class="asia">
                <p class="start green">赢</p>
                <p class="end green" style="display: none;">赢</p>
            </td>
            <td class="goal">
                <p class="start green">大</p>
                <p class="end green" style="display: none;">大</p>
            </td>
        </tr>
        <tr>
            <td><p class="line" style="background: #8652de;"></p>亚洲杯</td>
            <td>17.11.08</td>
            <td>大分三神</td>
            <td>2-1<p class="half">(0-0)</p></td>
            <td>马特斯宝</td>
            <td class="europe">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="europe">
                <p class="start">3/3.5</p>
                <p class="end" style="display: none;">3/3.5</p>
            </td>
            <td class="europe">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="asia">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="asia">
                <p class="start">让半球/一球</p>
                <p class="end" style="display: none;">让半球/一球</p>
            </td>
            <td class="asia">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="goal">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="goal">
                <p class="start">2.5/3</p>
                <p class="end" style="display: none;">2.5/3</p>
            </td>
            <td class="goal">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="gray">平</td>
            <td class="asia">
                <p class="start gray">走</p>
                <p class="end gray" style="display: none;">走</p>
            </td>
            <td class="goal">
                <p class="start gray">走</p>
                <p class="end gray" style="display: none;">走</p>
            </td>
        </tr>
        <tr>
            <td><p class="line" style="background: #8652de;"></p>亚洲杯</td>
            <td>17.11.08</td>
            <td>大分三神</td>
            <td>2-1<p class="half">(0-0)</p></td>
            <td>马特斯宝</td>
            <td class="europe">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="europe">
                <p class="start">3/3.5</p>
                <p class="end" style="display: none;">3/3.5</p>
            </td>
            <td class="europe">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="asia">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="asia">
                <p class="start">让半球/一球</p>
                <p class="end" style="display: none;">让半球/一球</p>
            </td>
            <td class="asia">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="goal">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="goal">
                <p class="start">2.5/3</p>
                <p class="end" style="display: none;">2.5/3</p>
            </td>
            <td class="goal">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="blue">负</td>
            <td class="asia">
                <p class="start blue">输</p>
                <p class="end blue" style="display: none;">输</p>
            </td>
            <td class="goal">
                <p class="start yellow">小</p>
                <p class="end yellow" style="display: none;">小</p>
            </td>
        </tr>
        <tr>
            <td><p class="line" style="background: #8652de;"></p>亚洲杯</td>
            <td>17.11.08</td>
            <td>大分三神</td>
            <td>2-1<p class="half">(0-0)</p></td>
            <td>马特斯宝</td>
            <td class="europe">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="europe">
                <p class="start">3/3.5</p>
                <p class="end" style="display: none;">3/3.5</p>
            </td>
            <td class="europe">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="asia">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="asia">
                <p class="start">让半球/一球</p>
                <p class="end" style="display: none;">让半球/一球</p>
            </td>
            <td class="asia">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="goal">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="goal">
                <p class="start">2.5/3</p>
                <p class="end" style="display: none;">2.5/3</p>
            </td>
            <td class="goal">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="green">胜</td>
            <td class="asia">
                <p class="start green">赢</p>
                <p class="end green" style="display: none;">赢</p>
            </td>
            <td class="goal">
                <p class="start green">大</p>
                <p class="end green" style="display: none;">大</p>
            </td>
        </tr>
        <tr>
            <td><p class="line" style="background: #8652de;"></p>亚洲杯</td>
            <td>17.11.08</td>
            <td>大分三神</td>
            <td>2-1<p class="half">(0-0)</p></td>
            <td>马特斯宝</td>
            <td class="europe">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="europe">
                <p class="start">3/3.5</p>
                <p class="end" style="display: none;">3/3.5</p>
            </td>
            <td class="europe">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="asia">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="asia">
                <p class="start">让半球/一球</p>
                <p class="end" style="display: none;">让半球/一球</p>
            </td>
            <td class="asia">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="goal">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="goal">
                <p class="start">2.5/3</p>
                <p class="end" style="display: none;">2.5/3</p>
            </td>
            <td class="goal">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="gray">平</td>
            <td class="asia">
                <p class="start gray">走</p>
                <p class="end gray" style="display: none;">走</p>
            </td>
            <td class="goal">
                <p class="start gray">走</p>
                <p class="end gray" style="display: none;">走</p>
            </td>
        </tr>
        <tr>
            <td><p class="line" style="background: #8652de;"></p>亚洲杯</td>
            <td>17.11.08</td>
            <td>大分三神</td>
            <td>2-1<p class="half">(0-0)</p></td>
            <td>马特斯宝</td>
            <td class="europe">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="europe">
                <p class="start">3/3.5</p>
                <p class="end" style="display: none;">3/3.5</p>
            </td>
            <td class="europe">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="asia">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="asia">
                <p class="start">让半球/一球</p>
                <p class="end" style="display: none;">让半球/一球</p>
            </td>
            <td class="asia">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="goal">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="goal">
                <p class="start">2.5/3</p>
                <p class="end" style="display: none;">2.5/3</p>
            </td>
            <td class="goal">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="blue">负</td>
            <td class="asia">
                <p class="start blue">输</p>
                <p class="end blue" style="display: none;">输</p>
            </td>
            <td class="goal">
                <p class="start yellow">小</p>
                <p class="end yellow" style="display: none;">小</p>
            </td>
        </tr>
        <tr>
            <td><p class="line" style="background: #8652de;"></p>亚洲杯</td>
            <td>17.11.08</td>
            <td>大分三神</td>
            <td>2-1<p class="half">(0-0)</p></td>
            <td>马特斯宝</td>
            <td class="europe">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="europe">
                <p class="start">3/3.5</p>
                <p class="end" style="display: none;">3/3.5</p>
            </td>
            <td class="europe">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="asia">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="asia">
                <p class="start">让半球/一球</p>
                <p class="end" style="display: none;">让半球/一球</p>
            </td>
            <td class="asia">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="goal">
                <p class="start">0.81</p>
                <p class="end" style="display: none;">0.81</p>
            </td>
            <td class="goal">
                <p class="start">2.5/3</p>
                <p class="end" style="display: none;">2.5/3</p>
            </td>
            <td class="goal">
                <p class="start">0.99</p>
                <p class="end" style="display: none;">0.99</p>
            </td>
            <td class="green">胜</td>
            <td class="asia">
                <p class="start green">赢</p>
                <p class="end green" style="display: none;">赢</p>
            </td>
            <td class="goal">
                <p class="start green">大</p>
                <p class="end green" style="display: none;">大</p>
            </td>
        </tr>
        </tbody>
    </table>
</div>