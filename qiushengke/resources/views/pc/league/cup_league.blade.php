@extends('pc.layout.league_base')
@section('league_content')
    <div class="lbox" id="Cup">
        <ul><!-- on-表示当前选中，now-表示赛事进行中的轮次，last-最后一个li-->
            <?php
            $width = count($stages)>0?100/count($stages):0;
            ?>
            @for($i = 0 ; $i < count($stages) ; $i++)
                <?php
                $stage = $stages[$i];
                ?>
                <li
                        @if($stage['status'] == 1)
                        class="now on"
                        @endif
                        style="width: {{$width}}%">
                    <p class="name">{{$stage['name']}}</p>
                    <p class="round">{{$i + 1}}</p>
                </li>
            @endfor
        </ul>
        @for($i = 0 ; $i < count($stages) ; $i++)
            <?php
            $stage = $stages[$i];
            ?>
                @if(array_key_exists('matches',$stage))
                    @component('pc.cell.cup_league_list_table',['stage'=>$stage,'num'=>($i+1),'show'=>($stage['status'] == 1)])
                    @endcomponent
                @elseif(array_key_exists('groupMatch',$stage))
                    @component('pc.cell.cup_league_list_table_group',['stage'=>$stage,'num'=>($i+1),'show'=>($stage['status'] == 1)])
                    @endcomponent
                @elseif(array_key_exists('combo',$stage))
                    @component('pc.cell.cup_league_list_table_combo',['stage'=>$stage,'num'=>($i+1),'show'=>($stage['status'] == 1)])
                    @endcomponent
                @endif
        @endfor
    </div>
@endsection