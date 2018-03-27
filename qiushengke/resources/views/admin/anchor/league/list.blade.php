@extends('admin.layout.nav')
@section('css')
    <style>
        .form-input-css {
            display: inline;margin-left: 5px;vertical-align: bottom;
        }
        .bs-example {
            margin-right: 0;
            margin-left: 0;
            background-color: #fff;
            border-color: #ddd;
            border-width: 1px;
            border-radius: 4px 4px 0 0;
            -webkit-box-shadow: none;
            box-shadow: none;
        }
        .bs-example::after {
            content: "可预约赛事：";
        }
        .book {
            background-color:#f5f5f5;
            border:1px solid #ccc;
            border-radius:4px;
            padding: 10px;
            margin: 0 0 10px;
        }
        .book span {
            margin-top: 5px;
        }
        .book ul
        {
            list-style:none; /* 去掉ul前面的符号 */
            margin: 0px; /* 与外界元素的距离为0 */
            padding: 0px; /* 与内部元素的距离为0 */
            width: auto; /* 宽度根据元素内容调整 */
        }
        div.book ul li
        {
            margin-left: 5px;
            margin-top: 10px;
            float:left; /* 向左漂移，将竖排变为横排 */
        }
    </style>
@endsection
@section('content')
    <h1 class="page-header">联赛设置</h1>
    <div style="margin-bottom: 10px;">
        <form action="/admin/anchor/{{$sport == 1 ? 'football' : 'basketball'}}/leagues">
            <input style="width:99px;" name="name" value="{{request('name', '')}}" placeholder="联赛名称" class="form-control form-input-css">
            <select style="width:99px;" name="type" class="form-control form-input-css">
                <option value="">全部</option>
                @if($sport == 1)<option value="1" @if(request('type', '') == '1') selected @endif >主流</option>@endif
                <option value="2" @if(request('type', '') == '2') selected @endif >热门</option>
            </select>
            <button type="submit" class="btn btn btn-success">查询</button>
        </form>
    </div>

    <div class="bs-example" data-example-id="simple-nav-pills"></div>
    <div class="book">
        @if(count($books) == 0) 暂未选择预约联赛 @else
            <ul>
            @foreach($books as $index=>$book)
            <li>
            <b style="font-size: 14px; color: #2a88bd;margin-left: 5px;">{{$book->name}}</b>
            <a class="btn btn-xs btn-danger" id="{{$book->id}}" onclick="changeStatus('{{$book->id}}', 1, this)">取消</a>
            </li>
            @endforeach
            <div style="clear:both"></div>
            </ul>
        @endif
    </div>

    <div class="row placeholders">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="15%">联赛名称</th>
                    <th width="15%">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($page as $league)
                    <tr style="text-align: left;">
                        <td><h5>{{ $league->id }}</h5></td>
                        <td>{{ $league->name }}</td>
                        <td>
                            @if($league->status == 2)
                                <a class="btn btn-sm btn-danger" onclick="changeStatus('{{$league->id}}', 1, this);">取消预约</a>
                            @else
                                <a class="btn btn-sm btn-info" onclick="changeStatus('{{$league->id}}', 2, this);">设置预约</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$page or ''}}
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });
        function changeStatus(id, status, btnObj) {
            var msg = status == 1 ? '是否确认取消预约' : '是否确认设置预约';
            if (!confirm(msg)) {
                return;
            }
            btnObj.setAttribute('disabled', 'disabled');
            $.ajax({
                "url": "/admin/anchor/leagues/change",
                "type": "post",
                "data": {"id": id, "status": status, 'sport': "{{$sport or '1'}}"},
                "dataType": "json",
                "success": function (json) {
                    if (json) {
                        alert(json.msg);
                        if (json.code == 200) {
                            location.reload();
                        }
                    }
                    btnObj.removeAttribute('disabled');
                },
                "error": function () {
                    btnObj.removeAttribute('disabled');
                }
            });
        }
    </script>
@endsection