

function setPage () {
    setFilter()
}

function setFilter () {
    $('#Navigation .filter .in:not(.date, .select)').click(function(){ //唤出弹框
        if ($(this).hasClass('league')) {
            $('#LeagueFilter').css('display','');
        }else{
            $('#OddFilter').css('display','');
        }
    })

    $('#Navigation .filter .select').click(function(){ //唤出select弹框
         $("#Navigation .filter select").focus();
    })
    $("#Navigation .filter select").change(function(){
        $('#Navigation .filter .select span').html($(this).find('option:selected').text());
    })

    $('.filterBox .tab input').change(function(){ //切换栏目
        if (this.checked) {
            $(this).parents('.filterBox').find('ul').css('display','none');
            $('#' + $(this).val()).css('display','');

            $('#' + $(this).val()).scrollTop(0);

            checkChoose(this);
        }
    })

    $('#liveOnlyGoal, #liveOnlyLeague').change(function(){ //只看直播
        if ($(this).is(':checked')) {
            $('#liveOnlyLeague')[0].checked = true;
            if ($('#liveOnlyGoal')[0]) {
                $('#liveOnlyGoal')[0].checked = true;
            }
        }else{
            $('#liveOnlyLeague')[0].checked = false;
            if ($('#liveOnlyGoal')[0]) {
                $('#liveOnlyGoal')[0].checked = false;
            }
        }
    })

    $('.filterBox ul input').change(function(){
        checkChoose(this);
    })

    $('.filterBox .comfirmLine button.comfirm, .filterBox button.close').click(function(){
        $('.filterBox').css('display','none');
    })

    function checkChoose(obj) {
        if ($('#' + $(obj).parents('.filterBox').find('.tab input:checked').val()).find('input:checked').length == 0) {
            $(obj).parents('.filterBox').find('.comfirmLine button.comfirm').attr('disabled','disabled');
        }else{
            $(obj).parents('.filterBox').find('.comfirmLine button.comfirm').removeAttr('disabled');
        }
    }

    //a标签内跳转
    $('a p[href], a span[href]').click(function(){
        event.preventDefault();// 阻止浏览器默认事件，重要
        location.href = $(this).attr('href');
    })
}














