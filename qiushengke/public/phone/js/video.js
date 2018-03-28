

function setPage () {
    $('.tab input').click(function(){
        $('#Info, #Event, #Player').css('display','none');
        $('#' + $(this).val()).css('display','');
        $('body').scrollTop(0);
    })
}
