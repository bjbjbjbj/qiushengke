@extends('admin.layout.nav')
@section('content')
    <h1 class="page-header">修改录像</h1>
    <div class="row">
        <div class="col-lg-12">
            <form class="form" method="post" action="/admin/videos/save/" onsubmit="return saveVideo(this);">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{ isset($video) ? $video->id : '' }}">
                <input type="hidden" name="cover" id="coverImageInput"  value="{{ isset($video) ? $video->cover : '' }}">

                <div class="input-group form-group">
                    <span class="input-group-addon">标题</span>
                    <input type="text"
                           name="title"
                           value="{{ session('title',isset($video) ? $video->title : '') }}"
                           class="form-control"
                           placeholder="标题"
                           required autofocus>
                    <span class="input-group-addon">{{isset($video) ? mb_strlen($video->title) : 0}}字</span>
                </div>

                <div class="input-group form-group">
                    <span class="input-group-addon">专题联赛</span>
                    <select name="s_lid" class="form-control" required>
                        <option value="">请选择联赛</option>
                        @foreach($leagues as $league)
                        <option value="{{$league->id}}" @if(isset($video) && $video->s_lid == $league->id) selected @endif>{{$league->getName()}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="input-group form-group">
                    <span class="input-group-addon">源链接</span>
                    <input type="text"
                           value="{{ session('labels',isset($video) ? $video->content : '') }}"
                           name="content"
                           class="form-control"
                           placeholder="源链接">
                </div>

                <div class="input-group form-group">
                    <span class="input-group-addon">播放方式</span>
                    <select name="player" class="form-control">
                        @foreach($players as $key=>$name)
                        <option value="{{$key}}" @if(isset($video) && $video->player == $key) selected @endif>{{$name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="input-group form-group">
                    <span class="input-group-addon">显示</span>
                    <select name="status" class="form-control">
                        <option value="1">显示</option>
                        <option value="2" @if(isset($video) && $video->status == 2) selected @endif >隐藏</option>
                    </select>
                </div>

                <div class="input-group form-group">
                    <span class="input-group-addon">排序</span>
                    <input type="number" name="od" class="form-control" value="{{isset($video) ? $video->od : ''}}">
                </div>

                <img @if(!isset($video) || empty($video->cover)) style="display: none" @endif
                        class="img-thumbnail" id="coverImage"
                        src="{{ isset($video) && !empty($video->cover) ? $video->cover : '' }}">

                <div class="input-group form-group">
                    <span class="input-group-addon">上传封面</span>
                    <button type="button" class="btn btn-sm btn-default" onclick="uploadCover()">
                        <span class="glyphicon glyphicon-upload"></span>上传封面
                    </button>
                </div>

                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-2">
                        <button type="submit" id="save" onclick="saveVideo();" class="btn btn-primary">保存</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <form id="imageUploadForm" enctype="multipart/form-data" action="/admin/upload/cover" method="post">
        {{ csrf_field() }}
        <input type="file" id="ImageBrowse" name="cover" onchange="changeCoverImage()" style="position:absolute;clip:rect(0 0 0 0);"/>
    </form>

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
        function saveVideo(thisForm) {
            //判断参数
            var title = thisForm.title.value;
            // var type_id = thisForm.type_id.value;
            var s_lid = thisForm.s_lid.value;
            var content = thisForm.content.value;

            title = $.trim(title);
            content = $.trim(content);

            if (title.length == 0) {
                alert("标题不能为空");
                return false;
            }
            if (title.length > 30) {
                alert("标题不能大于30字");
                return false;
            }
            // if (type_id == "") {
            //     alert("请选择分类");
            //     return false;
            // }
            if (s_lid == "") {
                alert("请选择专题联赛");
                return false;
            }
            if (content.length == 0) {
                alert("源链接不能为空");
                return false;
            }
            //判断参数

            var $form = $(thisForm);
            var data = $form.serialize();
            var btn = $("#save")[0];
            btn.setAttribute('disabled', 'disabled');
            $.ajax({
                "url": "/admin/videos/save",
                "data": data,
                "type": "post",
                "dataType": "json",
                "success": function (json) {
                    if (json) {
                        alert(json.msg);
                        if (json.code == 200) {
                            location.href = "/admin/videos";
                        }
                    }
                    btn.removeAttribute('disabled');
                },
                "error": function () {
                    alert("保存失败");
                    btn.removeAttribute('disabled');
                }
            });
            return false;
        }

        /**
         * 上传图片
         */
        function changeCoverImage() {
            $('.btn').button('loading');
            var formData = new FormData($('#imageUploadForm')[0]);
            console.log(formData);
            $.ajax({
                type: 'POST',
                url: '/admin/upload/cover',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    console.log("success");
                    $('#coverImageInput')[0].value = $('#coverImage')[0].src = data;
                    $('#coverImage').show();
                    $('.btn').button('reset');
                },
                error: function (data) {
                    console.log("error");
                    $('.btn').button('reset');
                }
            });
        }

        /**
         * 选择文件
         */
        function uploadCover() {
            $('#ImageBrowse').click();
        }

        /**
         * 统计标题字数
         */
        $("input[name='title']").keyup(function () {
            var len = this.value.length;
            var $next = $(this).next();
            if (len > 30) {
                $next.css({"background-color": "red"});
            } else {
                $next.css({"background-color": "#eee"});
            }
            $next.html(len + '字');
        });

    </script>
@endsection