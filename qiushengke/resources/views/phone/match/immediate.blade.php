@extends('phone.layout.matchlist')
@section('match_list_content')
    <div id="Navigation">
        <div class="tab">
            <a class="on">即时比分</a>
            <a href="/wap/match/foot/schedule/{{$lastDate}}/result.html">完场赛果</a>
            <a href="/wap/match/foot/schedule/{{$nextDate}}/schedule.html">未来赛程</a>
            {{--<a href="hotleague.html">热门赛事</a>--}}
        </div>
        <div class="filter">
            <p class="in league">重要赛事</p>
            <p class="in odd">全部盘口</p>
            <p class="num">共<span>{{$total}}</span>场&nbsp;&nbsp;隐藏<span id="hideMatchCount">-</span>场</p>
        </div>
    </div>
    <div id="List">
        @foreach($matches as $match)
            @component('phone.cell.match_list_cell',['match'=>$match,'sport'=>$sport,'cdn'=>$cdn])
            @endcomponent
        @endforeach
    </div>
@endsection
@section('match_list_js')
    <script type="text/javascript" src="{{$cdn}}/phone/js/immediate.js"></script>
    <script type="text/javascript">
    </script>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/phone/css/immediate.css">
@endsection