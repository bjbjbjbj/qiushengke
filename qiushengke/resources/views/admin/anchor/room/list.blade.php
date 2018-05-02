@extends('admin.layout.nav')
@section('css')
    <style>
        .form-css {
            height: 33px;
            border: 1px solid #ccc;
            color:#555;
            border-radius:4px;
            font-size: 14px;
        }
        td {
            text-align: left;
        }
    </style>
@endsection
@section('content')
    <h1 class="page-header">直播间列表</h1>
    <div class="row placeholders">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <form action="/admin/anchor/rooms/save" method="post" enctype="multipart/form-data" onsubmit="return checkAnchor(this);">
                        <th>{{ csrf_field() }}</th>
                        <th>
                            <input type="text" name="name" class="form-control" placeholder="直播间名称" value="{{ session('name','') }}" required>
                        </th>
                        <th>
                            <select name="anchor_id" class="form-css" required>
                                <option value="">请选择</option>
                                @foreach($anchors as $anchor)
                                <option value="{{$anchor->id}}">{{$anchor->name}}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            <select name="type" class="form-css" required>
                                <option value="">请选择</option>
                                @foreach($types as $type)
                                <option value="{{$type->id}}">{{$type->name}}</option>
                                @endforeach
                            </select>
                        </th>
                        <th><input style="width: 150px;" type="file" name="cover"></th>
                        <th><input name="link" style="width: 88%;" class="form-css" placeholder="链接/源" required></th>
                        <th>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <span class="glyphicon glyphicon-plus"></span>新建
                            </button>
                        </th>
                    </form>
                </tr>
                <tr>
                    <th width="5%">#</th>
                    <th width="12%">名称</th>
                    <th width="12%">主播</th>
                    <th width="10%">平台</th>
                    <th width="150px">封面</th>
                    <th>链接/源</th>
                    <th width="15%">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($page as $room)
                    <form action="/admin/anchor/rooms/save" enctype="multipart/form-data" method="post" onsubmit="return checkAnchor(this);">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $room->id }}">
                        <tr>
                            <td><h5>{{ $room->id }}</h5></td>
                            <td>
                                <input type="text" name="name" class="form-css" value="{{ $room->name }}" required>
                            </td>
                            <td>
                                <select name="anchor_id" class="form-css">
                                    @foreach($anchors as $anchor)
                                    <option value="{{$anchor->id}}" @if($anchor->id == $room->anchor_id) selected @endif>{{$anchor->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="type" class="form-css">
                                    @foreach($types as $type)
                                        <option value="{{$type->id}}" @if($type->id == $room->type) selected @endif>{{$type->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="text-align:left;">
                                @if(!empty($room->cover)) <img src="{{$room->cover}}" style="max-width: 100%;max-height: 100px;"> @endif
                                <input type="file" name="cover" style="width: 150px;" >
                            </td>
                            <td><input class="form-css" style="width: 88%;"  name="link" value="{{$room->link}}"></td>
                            <td>
                                <p>
                                    <b>状态：{{$room->statusCn()}}</b>
                                </p>
                                <p>
                                    <button type="submit" class="btn btn-xs btn-info">保存</button>
                                    <?php
                                    $msg = isset($anchor) && $anchor->status == 1 ? "隐藏" : "显示";
                                    $status = isset($anchor) && $anchor->status == 1 ? -1 : 1;
                                    ?>
                                    <a class="btn btn-xs btn-danger" href="javascript:if (confirm('是否{{$msg}}主播')) { location.href = '/admin/anchor/change?status={{$status}}&id={{ $anchor->id or '' }}';}">
                                        {{$msg}}
                                    </a>
                                </p>
                            </td>
                        </tr>
                    </form>
                @endforeach
                </tbody>
            </table>
            {{$page or ''}}
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        function submit() {
            console.info('submit...');
        }

        /**
         * 检查字段
         * @param formObj
         * @returns {boolean}
         */
        function checkAnchor(formObj) {
            var name = formObj.name.value;
            var intro = formObj.intro.value;

            if (name == "" || $.trim(name) == "") {
                alert("主播名称不能为空");
                return false;
            }

            if (name.length >50) {
                alert("主播名称不能大于50字");
                return false;
            }
            if (intro.length > 255) {
                alert("主播简介不能大于255字");
                return false;
            }
            return true;
        }
    </script>
@endsection