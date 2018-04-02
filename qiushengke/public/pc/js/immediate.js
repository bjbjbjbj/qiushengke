
function setPage() {
    $('#Table .even').parent('').mouseover(function(){
        getMousePos(this);
        $(this).find('dl.tbox span').each(function(){
            $(this).width($(this).attr('width'))
        })
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
    CheckChooseBtn();

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
        CheckChooseBtn();
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
        CheckChooseBtn()
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
        $('#OddFilter .bottomBar p span').html($('#OddFilter .inner ul.' + $('#OddFilter .item').attr('type') + ' button[value="1"]').length);
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
            CheckChooseBtn();
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

function Goal(Host,Away,Hscore,Ascore,Icon,Time,Type,ID) {
    var Target = {
        'ID': ID,
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

        //对应TD加底色
        var TD;
        if (Target.Type == 'host') {
            TD = $('tr[match=' + Target.ID + '] td.host, tr[match=' + Target.ID + '] td.host + td');
        }else{
            TD = $('tr[match=' + Target.ID + '] td.away, tr[match=' + Target.ID + '] td.host + td + td + td');
        }
        TD.addClass('goal');

        //播音频
        var CanPlay = false;
        if (document.getElementById('GoalAudio').canPlayType('audio/mp3') != '' || document.getElementById('GoalAudio').canPlayType('audio/wav') != '') {
            CanPlay = true;
        }
        if ($('#Control .sound button').html() != '静音' && CanPlay) {
            PlayAudio();
        }

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
        setTimeout(function(){
            TD.removeClass('goal');
        },21000)

        GoalArr.splice(0, 1);
    }
}

function PlayAudio () {
    document.getElementById("GoalAudio").play();
}

//重置底色
function setBG () {
    $('#Table tbody').each(function(){
        if (this.id == 'Top') {
            $(this).find('tr.show:even td').css('background','#f5f5f5');
            $(this).find('tr.show:odd td').css('background','#fff');
        }else{
            $(this).find('tr.show:even td').css('background','#fff');
            $(this).find('tr.show:odd td').css('background','#f5f5f5');
        }
    })
}




















