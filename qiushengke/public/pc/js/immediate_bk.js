
function setPage() {
    $('a.totop').click(function(){
        $("html,body").animate({scrollTop:0}, 500);
    })

    setControl();
    setDateInput();
    setFilter();
}

function setControl() {
    var Num = $('#Control').offset().top;
    window.onscroll = function () {
        // console.log($('#Control').offset().top)
        if ((document.documentElement.scrollTop || document.body.scrollTop) > Num) {
            $('#Control').attr('class','fixed');
        }else{
            $('#Control').removeAttr('class');
        }
    }
}

function setDateInput() {
    $('#Calendar input').datepicker({format:"yyyy-mm-dd",language: "zh-CN"});
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

function setFilter() {
    CheckChooseBtn();
    //按钮设置
    $('#Control button.league').click(function(){
        $('#LeagueFilter').css('display','block');
    })
    $('.filterBox button.close').click(function(){
        $('#LeagueFilter').css('display','none');
    })
    $('.filterBox button.all').click(function(){
        var Par = $(this).parents('.filterBox')[0];
        $(Par).find('ul button').val(1);
        $(Par).find('button.comfirm').removeAttr('disabled');
        CheckChooseBtn();
    })
    $('.filterBox button.opposite').click(function(){
        var Par = $(this).parents('.filterBox')[0];
        $(Par).find('ul button').each(function(){
            ClickBtn(this)
        })
        CheckChooseBtn();
    })
    $('.filterBox ul button').click(function(){
        ClickBtn(this)
    })

    function ClickBtn(obj) {
        if (obj.value == 1 || obj.value == '1') {
            obj.value = 0;
            $(obj).parents('.filterBox').find('.bottomBar p span').html(parseInt($(obj).parents('.filterBox').find('.bottomBar p span').html()) - 1)
        }else{
            obj.value = 1;
            $(obj).parents('.filterBox').find('.bottomBar p span').html(parseInt($(obj).parents('.filterBox').find('.bottomBar p span').html()) + 1)
        }

        CheckAllBtn(obj);
    }

    function CheckChooseBtn() {
        $('#LeagueFilter .bottomBar p span').html($('#LeagueFilter .inner ul button[value="1"]').length);
    }

    function CheckAllBtn(obj) {
        var Par = $(obj).parents('.filterBox')[0];
        var Btn = $(Par).find('ul button');
        Btn.each(function(){
            if (this.value == 1 || this.value == '1') {
                $(Par).find('button.comfirm').removeAttr('disabled');
                return false;
            }else if (this == Btn[Btn.length - 1]) {
                $(Par).find('button.comfirm').attr('disabled','disabled');
            }
        })
    }
}

function placeholderSupport() {
    return 'placeholder' in document.createElement('input');
}























