
function setPage() {
    setPlayer();
    setBattle();
    setHistory();
    setTotalAsia();
    setTotalGoal();
    setTotalAverage();
    setTotalTotal();
    setTotalDifference();
    setTab();

    $('a.totop').click(function(){
        $("html,body").animate({scrollTop:0}, 500);
    })
}

function setPlayer(){
    //底色
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
        $('#Match .player table').removeAttr('choose');
    }

    $('#Match .player td').mouseover(function(){
        setTB(this);
    })
    $('#Match .player table').mouseout(function(){
        cleanTB(this);
    })
}

function setBattle () {

    //设置切换
    $('#Data .battle .cbox button[name=ha], #Data .battle .cbox button[name=ma]').click(function(){
        if ($(this).hasClass('on')) {
            $('#Data .battle').attr($(this).attr('name'),0);
            $(this).removeClass('on');
        }else{
            $('#Data .battle').attr($(this).attr('name'),1);
            $(this).addClass('on');
        }
    })
    $('#Data .battle .cbox button[name=number]').click(function(){
        if (!$(this).hasClass('on')) {
            $('#Data .battle .cbox button[name=number]').removeClass('on');
            $('#Data .battle .con dl').css('display','none');

            $(this).addClass('on');
            $('#Data .battle .con dl[num="' + $(this).val() + '"]').css('display','');

            if ($(this).val() == '5' || $(this).val() == 5) {
                $('#Data .battle table').each(function(){
                    $(this).find('tbody tr:gt(4)').css('display','none');
                })
            }else{
                $('#Data .battle table tr').css('display','');
            }
        }
    })

    //底色
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
        $('#Data .battle table').removeAttr('choose');
    }

    $('#Data .battle td').mouseover(function(){
        setTB(this);
    })
    $('#Data .battle table').mouseout(function(){
        cleanTB(this);
    })

}

function setHistory () {
    //设置切换
    $('#Data .history .cbox button[name=ha], #Data .history .cbox button[name=ma]').click(function(){
        if ($(this).hasClass('on')) {
            $('#Data .history').attr($(this).attr('name'),0);
            $(this).removeClass('on');
        }else{
            $('#Data .history').attr($(this).attr('name'),1);
            $(this).addClass('on');
        }
    })
    $('#Data .history .cbox button[name=number]').click(function(){
        if (!$(this).hasClass('on')) {
            $('#Data .history .cbox button[name=number]').removeClass('on');
            $('#Data .history .con dl').css('display','none');

            $(this).addClass('on');
            $('#Data .history .con dl[num="' + $(this).val() + '"]').css('display','');

            if ($(this).val() == '5' || $(this).val() == 5) {
                $('#Data .history table').each(function(){
                    $(this).find('tbody tr:gt(4)').css('display','none');
                })
            }else{
                $('#Data .history table tr').css('display','');
            }
        }
    })
    $('#Data .history .team button').click(function(){
        if (!$(this).hasClass('on')) {
            if ($(this).hasClass('host')) {
                $('#Data .history .team button.away').removeClass('on');
                $('#Data .history div.con').each(function(){
                    if ($(this).hasClass('host')) {
                        $(this).css('display','');
                    }else{
                        $(this).css('display','none');
                    }
                })
            }else{
                $('#Data .history .team button.host').removeClass('on');
                $('#Data .history div.con').each(function(){
                    if ($(this).hasClass('away')) {
                        $(this).css('display','');
                    }else{
                        $(this).css('display','none');
                    }
                })
            }

            $(this).addClass('on');
        }
    })

    //底色
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
        $('#Data .history table').removeAttr('choose');
    }

    $('#Data .history td').mouseover(function(){
        setTB(this);
    })
    $('#Data .history table').mouseout(function(){
        cleanTB(this);
    })
}

function setTotalAsia() {
    $('#Total .asia .fh button').click(function(){
        if (!$(this).hasClass('on')) {
            var Part = $(this).parents('.part');
            Part.find('.fh button').removeClass('on');
            Part.find('table.full, table.away').css('display','none');
            Part.find('table.' + ($(this).hasClass('full') ? 'full' : 'away')).css('display','');
            $(this).addClass('on');
        }
    })
}

function setTotalGoal() {
    $('#Total .goal .fh button').click(function(){
        if (!$(this).hasClass('on')) {
            var Part = $(this).parents('.part');
            Part.find('.fh button').removeClass('on');
            Part.find('table.full, table.away').css('display','none');
            Part.find('table.' + ($(this).hasClass('full') ? 'full' : 'away')).css('display','');
            $(this).addClass('on');
        }
    })
}

function setTotalAverage() {
    $('#Total .average .num button').click(function(){
        if (!$(this).hasClass('on')) {
            var Part = $(this).parents('.part');
            Part.find('.num button').removeClass('on');
            Part.find('table').css('display','none');
            Part.find('table[num="' + $(this).val() + '"]').css('display','');
            $(this).addClass('on');
        }
    })
}

function setTotalTotal() {
    $('#Total .total .num button').click(function(){
        if (!$(this).hasClass('on')) {
            var Part = $(this).parents('.part');
            Part.find('.num button').removeClass('on');
            Part.find('table').css('display','none');
            Part.find('table[num="' + $(this).val() + '"]').css('display','');
            $(this).addClass('on');
        }
    })
}

function setTotalDifference() {
    $('#Total .difference .num button').click(function(){
        if (!$(this).hasClass('on')) {
            var Part = $(this).parents('.part');
            Part.find('.num button').removeClass('on');
            Part.find('table').css('display','none');
            Part.find('table[num="' + $(this).val() + '"]').css('display','');
            $(this).addClass('on');
        }
    })
}

function setTab() {
    $('#Play li').click(function(){
        if (!$(this).hasClass('on')) {
            if ((document.documentElement.scrollTop || document.body.scrollTop) > 350) {
                $("html,body").animate({scrollTop:350}, 0);
            }

            $('#Play li').removeClass('on');
            $('#Match, #Data, #Total').css('display','none');

            $(this).addClass('on');
            $('#' + $(this).attr('target')).css('display','');
        }
    })

    window.onscroll = function () {
        // console.log($('#Control').offset().top)
        if ((document.documentElement.scrollTop || document.body.scrollTop) > 330) {
            $('#Play').addClass('fixed')
        }else{
            $('#Play').removeClass('fixed')
        }
    }

}






