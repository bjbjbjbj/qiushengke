@extends('phone.layout.base')
@section('body')
    style="padding-top: 88px;"
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/phone/css/league.css">
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/phone/css/immediate.css">
@endsection
@section('content')
    <div id="Navigation">
        <div class="banner">{{$league['name']}}（{{$key}}组）</div>
    </div>
    <div id="Group">
        <?php
        $stage = null;
        foreach ($stages as $item){
            if (isset($item['groupMatch'])){
                $stage = $item;
                break;
            }
        }
        $matches = array();
        if (isset($stage)){
            $item = $stage['groupMatch'][$key];
            $matches = $item['matches'];
        }
        ?>
        @if(isset($stage))
            <table>
                <tbody>
                <tr>
                    <th>{{$key}}组</th>
                    <th>球队</th>
                    <th>赛</th>
                    <th>胜/平/负</th>
                    <th>得/失</th>
                    <th>净</th>
                    <th>积</th>
                </tr>
                <?php
                $scores = $item['scores'];
                ?>
                @for($i = 0 ; $i < count($scores) ; $i++)
                    <?php
                    $score = $scores[$i];
                    ?>
                    <tr>
                        <td><span>{{$i+1}}</span></td>
                        <td><a href="team.html">{{$score['tname']}}</a></td>
                        <td>{{$score['count']}}</td>
                        <td>{{$score['win']}}/{{$score['draw']}}/{{$score['lose']}}</td>
                        <td>{{$score['goal']}}/{{$score['fumble']}}</td>
                        <td>{{$score['goal'] - $score['fumble']}}</td>
                        <td>{{$score['score']}}</td>
                    </tr>
                @endfor
                </tbody>
            </table>
        @endif
    </div>
    <div id="List">
        @if(isset($stage))
            @foreach($matches as $match)
                @component('phone.cell.league_match_list_cell',['match'=>$match,'sport'=>$sport,'cdn'=>$cdn,'round'=>1,'isCurr'=>1])
                @endcomponent
            @endforeach
        @endif
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{$cdn}}/phone/js/league.js"></script>
@endsection