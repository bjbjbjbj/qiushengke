@extends('admin.layout.nav')
@section('content')
    <h1 class="page-header" style="margin-bottom: 5px;">录像列表</h1>
    <div style="margin-bottom: 10px;">
    <a href="/admin/videos/edit" target="_blank" class="btn btn-primary">新增录像</a>
    </div>
    <div class="row placeholders">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>封面</th>
                    <th>信息</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody style="text-align: left;">
                @foreach($page as $video)
                    <form action="/admin/link/save" method="post" onsubmit="return saveLink(this);">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $video->id }}">
                        <tr>
                            <td><h5>{{ $video->id }}</h5></td>
                            <td>
                                @if(!empty($video))
                                <img src="{{$video->cover}}" style="max-width: 200px;max-height: 150px;" />
                                @endif
                            </td>
                            <td>
                                <p>
                                    <b>专题联赛：</b>{{isset($leagues[$video->subject_lid]) ? $leagues[$video->subject_lid] : ''}}
                                    &nbsp;&nbsp;&nbsp;<b>播放方式：</b>{{$players[$video->player]}}
                                </p>
                                <p><b>源链接：</b>{{$video->content}}</p>
                                <p><b>是否显示：</b>{{$video->statusCn()}}</p>
                                <p><b>排序：</b>{{$video->od}}</p>
                            </td>
                            <td>
                                <a class="btn btn-primary btn-sm" href="/admin/videos/edit?id={{$video->id}}" target="_blank">修改</a>
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