<p>
    <select name="room_id" class="form-control form-input-css" style="width: 360px;">
        <option value="">请选择主播</option>
        @foreach($rooms as $room_id=>$room)
            <option value="{{$room_id}}" @if(isset($arm) && $room_id == $arm->room_id) selected @endif >{{$room['typeCn'] . '：' . $room['roomName'] . '（' . $room['anchorName'] . '）'}}</option>
        @endforeach
    </select>
    <input class="form-control form-input-css" name="od" placeholder="排序" style="width: 100px;" value="{{$arm->od or ''}}">
    <button class="btn btn-success form-input-css" onclick="book(this, '{{$arm->id or ''}}', '{{$sport or 1}}')">预约</button>
    <button class="btn btn-danger form-input-css" onclick="cancelBook(this, '{{$arm->id or ''}}');">取消</button>
</p>