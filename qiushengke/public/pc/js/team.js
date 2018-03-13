
function setPage() {
    $('#Features button').click(function(){
        if (!$(this).hasClass('on')) {
            $('#Features button').removeClass('on');
            $('#Features dd').css('display','none');

            $(this).addClass('on');
            $('#Features dd.' + $(this).val()).css('display','block');
        }
    })

    $('a.totop').click(function(){
        $("html,body").animate({scrollTop:0}, 500);
    })
}

