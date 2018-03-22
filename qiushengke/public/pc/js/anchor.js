
function setPage() {
    $('#Match .tabLine button').click(function(){
        if (!$(this).hasClass('on')) {
            $('#Match .tabLine button.on').removeClass('on');
            $('#Match ul').css('display','none');

            $(this).addClass('on');
            $('#' + $(this).val()).css('display','');
        }
    })
}
