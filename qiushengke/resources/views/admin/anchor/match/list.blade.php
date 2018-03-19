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
            content: "选择赛事：";
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
            margin-left: 10px;
            /*margin-top: 10px;*/
            float:left; /* 向左漂移，将竖排变为横排 */
        }
        div.book ul li button:hover
        {
            background-color: #449d44;
        }
    </style>
@endsection
@section('content')
    <h1 class="page-header">比赛预约</h1>
    <div class="bs-example" data-example-id="simple-nav-pills"></div>
    <div class="book">
        @if(count($books) == 0) 暂未选择预约联赛 @else
            <ul>
                <li><button lid="" class="btn btn-sm @if(request('lid', '') == '') btn-success @endif ">全部</button></li>
                @foreach($books as $index=>$book)
                    <li>
                        <button lid="{{$book->id}}" class="btn btn-sm @if(request('lid', '') == $book->id) btn-success @endif ">{{$book->name}}</button>
                    </li>
                @endforeach
                <div style="clear:both"></div>
            </ul>
        @endif
    </div>
    <div style="margin-bottom: 10px;">
        <form action="/admin/anchor/{{$sport == 1 ? 'football' : 'basketball'}}/matches">
            <input type="hidden" name="lid" value="{{request('lid', '')}}" />
            <input style="width:200px;" name="name" value="{{request('name', '')}}" placeholder="球队名称" class="form-control form-input-css">
            <button type="submit" class="btn btn btn-success">查询</button>
        </form>
    </div>

    <div class="row placeholders">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th width="25%">比赛信息</th>
                    <th>预约信息</th>
                </tr>
                </thead>
                <tbody>
                @foreach($page as $match)
                    <tr style="text-align: left;" mid="{{$match->id}}">
                        <td>
                            <p>ID：{{$match->id}}</p>
                            <p>比赛时间：<b>{{date('Y-m-d H:i', strtotime($match['time']))}}</b></p>
                            <p>
                                <span style="color: red;">{{empty($match['lname']) ? $match['win_lname'] : $match['lname']}}</span>&nbsp;&nbsp;
                                {{$match['hname'] . ' VS ' . $match['aname']}}
                            </p>
                            <p>
                                <button name="add" class="btn btn-sm btn-primary" mid="{{$match->id}}">添加主播</button>
                            </p>
                        </td>
                        <td>
                            <?php $arms = \App\Models\QSK\Anchor\AnchorRoomMatches::getRooms($match->id, $sport); ?>
                            @foreach($arms as $arm)
                            <p>
                                <select name="room_id" class="form-control form-input-css" style="width: 360px;">
                                    <option value="">请选择主播</option>
                                    @foreach($rooms as $room_id=>$room)
                                        <option value="{{$room_id}}" @if($room_id == $arm->room_id) selected @endif >{{$room['typeCn'] . '：' . $room['roomName'] . '（' . $room['anchorName'] . '）'}}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-success form-input-css" onclick="book(this, '{{$arm->id}}', '{{$sport or 1}}')">预约</button>
                                <button class="btn btn-danger form-input-css" onclick="cancelBook(this, '{{$arm->id}}');">取消</button>
                            </p>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$page or ''}}
        </div>
    </div>
@endsection
@section("extra_content")
    <div id="book_anchor_div" style="display: none;">
        <p>
        <select name="room_id" class="form-control form-input-css" style="width: 360px;">
            <option value="">请选择主播</option>
            @foreach($rooms as $room_id=>$room)
            <option value="{{$room_id}}">{{$room['typeCn'] . '：' . $room['roomName'] . '（' . $room['anchorName'] . '）'}}</option>
            @endforeach
        </select>
        <button class="btn btn-success form-input-css" onclick="book(this, '', '{{$sport or 1}}')">预约</button>
        <button class="btn btn-danger form-input-css" onclick="cancelBook(this);">取消</button>
        </p>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });
        $("div.book button").click(function () {
            var lid = this.getAttribute('lid');
            var form = document.forms[0];
            form.lid.value = lid;
            form.submit();
        });
        $("button[name='add']").click(function () {
            var html = $("#book_anchor_div").html();
            var mid = this.getAttribute('mid');
            $('tr[mid=' + mid + '] td:last').append(html);
        });

        /**
         * 为主播预约直播的比赛
         * @param id
         * @param match_id
         * @param sport
         */
        function book(thisObj, id, sport) {
            var room_id = $(thisObj).prev().val();
            if (room_id == "") {
                alert("请选择主播直播间");
                return;
            }

            if (!confirm('是否确认预约比赛')) {
                return;
            }
            thisObj.setAttribute('disabled', 'disabled');
            var match_id = $(thisObj).parent().parent().parent().attr('mid');

            $.ajax({
                "url": "/admin/anchor/matches/book",
                "type": "post",
                "dataType": "json",
                "data": {"id": id, "match_id": match_id, "sport": sport, 'room_id': room_id},
                "success": function (json) {
                    if (json) {
                        alert(json.msg);
                        if (json.code == 200) location.reload();
                    } else {
                        alert("预约失败");
                    }
                    thisObj.removeAttribute('disabled');
                },
                "error": function () {
                    alert("预约失败");
                    thisObj.removeAttribute('disabled');
                }
            });
        }

        /**
         * 取消预约比赛
         * @param thisObj
         * @param id
         */
        function cancelBook(thisObj, id) {
            if (!confirm('是否确认取消预约直播？')) {
                return;
            }
            if (!id || id == '') {
                $(thisObj).parent().remove();
                return;
            }

            $.ajax({
                "url": "/admin/anchor/matches/cancel",
                "type": "post",
                "dataType": "json",
                "data": {"id": id},
                "success": function (json) {
                    if (json) {
                        alert(json.msg);
                        $(thisObj).parent().remove();
                    } else {
                        alert("取消预约失败");
                    }
                    thisObj.removeAttribute('disabled');
                },
                "error": function () {
                    alert("取消预约失败");
                    thisObj.removeAttribute('disabled');
                }
            });
        }
    </script>
@endsection