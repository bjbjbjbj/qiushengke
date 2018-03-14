<?php
$totalCount = $hWinCount + $aWinCount;
$h_p = $totalCount > 0 ? $hWinCount * 100 / $totalCount : 0;
$a_p = $totalCount > 0 ? $aWinCount * 100 / $totalCount : 0;
?>
<dl num="{{$key}}" @if(isset($show) && $show == 0) style="display: none" @endif>
    <dt class="win">{{$hWinCount}}胜</dt>
    <dt class="lose">{{$aWinCount}}负</dt>
    <dd><p class="win" style="width: {{$h_p}}%"></p><p class="lose" style="width: {{$a_p}}%"></p></dd>
</dl>