@extends('phone.layout.base')
@section("css")
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/phone/css/match.css">
@endsection
@section("content")
    {!! $views !!}
@endsection
@section("js")
<script type="text/javascript" src="/phone/js/match.js"></script>
<script type="text/javascript" src="/phone/js/match_football_data.js"></script>
<script type="text/javascript">
     $(function () {
         setCanvas();
         @if(isset($odds))
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
         @endif
     });
</script>
@endsection

























