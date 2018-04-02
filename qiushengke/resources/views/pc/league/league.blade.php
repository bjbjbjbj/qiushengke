@extends('pc.layout.league_base')
@section('league_link')
    @component('pc.cell.top_leagues',['links'=>$footLeagues])
    @endcomponent
    @endsection
@section('league_content')
    <?php
    $lis = '';
    for($i= 0 ; $i < $season['total_round'] ; $i++){
        if($season['curr_round'] == $i+1)
            $lis = $lis . '<li class="on">'.($i + 1).'</li>';
        else
            $lis = $lis . '<li>'.($i + 1).'</li>';
    }

    for ($i = 0 ; $i < 16 - $season['total_round']%16 ; $i++){
        $lis = $lis . '<li>-</li>';
    }
    ?>
    <div class="lbox" id="Match"><!--联赛-->
        <div class="title">
            <p>赛程赛果</p>
        </div>
        <div class="con">
            <ul>
                {!! $lis !!}
            </ul>
            @foreach($schedule as $key=>$matches)
                @component('pc.cell.league_list_table',['num'=>$key,'matches'=>$matches,'show'=>($key == $season['curr_round'])])
                @endcomponent
            @endforeach
        </div>
    </div>
    <div class="lbox" id="Rank"><!--联赛-->
        <div class="title">
            <p>积分榜</p>
        </div>
        <div class="con">
            <table>
                <colgroup>
                    <col num="1" width="40px">
                    <col num="2">
                    <col num="3" width="5.2%">
                    <col num="4" width="5.2%">
                    <col num="5" width="5.2%">
                    <col num="6" width="5.2%">
                    <col num="7" width="5.2%">
                    <col num="8" width="5.2%">
                    <col num="9" width="5.2%">
                    <col num="10" width="50px">
                    <col num="11" width="50px">
                    <col num="12" width="50px">
                    <col num="13" width="5.2%">
                    <col num="14" width="100px">
                </colgroup>
                <thead>
                <tr>
                    <th>排名</th>
                    <th>球队</th>
                    <th>赛</th>
                    <th>胜</th>
                    <th>平</th>
                    <th>负</th>
                    <th>得</th>
                    <th>失</th>
                    <th>净</th>
                    <th>胜%</th>
                    <th>平%</th>
                    <th>负%</th>
                    <th>积分</th>
                    <th>近六轮</th>
                </tr>
                </thead>
                <tbody>
                @foreach($score as $item)
                    @component('pc.cell.league_list_score',['score'=>$item])
                    @endcomponent
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @if(isset($articles) && count($articles) > 0)
        <div class="lbox" id="News">
            <div class="title">
                <p>赛事资讯</p>
            </div>
            <div class="con">
                <ul>
                    @foreach($articles as $article)
                        <a target="_blank" class="li" href="{{$article['link']}}">
                            <p><img alt="{{$article['title']}}" src="{{$article['cover']}}">{{$article['title']}}</p>
                        </a>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
@endsection