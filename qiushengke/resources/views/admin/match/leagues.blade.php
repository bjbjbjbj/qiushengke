@extends('admin.layout.nav')
@section("css")
    {{--<link href="/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">--}}
    <style>
        select {
            height: 26px;
        }

        .table tr td div {
            /*background: #C0C0C0;*/
            margin-bottom: 3px;
            padding-top: 8px;
            padding-bottom: 5px;
        }

        .table tr td div:nth-child(odd) {
            /*background: #C0C0C0;*/
        }

    </style>
@endsection
@section('content')
    <h1 class="page-header">{{$sport == 1 ? '足球' : '篮球'}}赛事设置</h1>

    <div class="row">
        <div class="table-responsive">
            <div style="text-align: left">
                <form action="/admin/leagues{{$sport == 2 ? '/basketball' : ''}}">
                    <label>赛事名称：</label>
                    <input type="text" name="l_name" value="{{ request('l_name', '') }}">
                    <button type="submit" class="btn btn-sm btn-primary">搜索</button>
                </form>
            </div>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>赛事名称</th>
                    <th width="85%">颜色</th>
                    <th width="100px;">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($leagues as $league)
                    <tr>
                        <td>
                            {{$league['name']}}
                        </td>
                        <td id="td_{{$league->id}}" league_id="{{$league->id}}">
                            <?php
                            if(isset($league['color'])){
                                $r = hexdec(substr($league['color'],0,2));
                                $g = hexdec(substr($league['color'],2,2));
                                $b = hexdec(substr($league['color'],4,2));
                            }
                            ?>
                            <input @if(isset($league['color']))style="background: rgb({{$r}}, {{$g}}, {{$b}});"@endif onkeyup="changeColor(this)" style="width: 120px;" name="color" value="{{$league['color']}}" placeholder="颜色FFFFFF">
                        </td>
                        <td>
                            <button class="btn btn-success btn-xs" type="button" onclick="saveLeague(this, '{{$league->id}}', '{{$sport or 1}}');">保存</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });

        function changeColor(input) {
            var color = input.value;
            color = color.replace('#','');
            input.style.background = '#' + color;
        }
        
        /**
         * 保存赛事
         * @param thisObj   保存按钮
         * @param lId 赛事id
         * @param sport     运动类型
         */
        function saveLeague(thisObj, lId, sport) {
            var dataDiv = $(thisObj).parent().parent();
            var lId = lId;
            var color = dataDiv.find("input[name=color]").val();

            if ($.trim(color) == "") {
                alert("颜色不能为空。");
                return;
            }

            var data = {};
            data['lid'] = lId;
            data['sport'] = sport;
            data['color'] = color;
            thisObj.setAttribute('disabled', 'disabled');
            $.ajax({
                "url": "/admin/leagues/save",
                "type": "POST",
                "data": data,
                "dataType": "json",
                "success": function (json) {
                    if (json && json.code == 200) {
                        alert(json.msg);
                        location.reload();
                    } else if (json) {
                        alert(json.msg);
                    } else {
                        alert("保存失败");
                    }
                    thisObj.removeAttribute('disabled');
                },
                "error": function () {
                    alert('保存失败。');
                    thisObj.removeAttribute('disabled');
                }
            });
        }

    </script>
@endsection