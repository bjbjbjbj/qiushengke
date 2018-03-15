@extends('admin.layout.nav')
@section('content')
    <h1 class="page-header">直播间列表</h1>
    <div class="row placeholders">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <form action="/admin/anchor/save" method="post" enctype="multipart/form-data" onsubmit="return checkAnchor(this);">
                        <th>{{ csrf_field() }}</th>
                        <th>
                            <input type="text" name="name" class="form-control" placeholder="主播名" value="{{ session('name','') }}" required>
                        </th>
                        <th><textarea name="intro" class="form-control"></textarea></th>
                        <th><input type="file" name="icon"></th>
                        <th>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <span class="glyphicon glyphicon-plus"></span>新建
                            </button>
                        </th>
                    </form>
                </tr>
                <tr>
                    <th width="5%">#</th>
                    <th width="15%">名称</th>
                    <th width="25%">简介</th>
                    <th width="10%">头像</th>
                    <th width="15%">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($page as $anchor)
                    <form action="/admin/anchor/save" enctype="multipart/form-data" method="post" onsubmit="return checkAnchor(this);">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $anchor->id }}">
                        <tr>
                            <td><h5>{{ $anchor->id }}</h5></td>
                            <td>
                                <input type="text" name="name" class="form-control" placeholder="名称" value="{{ $anchor->name }}" required>
                            </td>
                            <td><textarea name="intro" class="form-control">{{$anchor->intro}}</textarea></td>
                            <td style="text-align:left;">
                                @if(!empty($anchor->icon)) <img src="{{$anchor->icon}}" style="max-width: 100%;max-height: 100px;"> @endif
                                <input type="file" name="icon">
                            </td>
                            <td>
                                <p>
                                    <button type="submit" class="btn btn-sm btn-info">保存</button>
                                </p>
                                <p>
                                    <?php
                                    $msg = $anchor->status == 1 ? "隐藏" : "显示";
                                    $status = $anchor->status == 1 ? -1 : 1;
                                    ?>
                                    <a class="btn btn-sm btn-danger" href="javascript:if (confirm('是否{{$msg}}主播')) { location.href = '/admin/anchor/change?status={{$status}}&id={{ $anchor->id }}';}">
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