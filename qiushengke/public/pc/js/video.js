
function setPage() {
    setItemData();
    setCleanBtn();
    setChatPush();
    setTable();
    setLine();

    $('#Chatroom ul').scrollTop( $('#Chatroom ul')[0].scrollHeight);

    $('a.totop').click(function(){
        $("html,body").animate({scrollTop:0}, 500);
    })
}

function setItemData() {
    $('#Live button.open').click(function(){
        if ($('#Live .data').hasClass('show')) {
            $('#Live .data').removeClass('show');
            changeDataEM();
        }else{
            $('#Live .data').addClass('show');
            changeDataEM();
        }
    })
}

function changeDataEM() {
    if ($('#Live .data').hasClass('show')) {
        $('#Live li').each(function(){
            var hostNum = parseInt($(this).find('.host b').html());
            var awayNum = parseInt($(this).find('.away b').html());

            $(this).find('.host em').css('width', hostNum + awayNum == 0 ? 0 : (hostNum / (hostNum + awayNum) * 100).toFixed(0) + '%');
            $(this).find('.away em').css('width', hostNum + awayNum == 0 ? 0 : (awayNum / (hostNum + awayNum) * 100).toFixed(0) + '%');

        })
    }else{
        $('#Live li em').css('width','0');
    }
}


function addChat(Name,Text,Time) {
    var Li = $('<li><p class="time">' + Time + '</p><p class="name">' + Name + '</p><p class="con">' + Text + '</p></li>');
    $('#Chatroom ul').append(Li);
    $('#Chatroom ul').scrollTop( $('#Chatroom ul')[0].scrollHeight );
}

function setCleanBtn() {
    $('#Chatroom p.title button').click(function(){
        $('#Chatroom li').remove();
    })
}

function setChatPush() {
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
            $('#BKPlayer table').attr('choose',Num);
        }
    }

    function cleanTB (obj) {
        $('#BKPlayer table').removeAttr('choose');
    }

    $('#BKPlayer td').mouseover(function(){
        if (this.parentNode != $(this).parents('table').find('tr:last')[0]) {
            setTB(this);
        }
    })
    $('#BKPlayer table').mouseout(function(){
        cleanTB(this);
    })


    $('#BKPlayer .tab button').click(function(){
        if (!$(this).hasClass('on')) {
            if ($(this).parent('.host').length != 0) {
                $('#BKPlayer table.host').css('display','');
                $('#BKPlayer table.away').css('display','none');
                $('#BKPlayer .tab p.host button').addClass('on');
                $('#BKPlayer .tab p.away button').removeClass('on');
            }else{
                $('#BKPlayer table.host').css('display','none');
                $('#BKPlayer table.away').css('display','');
                $('#BKPlayer .tab p.host button').removeClass('on');
                $('#BKPlayer .tab p.away button').addClass('on');
            }
        }
    })
}

function setLine() {
    $('#Live .line a').click(function(){
        if ($(this).attr('img') && $(this).attr('img') != '') {
            $('#Anchor').css('display','');
            $('#Anchor p').html('<img src="' + $(this).attr('img') + '">' + $(this).find('span').html());
        }else{
            $('#Anchor').css('display','none');
        }
    })
}


/*即时提点*/
var Mention = [], MentionRun = false;

function AddMention(Text) {
    Mention = Mention.concat(Text);
    if (!MentionRun) {
        MentionRun = setInterval(function(){
            CheckMention();
        },5000)
    }
}

function CheckMention() {
    if (Mention.length == 0) {
        clearInterval(MentionRun);
        MentionRun = false;

        $('#Chatroom .mention').addClass('after');
        setTimeout(function(){
            $('#Chatroom .mention').remove();
        },500);
    }else{
        if ($('#Chatroom .mention').length == 0) {
            $('#Chatroom').append('<div class="mention before"><p class="tit">即时提点</p><button class="close"></button></div>');
            $('#Chatroom .mention button.close').click(function(){
                clearInterval(MentionRun);
                MentionRun = false;
                Mention = [];
                
                $('#Chatroom .mention').addClass('after');
                setTimeout(function(){
                    $('#Chatroom .mention').remove();
                },500);
            })

            setTimeout(function(){
                $('#Chatroom .mention').removeClass('before');
            },20)
        }

        var Text = $('<p class="text" type="hidden">' + Mention[0] + '</p>');
        $('#Chatroom .mention').append(Text);
        Mention.splice(0, 1);

        setTimeout(function(){
            $(Text).attr('type','show');
        },20)
        setTimeout(function(){
            $(Text).attr('type','hidden');
        },4500)
        setTimeout(function(){
            $(Text).remove()
        },5000)
    }
}
























