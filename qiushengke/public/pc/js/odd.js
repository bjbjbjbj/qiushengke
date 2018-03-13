
function setPage() {
    setTable();
    setTab();

    $('a.totop').click(function(){
        $("html,body").animate({scrollTop:0}, 500);
    })
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
        $('#Odd table').removeAttr('choose');
    }

    $('#Odd table td').mouseover(function(){
        setTB(this);
    })
    $('#Odd table').mouseout(function(){
        cleanTB(this);
    })
}

function setTab() {
    $('#Play li').click(function(){
        if (!$(this).hasClass('on')) {
            $('#Play li').removeClass('on');
            $('#Odd table').css('display','none');
            $('#' + $(this).attr('target')).css('display','');
            $(this).addClass('on');
        }
    })
}


