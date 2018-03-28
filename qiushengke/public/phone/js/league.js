

function setPage () {
    $('#Navigation .tab input').click(function(){
        $('#List, #Rank, #Group, #Playoffs').css('display','none');
        $('#' + $(this).val()).css('display','');
        $('body').scrollTop(0);
    })

    //a标签内跳转
    $('a p[href], a span[href]').click(function(){
        event.preventDefault();// 阻止浏览器默认事件，重要
        location.href = $(this).attr('href');
    })
}
