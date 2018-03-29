

function setPage () {
    $('#Navigation .tab input').click(function(){
        $('#Live, #Anchor').css('display','none');
        $('#' + $(this).val()).css('display','');
        $('body').scrollTop(0);
    })
}
