@extends('phone.layout.base')
@section("css")
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/phone/css/match.css">
@endsection
@section("content")
    @component("phone.detail.football.cell.info_cell", ['cdn'=>$cdn, 'match'=>$match]) {{-- Navigation Info 模块 --}}
    @endcomponent
    <div id="Tab" class="tab">
        <input type="radio" name="tab_type" id="Type_Match" value="Match" checked>
        <label for="Type_Match"><span>赛况</span></label>
        <input type="radio" name="tab_type" id="Type_Data" value="Data">
        <label for="Type_Data"><span>分析</span></label>
        <input type="radio" name="tab_type" id="Type_Team" value="Team">
        <label for="Type_Team"><span>球队</span></label>
        <input type="radio" name="tab_type" id="Type_Odd" value="Odd">
        <label for="Type_Odd"><span>指数</span></label>
        <input type="radio" name="tab_type" id="Type_SameOdd" value="SameOdd">
        <label for="Type_SameOdd"><span>同赔</span></label>
    </div>
    @component("phone.detail.football.cell.match_cell", ['cdn'=>$cdn, 'lineup'=>$lineup, 'tech'=>$tech, 'match'=>$match ]) {{-- Match 模块 --}}
    @endcomponent
    @component("phone.detail.football.cell.data_cell", ['cdn'=>$cdn, 'match'=>$match, 'analyse'=>$analyse ]) {{-- Data 模块 --}}
    @endcomponent
    @component("phone.detail.football.cell.team_cell", ['cdn'=>$cdn, 'match'=>$match, 'analyse'=>$analyse] ) {{-- Team 模块 --}}
    @endcomponent
    @component("phone.detail.football.cell.odd_cell", ['cdn'=>$cdn, 'match'=>$match] ) {{-- Odd 模块 TODO --}}
    @endcomponent
    @component("phone.detail.football.cell.same_odd_cell", ['cdn'=>$cdn, 'match'=>$match, 'sameOdd'=>(isset($analyse['sameOdd']) ? $analyse['sameOdd'] : null) ]) {{-- SameOdd 模块 --}}
    @endcomponent
@endsection
@section("js")
<script type="text/javascript" src="/phone/js/match.js"></script>
<script type="text/javascript">
    // window.onload = function () {
    //     // setPage() //base中已经存在
    //     setCanvas();
    // }
    $(function () {
        setCanvas();
        $.get('/wap/match/foot_cell/{{$first}}/{{$second}}/{{$mid}}.html', function (html) {
            if (html && html != "") {
                $("#Data div.odd").html(html);

                var BtnClose = $('#Data div.odd button.close');
                BtnClose.click(function(){
                    if ($(this).parents('.default').attr('close')) {
                        $(this).parents('.default').removeAttr('close');
                    }else{
                        $(this).parents('.default').attr('close','close');
                    }
                });

                var Sel = $('#Data div.odd select');
                Sel.change(function(){
                    $(this).parents('.default').children('table').css('display','none');
                    $('#' + $(this).children('option:selected').val()).css('display','');
                })
            }
        });
    });
    window.onscroll = function () {
        setHead();
    }
</script>
@endsection

























