@extends('phone.layout.matchlist_bk')
@section('match_list_content')
    <div id="Navigation">
        <div class="tab">
            <a href="/wap/match/basket/schedule/immediate_t.html">即时比分</a>
            <a href="/wap/match/basket/schedule/{{$lastDate}}/result_t.html">完场赛果</a>
            <a class="on">未来赛程</a>
            {{--<a href="hotleague.html">热门赛事</a>--}}
        </div>
        <div class="filter">
            <p class="in league">重要赛事</p>
            <p class="in date"><input type="date" placeholder="{{$currDate}}" name="date" onchange="clickDate(this)"></p>
            <p class="num">共<span>{{$total}}</span>场&nbsp;&nbsp;隐藏<span id="hideMatchCount">-</span>场</p>
        </div>
    </div>
    <div id="List">
        @foreach($matches as $match)
            @component('phone.cell.match_list_schedule_cell_bk',['match'=>$match,'sport'=>$sport,'cdn'=>$cdn])
            @endcomponent
        @endforeach
    </div>
@endsection
@section('match_list_js')
    <script type="text/javascript" src="{{$cdn}}/phone/js/immediate.js"></script>
    <script type="text/javascript">
        $('#Navigation .filter input[type=date]').val('{{$currDate}}');
        function clickDate(date) {
            var value = date.value.replace(/\//g,"");
            value = date.value.replace(/-/g,"");
            var url = '{{env('APP_URL')}}' + '/wap/match/basket/schedule/'+value+'/schedule_t.html';
            window.location.href = url;
        }
    </script>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/phone/css/immediate_bk.css">
@endsection