
function setPage() {
    $('#Table .even').parent('').mouseover(function(){
        getMousePos(this);
        $(this).find('dl.tbox span').css('width',$(this).find('dl.tbox span').attr('width'));
    })
    $('#Table .even').parent('').mouseout(function(){
        $(this).find('dl.tbox span').removeAttr('style');
    })
    $('#Table .odd').parent('').mouseover(function(){
        getMousePos(this);
    })

    $('a.totop').click(function(){
        $("html,body").animate({scrollTop:0}, 500);
    })

    $('#Simulation table').css('width',$('#Table').width());
    $(window).resize(function() {
        $('#Simulation table').css('width',$('#Table').width());
    });

    setControl();
    setSound();
    setDateInput();
    setFilter();
}


function setControl() {
    var Num = $('#Control').offset().top;
    window.onscroll = function () {
        // console.log($('#Control').offset().top)
        if ((document.documentElement.scrollTop || document.body.scrollTop) > Num) {
            $('#Control').attr('class','fixed');
            $('#Simulation').attr('class','fixed');
            $('#Date ul').attr('class','fixed');
        }else{
            $('#Control').removeAttr('class');
            $('#Simulation').removeAttr('class');
            $('#Date ul').removeAttr('class');
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
    //按钮设置
    $('#Control button.league').click(function(){
        $('#LeagueFilter').css('display','block');
    })
    $('#Control button.odd').click(function(){
        $('#OddFilter').css('display','block');
    })
    $('.filterBox button.close').click(function(){
        $('#LeagueFilter').css('display','none');
        $('#OddFilter').css('display','none');
    })
    $('.filterBox button.all').click(function(){
        var Par = $(this).parents('.filterBox')[0];
        if (Par.id == 'LeagueFilter') {
            $(Par).find('ul button').val(1);
        }else{
            $(Par).find('ul.' + $(Par).find('.item').attr('type') + ' button').val(1);
        }
        $(Par).find('button.comfirm').removeAttr('disabled');
    })
    $('.filterBox button.opposite').click(function(){
        var Par = $(this).parents('.filterBox')[0];
        if (Par.id == 'LeagueFilter') {
            $(Par).find('ul button').each(function(){
                ClickBtn(this)
            })
        }else{
            $(Par).find('ul.' + $(Par).find('.item').attr('type') + ' button').each(function(){
                ClickBtn(this)
            })
        }
    })
    $('.filterBox ul button').click(function(){
        ClickBtn(this)
    })

    function ClickBtn(obj) {
        if (obj.value == 1 || obj.value == '1') {
            obj.value = 0;
        }else{
            obj.value = 1;
        }

        CheckAllBtn(obj);
    }

    function CheckAllBtn(obj) {
        var Par = $(obj).parents('.filterBox')[0];
        var Btn;
        if (Par.id == 'LeagueFilter') {
            Btn = $(Par).find('ul button');
        }else{
            Btn = $(Par).find('ul.' + $(Par).find('.item').attr('type') + ' button');
        }
        Btn.each(function(){
            if (this.value == 1 || this.value == '1') {
                $(Par).find('button.comfirm').removeAttr('disabled');
                return false;
            }else if (this == Btn[Btn.length - 1]) {
                $(Par).find('button.comfirm').attr('disabled','disabled');
            }
        })
    }

    //Odd切换
    $('#OddFilter button.odd').click(function(){
        if (this.value == 0 || this.value == '0') {
            $('#OddFilter .item').scrollTop(0);
            if ($(this).hasClass('asia')) {
                $('#OddFilter .item').attr('type','asia');
                $(this).val(1);
                $('#OddFilter button.goal').val(0);
            }else{
                $('#OddFilter .item').attr('type','goal');
                $(this).val(1);
                $('#OddFilter button.asia').val(0);
            }

            CheckAllBtn(this)
        }
    })
}

function placeholderSupport() {
    return 'placeholder' in document.createElement('input');
}





function setSound() {
    $('#Control .sound').click(function(){
        if ($(this).find('ul.show').length != 0) {
            $(this).find('ul.show').removeAttr('class');
        }else{
            $(this).find('ul').attr('class','show');
        }
    })

    $('#Control .sound li').click(function(){
        if (this.className != 'on') {
            $('#Control .sound li.on').removeAttr('class');
            this.className = 'on';

            $('#Control .sound button').html(this.innerHTML)
        }
    })
}

function getMousePos(obj,event) {
    var Height = window.screen.availHeight;
    var e = event || window.event || arguments.callee.caller.arguments[0];
    var scrollY = document.documentElement.scrollTop || document.body.scrollTop;
    var y = e.clientY;

    if (y > Height / 2) {
        $(obj).find('.even').addClass('top');
        $(obj).find('.odd').addClass('top');
    }else{
        $(obj).find('.even').removeClass('top');
        $(obj).find('.odd').removeClass('top');
    }
}

//进球弹层
var GoalArr = [], GoalAdd = false;

function Goal(Host,Away,Hscore,Ascore,Icon,Time,Type) {
    var Target = {
        'Host': Host,
        'Away': Away,
        'Hscore': Hscore,
        'Ascore': Ascore,
        'Icon': Icon,
        'Time': Time,
        'Type': Type
    }

    GoalArr = GoalArr.concat(Target);

    if (!GoalAdd) {
        GoalAdd = setInterval(function(){
            CheckGoal();
        },800);
    }
}

function CheckGoal() {
    if (GoalArr.length == 0) {
        clearInterval(GoalAdd);
        GoalAdd = false;
    }else{

        if ($('#GoalUl').length == 0) {
            $('body').append('<div id="GoalUl"><ul></ul><button class="close"></button></div>');
            $('#GoalUl button.close').click(function(){
                clearInterval(GoalAdd);
                GoalAdd = false;
                GoalArr = [];
                
                $('#GoalUl').addClass('close');
                setTimeout(function(){
                    $('#GoalUl').remove();
                },550)
            })
        }

        var Target = GoalArr[0];

        var Li = $('<li class="' + Target.Type + '"><div class="icon"><img src="' + Target.Icon + '"></div>' + 
                   '<p class="team host">' + (Target.Type=='host'?Target.Time + "'":'') + '<span>' + Target.Host + '</span></p>' + 
                   '<p class="score"><span class="host">' + Target.Hscore + '</span>-<span class="away">' + Target.Ascore + '</span></p>' + 
                   '<p class="team away">' + (Target.Type=='away'?Target.Time + "'":'') + '<span>' + Target.Away + '</span></p></li>');

        $('#GoalUl ul').append(Li);

        GoalArr.splice(0, 1);

        setTimeout(function(){
            $(Li).addClass('show');
        },20)
        setTimeout(function(){
            $(Li).addClass('close');
        },7000)
        setTimeout(function(){
            $(Li).addClass('height');
        },7300)
        setTimeout(function(){
            $(Li).remove();
            if ($('#GoalUl li').length == 0) {
                $('#GoalUl').remove();
            }
        },7800)
    }
}























