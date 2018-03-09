<div class="Link">
    @foreach($links as $link)
        <a href="{{$link['url']}}">{{$link['name']}}</a>
    @endforeach
</div>