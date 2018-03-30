@extends('phone.layout.matchlist_bk')
@section('match_list_content')
    <div id="Navigation">
        <div class="tab">
            <a class="on">即时比分</a>
            <a href="/wap/match/basket/schedule/{{$lastDate}}/result_t.html">完场赛果</a>
            <a href="/wap/match/basket/schedule/{{$nextDate}}/schedule_t.html">未来赛程</a>
            {{--<a href="hotleague.html">热门赛事</a>--}}
        </div>
        <div class="filter">
            <p class="in league">重要赛事</p>
            <p class="in select">
                <span>时间排序</span>
                <select onchange="clickSort(this)">
                    <option value="1">时间排序</option>
                    <option value="2">赛事排序</option>
                </select>
            </p>
            <p class="num">共<span>{{$total}}</span>场&nbsp;&nbsp;隐藏<span id="hideMatchCount">-</span>场</p>
        </div>
    </div>
    <div id="List">
        @foreach($matches as $match)
            @component('phone.cell.match_list_cell_bk',['cdn'=>$cdn,'match'=>$match,'sport'=>$sport])
            @endcomponent
        @endforeach
    </div>
@endsection
@section('match_list_js')
    <script type="text/javascript" src="{{$cdn}}/phone/js/immediate.js"></script>
    <script type="text/javascript">
        function clickSort(select) {
            var url = window.location.href;
            if (select.value == 1){
                url = url.replace('_l.html','_t.html');
            }
            else if(select.value == 2){
                url = url.replace('_t.html','_l.html');
            }
            window.location.href = url;
        }
    </script>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/phone/css/immediate_bk.css">
@endsection