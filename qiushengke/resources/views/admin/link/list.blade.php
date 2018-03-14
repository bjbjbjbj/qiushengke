@extends('admin.layout.nav')
@section('content')
    <h1 class="page-header">友情链接</h1>
    <div class="row placeholders">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <form action="/admin/links/save" method="post" onsubmit="return saveLink(this);">
                        <th>
                            {{ csrf_field() }}
                        </th>
                        <th>
                            <input type="text" name="name" class="form-control" placeholder="名称"
                                   value="{{ session('name','') }}" required>
                        </th>
                        <th>
                            <input type="text" name="url" class="form-control" placeholder="链接"
                                   value="{{ session('url','') }}" required>
                        </th>
                        <th>
                            <select name="show" class="form-control" required>
                                <option value="1">显示</option>
                                <option value="0">隐藏</option>
                            </select>
                        </th>
                        <th>
                            <input type="number" name="od" class="form-control" placeholder="排序"
                                   value="{{ session('od','') }}">
                        </th>
                        <th>
                            <button type="submit" class="btn btn-sm btn-primary"><span
                                        class="glyphicon glyphicon-plus"></span>新建
                            </button>
                        </th>
                    </form>
                </tr>
                <tr>
                    <th width="5%">#</th>
                    <th width="15%">名称</th>
                    <th>链接</th>
                    <th width="10%">是否显示</th>
                    <th width="10%">排序</th>
                    <th width="15%">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($links as $link)
                    <form action="/admin/link/save" method="post" onsubmit="return saveLink(this);">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $link->id }}">
                        <tr>
                            <td><h5>{{ $link->id }}</h5></td>
                            <td>
                                <input type="text" name="name" class="form-control" placeholder="名称"
                                       value="{{ $link->name }}" required>
                            </td>
                            <td>
                                <input type="text" name="url" class="form-control" placeholder="链接"
                                       value="{{ $link->url }}" required>
                            </td>
                            <td>
                                <select name="show" class="form-control" required>
                                    <option value="1" {{ $link->show==1?'selected':'' }}>显示</option>
                                    <option value="0" {{ $link->show==0?'selected':'' }}>隐藏</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" name="od" class="form-control" placeholder="排序"
                                       value="{{ $link->od }}">
                            </td>
                            <td>
                                <p>
                                    <button type="submit" class="btn btn-sm btn-info">
                                        <span class="glyphicon glyphicon-ok"></span>保存
                                    </button>
                                    <a class="btn btn-sm btn-danger" href="javascript:if (confirm('是否确定删除外链？')) { location.href = '/admin/links/del?id={{ $link->id }}';}">
                                        <span class="glyphicon glyphicon-remove"></span>删除
                                    </a>
                                </p>
                            </td>
                        </tr>
                    </form>
                @endforeach
                </tbody>
            </table>
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
        function saveLink(formObj) {
            var form = $(formObj);
            var data = form.serialize();
            var btn = form.find('[type=submit]');
            btn.attr('disabled', 'disabled');
            $.ajax({
                "url": "/admin/links/save",
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