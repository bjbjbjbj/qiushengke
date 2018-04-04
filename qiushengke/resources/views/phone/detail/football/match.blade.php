@extends('phone.layout.base')
@section("css")
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/phone/css/match.css">
@endsection
@section("content")
    @component("phone.detail.football.cell.info_cell", ['cdn'=>$cdn, 'match'=>$match]) {{-- Navigation Info 模块 --}}
    @endcomponent
    <div id="Tab" class="tab">
        <input type="radio" name="tab_type" id="Type_Match" value="Match">
        <label for="Type_Match"><span>赛况</span></label>
        <input type="radio" name="tab_type" id="Type_Data" value="Data" checked>
        <label for="Type_Data"><span>分析</span></label>
        <input type="radio" name="tab_type" id="Type_Odd" value="Odd">
        <label for="Type_Odd"><span>指数</span></label>
        <input type="radio" name="tab_type" id="Type_SameOdd" value="SameOdd">
        <label for="Type_SameOdd"><span>同赔</span></label>
        <input type="radio" name="tab_type" id="Type_Team" value="Team">
        <label for="Type_Team"><span>角球</span></label>
    </div>
    @component("phone.detail.football.cell.match_cell", ['cdn'=>$cdn, 'lineup'=>$lineup, 'tech'=>$tech, 'match'=>$match ]) {{-- Match 模块 --}}
    @endcomponent
    @component("phone.detail.football.cell.data_cell", ['cdn'=>$cdn, 'match'=>$match, 'analyse'=>$analyse ]) {{-- Data 模块 --}}
    @endcomponent
    @component("phone.detail.football.cell.team_cell", ['cdn'=>$cdn, 'match'=>$match, 'analyse'=>$analyse] ) {{-- Team 模块 --}}
    @endcomponent
    <div id="Odd" class="content" style="display: none;"></div>
    @component("phone.detail.football.cell.same_odd_cell", ['cdn'=>$cdn, 'match'=>$match, 'sameOdd'=>(isset($analyse['sameOdd']) ? $analyse['sameOdd'] : null) ]) {{-- SameOdd 模块 --}}
    @endcomponent
@endsection
@section("js")
<script type="text/javascript" src="/phone/js/match.js"></script>
<script type="text/javascript" src="/phone/js/match_football_data.js"></script>
<script type="text/javascript">
     window.onload = function () {
         // setPage() //base中已经存在
         setDataUpdate('{{$mid}}');
     };
     $(function () {
         setCanvas();
         $.get('/wap/match/foot/detail/odd_cell/{{$first}}/{{$second}}/{{$mid}}.html', function (json) {
             var dataOddHtml = json.odd_html;
             if (dataOddHtml && dataOddHtml != "") {
                 $("#Data div.odd").html(dataOddHtml);
             }
             var oddIndexHtml = json.index_html;
             if (oddIndexHtml && oddIndexHtml != "") {
                 $("#Odd").html(oddIndexHtml);
             }
             setPage();
         });
     });
    window.onscroll = function () {
        setHead();
    }
</script>
@endsection

























