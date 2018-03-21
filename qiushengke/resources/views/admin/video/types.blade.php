@extends('admin.layout.nav')
@section('content')
    <h1 class="page-header">录像分类</h1>
    <div class="row placeholders">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <form action="/admin/videos/types/save" method="post" onsubmit="return saveType(this);">
                        <th>{{ csrf_field() }}</th>
                        <th>
                            <input type="text" name="name" class="form-control" placeholder="分类名称" value="{{ session('name','') }}" required>
                        </th>
                        <th>
                            <select name="status" class="form-control" required>
                                <option value="1">显示</option>
                                <option value="2">隐藏</option>
                            </select>
                        </th>
                        <th>
                            <input type="number" name="od" class="form-control" placeholder="排序" value="{{ session('od','') }}">
                        </th>
                        <th>
                            <button type="submit" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-plus"></span>新建</button>
                        </th>
                    </form>
                </tr>
                <tr>
                    <th width="5%">#</th>
                    <th width="15%">名称</th>
                    <th width="10%">是否显示</th>
                    <th width="10%">排序</th>
                    <th width="15%">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($page as $type)
                    <form action="/admin/videos/types/save" method="post" onsubmit="return saveType(this);">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $type->id }}">
                        <tr>
                            <td><h5>{{ $type->id }}</h5></td>
                            <td>
                                <input name="name" class="form-control" placeholder="名称" value="{{ $type->name }}" required>
                            </td>
                            <td>
                                <select name="status" class="form-control" required>
                                    <option value="1" {{ $type->status == 1 ? 'selected' : '' }}>显示</option>
                                    <option value="2" {{ $type->status == 2 ? 'selected' : '' }}>隐藏</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" name="od" class="form-control" placeholder="排序" value="{{ $type->od }}">
                            </td>
                            <td style="text-align: left;">
                                <p>
                                    <button type="submit" class="btn btn-sm btn-info"><span class="glyphicon glyphicon-ok"></span>保存</button>
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

        var statusObj = $("select[name='status']");
        statusObj.change(function () {
            setStatusColor(this);
        });
        statusObj.each(function () {
            setStatusColor(this);
        });

        function setStatusColor(thisObj) {
            var value = thisObj.value;
            if (value == 2) {
                thisObj.style.background = '#ac2925';
                thisObj.style.color = '#fff';
            } else {
                thisObj.style.background = '';
                thisObj.style.color = '';
            }
        }

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });

        /**
         * 保存外链
         * @param formObj
         * @returns {boolean}
         */
        function saveType(formObj) {
            var form = $(formObj);
            var data = form.serialize();
            var btn = form.find('[type=submit]');
            btn.attr('disabled', 'disabled');
            $.ajax({
                "url": "/admin/videos/types/save",
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