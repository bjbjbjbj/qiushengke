@extends('pc.layout.league_base_bk')
@section('league_js')
    <script type="text/javascript">
        function changeDate(input) {
            var timeStr = '';
            var params = input.value.split('/');
            timeStr = params[2]+'-'+params[0]+'-'+params[1];
            $.ajax({
                'url':'http://localhost:8000//league/basket/schedule/'+'{{$lid}}'+'.html?date='+timeStr,
                'success':function (html) {
                    $('div#Match tbody').html(html);
                }
            });
        }
    </script>
    @endsection
@section('league_content')
    <div class="lbox" id="Match">
        <div class="title">
            <p>赛程赛果</p>
            <div class="inbox"><input type="text" name="date" placeholder="请选择日期" value="{{date('m/d/Y', $start)}}" onchange="changeDate(this)"><button></button></div>
        </div>
        <div class="con">
            <table class="match">
                <colgroup>
                    <col num="1" width="100px">
                    <col num="2" width="">
                    <col num="3" width="32px">
                    <col num="4" width="80px">
                    <col num="5" width="32px">
                    <col num="6" width="">
                    <col num="7" width="12%">
                    <col num="8" width="12%">
                    <col num="8" width="100px">
                </colgroup>
                <thead>
                <tr>
                    <th>时间</th>
                    <th colspan="5">对阵</th>
                    <th>让分</th>
                    <th>总分</th>
                    <th>分析</th>
                </tr>
                </thead>
                <tbody>
                @foreach($schedule as $match)
                    @component('pc.cell.league_list_match_bk',['match'=>$match])
                    @endcomponent
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection