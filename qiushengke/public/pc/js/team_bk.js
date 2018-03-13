
function setPage() {
    $('#Info .player button').click(function(){
        if (!$(this).hasClass('on')) {
            $('#Info .player button').removeClass('on');
            $('#Info .player table').css('display','none');

            $(this).addClass('on');
            $('#Info .player table.' + $(this).val()).css('display','');
        }
    })

    $('#This .tabLine button').click(function(){
        if (!$(this).hasClass('on')) {
            $('#This .tabLine button').removeClass('on');
            $('#This .con').css('display','none');

            $(this).addClass('on');
            $('#This .' + $(this).val()).css('display','');
        }
    })

    $('#Season .tabLine button').click(function(){
        if (!$(this).hasClass('on')) {
            $('#Season .tabLine button').removeClass('on');
            $('#Season .con').css('display','none');

            $(this).addClass('on');
            $('#Season .' + $(this).val()).css('display','');
        }
    })

    $('a.totop').click(function(){
        $("html,body").animate({scrollTop:0}, 500);
    })

    setTable();


}

function setTable() {
	function setTB (obj) {
        var TR = obj.parentNode;
        var Num = false;
        for (var i = 0; i < TR.getElementsByTagName('td').length; i++) {
            if (obj == TR.getElementsByTagName('td')[i]) {
                Num = i + 1;
                break;
            }
        }

        if (Num) {
            $(obj).parents('table').attr('choose',Num);
        }
    }

    function cleanTB (obj) {
        $('.table table').removeAttr('choose');
    }

    $('.table td').mouseover(function(){
        setTB(this);
    })
    $('.table table').mouseout(function(){
        cleanTB(this);
    })
}



