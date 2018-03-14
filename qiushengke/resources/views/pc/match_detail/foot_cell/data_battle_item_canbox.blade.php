<div class="canBox" num="{{$key}}" @if(isset($show) && $show == 0) style="display: none" @endif>
    <dl class="europe start">
        <dt><canvas width="90px" height="90p" win="{{$sortData['result']['1']['win'.$key.'_p']}}" draw="{{$sortData['result']['1']['draw'.$key.'_p']}}" lose="{{$sortData['result']['1']['lose'.$key.'_p']}}"></canvas></dt>
        <dd class="win">主胜：{{$sortData['result']['1']['win'.$key.'_p']}}%</dd>
        <dd class="draw">平局：{{$sortData['result']['1']['draw'.$key.'_p']}}%</dd>
        <dd class="lose">主负：{{$sortData['result']['1']['lose'.$key.'_p']}}%</dd>
    </dl>
    <dl class="europe end" style="display: none;">
        <dt><canvas width="90px" height="90p" win="{{$sortData['result']['2']['win'.$key.'_p']}}" draw="{{$sortData['result']['2']['draw'.$key.'_p']}}" lose="{{$sortData['result']['2']['lose'.$key.'_p']}}"></canvas></dt>
        <dd class="win">主胜：{{$sortData['result']['2']['win'.$key.'_p']}}%</dd>
        <dd class="draw">平局：{{$sortData['result']['2']['draw'.$key.'_p']}}%</dd>
        <dd class="lose">主负：{{$sortData['result']['2']['lose'.$key.'_p']}}%</dd>
    </dl>
    <dl class="asia start">
        <dt><canvas width="90px" height="90p" win="{{$sortData['asia']['1']['win'.$key.'_p']}}" draw="{{$sortData['asia']['1']['draw'.$key.'_p']}}" lose="{{$sortData['asia']['1']['lose'.$key.'_p']}}"></canvas></dt>
        <dd class="win">主赢：{{$sortData['asia']['1']['win'.$key.'_p']}}%</dd>
        <dd class="draw">走水：{{$sortData['asia']['1']['draw'.$key.'_p']}}%</dd>
        <dd class="lose">主输：{{$sortData['asia']['1']['lose'.$key.'_p']}}%</dd>
    </dl>
    <dl class="asia end" style="display: none;">
        <dt><canvas width="90px" height="90p" win="{{$sortData['asia']['2']['win'.$key.'_p']}}" draw="{{$sortData['asia']['2']['draw'.$key.'_p']}}" lose="{{$sortData['asia']['2']['lose'.$key.'_p']}}"></canvas></dt>
        <dd class="win">主赢：{{$sortData['asia']['2']['win'.$key.'_p']}}%</dd>
        <dd class="draw">走水：{{$sortData['asia']['2']['draw'.$key.'_p']}}%</dd>
        <dd class="lose">主输：{{$sortData['asia']['2']['lose'.$key.'_p']}}%</dd>
    </dl>
    <dl class="goal start">
        <dt><canvas width="90px" height="90p" win="{{$sortData['goal']['1']['win'.$key.'_p']}}" draw="{{$sortData['goal']['1']['draw'.$key.'_p']}}" lose="{{$sortData['goal']['1']['lose'.$key.'_p']}}"></canvas></dt>
        <dd class="win">大球：{{$sortData['goal']['1']['win'.$key.'_p']}}%</dd>
        <dd class="draw">走水：{{$sortData['goal']['1']['draw'.$key.'_p']}}%</dd>
        <dd class="lose">小球：{{$sortData['goal']['1']['lose'.$key.'_p']}}%</dd>
    </dl>
    <dl class="goal end" style="display: none;">
        <dt><canvas width="90px" height="90p" win="{{$sortData['goal']['2']['win'.$key.'_p']}}" draw="{{$sortData['goal']['2']['draw'.$key.'_p']}}" lose="{{$sortData['goal']['2']['lose'.$key.'_p']}}"></canvas></dt>
        <dd class="win">大球：{{$sortData['goal']['2']['win'.$key.'_p']}}%</dd>
        <dd class="draw">走水：{{$sortData['goal']['2']['draw'.$key.'_p']}}%</dd>
        <dd class="lose">小球：{{$sortData['goal']['2']['lose'.$key.'_p']}}%</dd>
    </dl>
</div>