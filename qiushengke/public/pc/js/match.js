
function setPage() {
    setPredictionCanvas();
    setSameOdd();
    setAttack();
    setBattle();
    setHistory();
    setCorData();
    setCorBattle();
    setCorHistory();
    setTab();

    $('a.totop').click(function(){
        $("html,body").animate({scrollTop:0}, 500);
    })
}

function setPredictionCanvas () {
    $('#Prediction canvas').each(function(){
        Circle($(this).attr('value'),$(this).attr('color'),this);
    });

    function Circle (Value,Color,obj) {
        if (Value != 0) {
            var ctx = obj.getContext("2d");
            ctx.lineWidth = 4;
            ctx.beginPath();
            ctx.arc(57, 57, 60, - 0.5 * Math.PI, (2 * (Value / 100) - 0.5) * Math.PI, false);
            ctx.lineTo(57, 57);
            ctx.fillStyle = Color;
            ctx.fill();
            ctx.strokeStyle = "white";
            ctx.closePath();
            if (Value != 100) {
                ctx.stroke();
            };
        }
    }

    //顺路设置一下历史同赔
    $('#Info .sameOdd span').click(function(){
        $('#Play li').removeClass('on');
        $('#Match, #Character, #Data, #Corner').css('display','none');
        $('#Play li[target=Character]').addClass('on');
        $('#Character').css('display','');

        $("html,body").animate({scrollTop:$('#Character .sameOdd').offset().top}, 0);
    })
}

function setSameOdd () {
    $('#Character .sameOdd .tabBox button').click(function(){
        if (!$(this).hasClass('on')) {
            $('#Character .sameOdd .tabBox button').removeClass('on');
            $('#Character .sameOdd .con').css('display','none');

            $(this).addClass('on')
            $('#Character .sameOdd .' + $(this).val()).css('display','block');
        }
    });

    $('#Character .sameOdd .con button').click(function(){
        if (!$(this).hasClass('on')) {
            $('#Character .sameOdd .con button').removeClass('on')
            $('#Character .sameOdd .con .result').css('display','none');

            $('#Character .sameOdd .con button[value="' + $(this).val() + '"]').addClass('on');
            $('#Character .sameOdd .con .result[num="' + $(this).val() + '"]').css('display','block');

            if ($(this).val() == 5 || $(this).val() == '5') {
                $('#Character .sameOdd .asia tbody tr:gt(4), #Character .sameOdd .goal tbody tr:gt(4), #Character .sameOdd .europe tbody tr:gt(4)').css('display','none');
            }else{
                $('#Character .sameOdd .con tbody tr').css('display','');
            }
        }
    })

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
        $('#Character .sameOdd table').removeAttr('choose');
    }

    $('#Character .sameOdd td').mouseover(function(){
        setTB(this);
    })
    $('#Character .sameOdd table').mouseout(function(){
        cleanTB(this);
    })
}

function setAttack () {
    $('#Data .attack button').click(function(){
        if (!$(this).hasClass('on')) {
            $('#Data .attack button').removeClass('on');
            $('#Data .attack table').css('display','none');

            $(this).addClass('on');
            $('#Data .attack table[num="' + $(this).val() + '"]').css('display','');
        }
    })
}

function setBattle () {

    //设置canvas
    $('#Data .battle canvas').each(function(){
        Circle(parseInt($(this).attr('win')),parseInt($(this).attr('draw')),parseInt($(this).attr('lose')),this);
    });

    function Circle (Win,Draw,Lose,obj) {
        var All = Win + Draw + Lose;
        if (All != 0) {
            var ctx = obj.getContext("2d");
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.arc(45, 45, 50, - 0.5 * Math.PI, (2 * (Win / All) - 0.5) * Math.PI, false);
            ctx.lineTo(45, 45);
            ctx.fillStyle = "#2b9968";
            ctx.fill();
            ctx.strokeStyle = "white";
            ctx.closePath();
            if (Draw != All && Lose != All && Win != All) {
                ctx.stroke();
            };

            // ctx.lineWidth = 4;
            ctx.beginPath();
            ctx.arc(45, 45, 50, Math.PI / 180 * (Win / All) * 360 - 0.5 * Math.PI, (2 * ((Win + Draw) / All) - 0.5) * Math.PI, false);
            ctx.lineTo(45, 45);
            ctx.fillStyle = "#dbdbdb";
            ctx.fill();
            // ctx.strokeStyle = "white";
            ctx.closePath();
            if (Draw != All && Lose != All && Win != All) {
                ctx.stroke();
            };

            // ctx.lineWidth = 4;
            ctx.beginPath();
            ctx.arc(45, 45, 50, Math.PI / 180 * ((Win + Draw) / All) * 360 - 0.5 * Math.PI, 1.5 * Math.PI, false);
            ctx.lineTo(45, 45);
            ctx.fillStyle = $(obj).parents('dl').hasClass('goal') ? "#fabd36" : "#1661c7";
            ctx.fill();
            // ctx.strokeStyle = "white";
            ctx.closePath();

            if (Draw != All && Lose != All && Win != All) {
                ctx.stroke();
            };
        }
    }

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
            $('#Data .battle .canBox').css('display','none');

            $(this).addClass('on');
            $('#Data .battle .canBox[num="' + $(this).val() + '"]').css('display','');

            if ($(this).val() == '5' || $(this).val() == 5) {
                $('#Data .battle table').each(function(){
                    $(this).find('tbody tr:gt(4)').css('display','none');
                })
            }else{
                $('#Data .battle table tr').css('display','');
            }
        }
    })
    $('#Data .battle select').change(function(){
        $(this).parents('table').find('td.' + $(this).attr('class') + ' p.start, td.' + $(this).attr('class') + ' p.end').css('display','none');
        $(this).parents('.con').find('.canBox dl.' + $(this).attr('class')).css('display','none');

        $(this).parents('table').find('td.' + $(this).attr('class') + ' p.' + $(this).val()).css('display','');
        $(this).parents('.con').find('.canBox dl.' + $(this).attr('class') + '.' + $(this).val()).css('display','');
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
            $('#Data .history svg[num]').css('display','none');

            $(this).addClass('on');
            $('#Data .history svg[num="' + $(this).val() + '"]').css('display','');
            $('#Data .history .svgBox').attr('num',$(this).val())

            if ($(this).val() == '5' || $(this).val() == 5) {
                $('#Data .history table').each(function(){
                    $(this).find('tbody tr:gt(4)').css('display','none');
                })
            }else{
                $('#Data .history table tr').css('display','');
            }
        }
    })
    $('#Data .history select').change(function(){
        $(this).parents('table').find('td.' + $(this).attr('class') + ' p.start, td.' + $(this).attr('class') + ' p.end').css('display','none');
        $(this).parents('.con').find('.canBox dl.' + $(this).attr('class')).css('display','none');

        $(this).parents('table').find('td.' + $(this).attr('class') + ' p.' + $(this).val()).css('display','');
        $(this).parents('.con').find('.canBox dl.' + $(this).attr('class') + '.' + $(this).val()).css('display','');
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

function setCorData () {
    $('#Corner .data .tabBox button').click(function(){
        if (!$(this).hasClass('on')) {
            $('#Corner .data table').css('display','none');
            $('#Corner .data .tabBox button').removeClass('on');

            $('#Corner .data table[num="' + $(this).val() + '"]').css('display','');
            $(this).addClass('on')
        }
    })
}

function setCorBattle() {
    //设置切换
    $('#Corner .battle .cbox button[name=ha], #Corner .battle .cbox button[name=ma]').click(function(){
        if ($(this).hasClass('on')) {
            $('#Corner .battle').attr($(this).attr('name'),0);
            $(this).removeClass('on');
        }else{
            $('#Corner .battle').attr($(this).attr('name'),1);
            $(this).addClass('on');
        }
    })
    $('#Corner .battle .cbox button[name=number]').click(function(){
        if (!$(this).hasClass('on')) {
            $('#Corner .battle .cbox button[name=number]').removeClass('on');
            $('#Corner .battle .con dl').css('display','none');

            $(this).addClass('on');
            $('#Corner .battle .con dl[num="' + $(this).val() + '"]').css('display','');

            if ($(this).val() == '5' || $(this).val() == 5) {
                $('#Corner .battle table').each(function(){
                    $(this).find('tbody tr:gt(4)').css('display','none');
                })
            }else{
                $('#Corner .battle table tr').css('display','');
            }
        }
    })
}

function setCorHistory () {
    //设置切换
    $('#Corner .history .cbox button[name=ha], #Corner .history .cbox button[name=ma]').click(function(){
        if ($(this).hasClass('on')) {
            $('#Corner .history').attr($(this).attr('name'),0);
            $(this).removeClass('on');
        }else{
            $('#Corner .history').attr($(this).attr('name'),1);
            $(this).addClass('on');
        }
    })
    $('#Corner .history .cbox button[name=number]').click(function(){
        if (!$(this).hasClass('on')) {
            $('#Corner .history .cbox button[name=number]').removeClass('on');
            $('#Corner .history .con dl').css('display','none');

            $(this).addClass('on');
            $('#Corner .history .con dl[num="' + $(this).val() + '"]').css('display','');

            if ($(this).val() == '5' || $(this).val() == 5) {
                $('#Corner .history table').each(function(){
                    $(this).find('tbody tr:gt(4)').css('display','none');
                })
            }else{
                $('#Corner .history table tr').css('display','');
            }
        }
    })
    $('#Corner .history .team button').click(function(){
        if (!$(this).hasClass('on')) {
            if ($(this).hasClass('host')) {
                $('#Corner .history .team button.away').removeClass('on');
                $('#Corner .history div.con').each(function(){
                    if ($(this).hasClass('host')) {
                        $(this).css('display','');
                    }else{
                        $(this).css('display','none');
                    }
                })
            }else{
                $('#Corner .history .team button.host').removeClass('on');
                $('#Corner .history div.con').each(function(){
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
}

function setTab() {
    $('#Play li').click(function(){
        if (!$(this).hasClass('on')) {
            if ((document.documentElement.scrollTop || document.body.scrollTop) > 560) {
                $("html,body").animate({scrollTop:560}, 0);
            }

            $('#Play li').removeClass('on');
            $('#Match, #Character, #Data, #Corner').css('display','none');

            $(this).addClass('on');
            $('#' + $(this).attr('target')).css('display','');
        }
    })

    // window.onscroll = function () {
    //     // console.log($('#Control').offset().top)
    //     if ((document.documentElement.scrollTop || document.body.scrollTop) > 560) {
    //         $('#Play').addClass('fixed')
    //     }else{
    //         $('#Play').removeClass('fixed')
    //     }
    // }

}






