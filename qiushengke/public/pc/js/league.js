
function setPage() {
    setMatch();
    setCup();
    setDateInput();
    setRank();
    setTable();





    $('#Info button.open').click(function(){
        if ($('#Info').hasClass('open')) {
            $('#Info').removeClass('open');
        }else{
            $('#Info').addClass('open');
        }
    })

    $('a.totop').click(function(){
        $("html,body").animate({scrollTop:0}, 500);
    })
}


function setMatch() {
    $('#Match ul li').click(function(){
        if ($(this).html() != '-') {
            $('#Match table').css('display','none');
            $('#Match table[num="' + $(this).html() + '"]').css('display','');
            $('#Match ul li').removeClass('on');
            $(this).addClass('on');
        }
    })
}

function setCup() {
    $('#Cup p.round').click(function(){
        $('#Cup .con').css('display','none');
        $('#Cup .con[num="' + parseInt($(this).html()) + '"]').css('display','');
        $('#Cup li').removeClass('on');
        $(this).parents('li').addClass('on');
    })

    $('#Group .tabLine button').click(function(){
        $('#Group .tabLine button').removeClass('on');
        $(this).addClass('on');
        $('#Group .item').css('display','none');
        $('#Group .item[type=' + $(this).val() + ']').css('display','');
    })
}

function setRank() {
    $('#Rank .tabLine button').click(function(){
        $('#Rank .tabLine button').removeClass('on');
        $('#Rank .con').css('display','none');
        $(this).addClass('on');
        $('#Rank .con[type=' + $(this).val() + ']').css('display','block');
    })
}

function setDateInput() {
    $('#Match .inbox').mouseover(function(){
        $('#Match .inbox input')[0].focus();
    }).mouseout(function(){
        $('#Match .inbox input')[0].blur();
    })
    $('#Match .inbox input').datepicker({format:"yyyy-mm-dd",language: "zh-CN"});
    if(!placeholderSupport()){   // 判断浏览器是否支持 placeholder
        $('[placeholder]').focus(function() {
            var input = $(this);
            if (input.val() == input.attr('placeholder')) {
                input.val('');
                input.removeClass('placeholder');
            }
        }).blur(function() {
            var input = $(this);
            if (input.val() == '' || input.val() == input.attr('placeholder')) {
                input.addClass('placeholder');
                input.val(input.attr('placeholder'));
            }
        }).blur();
    };
}
function placeholderSupport() {
    return 'placeholder' in document.createElement('input');
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
        $('#Rank table').removeAttr('choose');
    }

    $('#Rank td').mouseover(function(){
        setTB(this);
    })
    $('#Rank table').mouseout(function(){
        cleanTB(this);
    })
}












