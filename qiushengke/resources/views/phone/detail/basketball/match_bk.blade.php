@extends('phone.layout.base')
@section("css")
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/phone/css/match_bk.css">
@endsection
@section("content")
    @component("phone.detail.basketball.cell.info_cell", ['cdn'=>$cdn, 'match'=>$match]) {{-- Navigation Info 模块 --}}
    @endcomponent
    <div id="Tab" class="tab">
        <input type="radio" name="tab_type" id="Type_Match" value="Match" checked>
        <label for="Type_Match"><span>赛况</span></label>
        <input type="radio" name="tab_type" id="Type_Data" value="Data">
        <label for="Type_Data"><span>分析</span></label>
        <input type="radio" name="tab_type" id="Type_Odd" value="Odd">
        <label for="Type_Odd"><span>指数</span></label>
    </div>
    <div id="Match" class="content" style="display: ;">
        @component("phone.detail.basketball.cell.match_cell", ['cdn'=>$cdn, 'match'=>$match,'tech'=>$tech,'players'=>$players]) {{-- Match 模块 --}}
        @endcomponent
    </div>
    <div id="Data" class="content" style="display: none;">
        @component("phone.detail.basketball.cell.data_cell", ['cdn'=>$cdn, 'match'=>$match,'odds'=>$odds,'analyse'=>$analyse]) {{-- Data 模块 --}}
        @endcomponent
    </div>
    <div id="Odd" class="content" style="display: none;"></div>
@endsection
@section("js")
<script type="text/javascript" src="/phone/js/match.js"></script>
<script type="text/javascript" src="/phone/js/match_basketball_data.js"></script>
<script type="text/javascript">
     window.onload = function () {
         setDataUpdate('{{$mid}}'); //ID
     }
    $(function () {
        setCanvas();
        $.get('/wap/match/basket/detail/odd_cell/{{$first}}/{{$second}}/{{$mid}}.html', function (json) {
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

























