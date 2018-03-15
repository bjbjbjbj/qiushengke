@extends('admin.layout.nav')
@section('content')
    <h1 class="page-header">直播平台列表</h1>
    <div class="row placeholders">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <form method="post" onsubmit="return savePlatform(this);">
                        <th>
                            {{ csrf_field() }}
                        </th>
                        <th>
                            <input type="text" name="name" class="form-control" placeholder="名称"
                                   value="{{ session('name','') }}" required>
                        </th>
                        <th>
                            <select name="status" class="form-control" required>
                                <option value="1">显示</option>
                                <option value="2">隐藏</option>
                            </select>
                        </th>
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
                    <th width="10%">是否显示</th>
                    <th width="15%">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($page as $platform)
                    <form action="/admin/anchor/platforms/save" method="post" onsubmit="return savePlatform(this);">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $platform->id }}">
                        <tr>
                            <td><h5>{{ $platform->id }}</h5></td>
                            <td>
                                <input type="text" name="name" class="form-control" placeholder="名称" value="{{ $platform->name }}" required>
                            </td>
                            <td>
                                <select name="status" class="form-control" required>
                                    <option value="1" {{ $platform->status==1?'selected':'' }}>显示</option>
                                    <option value="0" {{ $platform->status==2?'selected':'' }}>隐藏</option>
                                </select>
                            </td>
                            <td>
                                <p>
                                    <button type="submit" class="btn btn-sm btn-info">
                                        <span class="glyphicon glyphicon-ok"></span>保存
                                    </button>
                                    {{--<a class="btn btn-sm btn-danger" href="javascript:if (confirm('是否确定删除外链？')) { location.href = '/admin/links/del?id={{ $platform->id }}';}">--}}
                                        {{--<span class="glyphicon glyphicon-remove"></span>删除--}}
                                    {{--</a>--}}
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
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });

        /**
         * 保存外链
         * @param formObj
         * @returns {boolean}
         */
        function savePlatform(formObj) {
            var form = $(formObj);
            var data = form.serialize();
            var btn = form.find('[type=submit]');
            btn.attr('disabled', 'disabled');
            $.ajax({
                "url": "/admin/anchor/platforms/save",
                "data": data,
                "type": "post",
                "dataType": "json",
                "success": function (json) {
                    if (json) {
                        alert(json.msg);
                        if (json.code == 200) {
                            location.reload();
                        }
                    } else {
                        btn.attr('disabled', '');
                    }
                },
                "error": function () {
                    alert("保存失败");
                    btn.attr('disabled', '');
                }
            });
            return false;
        }
    </script>
@endsection