

function setPage(){
    var Tab = $('#Tab input');
    Tab.change(function(){
        $('.content').css('display','none');
        $('#' + this.value).css('display','');

        if ((document.documentElement.scrollTop || document.body.scrollTop) > 252) {
            document.documentElement.scrollTop = 252;
        }
    })


    var BottomTab = $('.bottom input');
    BottomTab.off('click').change(function(){
        $(this).parents('.content').children('.childNode').css('display','none');
        $('#' + this.value).css('display','');
    })

    var BtnClose = $('button.close');
    BtnClose.click(function(){
        if ($(this).parents('.default').attr('close')) {
            $(this).parents('.default').removeAttr('close');
        }else{
            $(this).parents('.default').attr('close','close');
        }
    })

    var Sel = $('select');
    Sel.change(function(){
        $(this).parents('.default').children('table').css('display','none');
        $('#' + $(this).children('option:selected').val()).css('display','');
    })

    var Hale = $('.content input[value=ha],.content input[value=le]');
    Hale.change(function(){
        $(this).parents('.default').attr(this.value,this.checked?1:0)
    })

    if (window.location.hash && window.location.hash != '') {
        $('#Tab input[value="' + window.location.hash.replace('#','') + '"]').trigger('click');
    }
}

function setHead () {
    var Scroll = getPageScroll()[1];
    $('#Navigation .team').css('opacity',Scroll / 252)
    if (Scroll >= 252) {
        $('#Info').css('margin-bottom','88px');
        $('#Tab').css('position','fixed');
        $('#Tab').css('top','88px');
    }else{
        $('#Info').css('margin-bottom','');
        $('#Tab').css('position','');
        $('#Tab').css('top','');
    }
}

function setCanvas () {
    var Canvas = $('canvas');

    function Circle (value,color,obj) {
        if (value != 0) {
            var ctx = obj.getContext("2d");
            ctx.lineWidth = 4;
            ctx.beginPath();
            ctx.arc(70, 70, 80, - 0.5 * Math.PI, (2 * value - 0.5) * Math.PI, false);
            ctx.lineTo(70, 70);
            ctx.fillStyle = color;
            ctx.fill();
            ctx.strokeStyle = "white";
            ctx.closePath();
        }
    }

    for (var i = 0; i < Canvas.length; i++) {
        Circle(parseFloat(Canvas[i].getAttribute('value')),Canvas[i].getAttribute('color'),Canvas[i]);
    }
}










































