// var RootUrl = 'http://qiushengke.com/test?url=http://match.qiushengke.com/static/terminal/';
var RootUrl = 'http://match.qiushengke.com/static/terminal/';


function setDataUpdate (ID) {
    getMatchData(ID.toString());
}

function getMatchData (ID) {
    $.ajax({
        url: RootUrl + '2/' + ID.slice(0,2) + '/' + ID.slice(2,4) + '/' + ID + '/match.json',
        type: 'GET',
        dataType: 'jsonp',
        success: function (data) {
            if (data.status != 0){
                var live = $('a.live');
                if (live.length > 0){
                    live[0].style.display = '';
                }
            }

        	if (parseInt(data.status) != 0) {
        		/*
        		0-未开始
        		1-第一节、上半场
        		2-第二节
        		3-第三节、下半场
        		4-第四节
                5、6、7、8-OT1~4
        		-1-已结束
        		*/

        		//更新时间
        		if (parseInt(data.status) == 1) {
            		$('#Info .minute').html('第一节　' + data.live_time_str);
            	}else if (parseInt(data.status) == 2) {
            		$('#Info .minute').html('第二节　' + data.live_time_str);
            	}else if (parseInt(data.status) == 3) {
                    $('#Info .minute').html('第三节　' + data.live_time_str);
                }else if (parseInt(data.status) == 4) {
                    $('#Info .minute').html('第四节　' + data.live_time_str);
                }else if (parseInt(data.status) == 5) {
                    $('#Info .minute').html('OT1　' + data.live_time_str);
                }else if (parseInt(data.status) == 6) {
                    $('#Info .minute').html('OT2　' + data.live_time_str);
                }else if (parseInt(data.status) == 7) {
                    $('#Info .minute').html('OT3　' + data.live_time_str);
                }else if (parseInt(data.status) == 8) {
                    $('#Info .minute').html('OT4　' + data.live_time_str);
                }else if (parseInt(data.status) == -1) {
            		$('#Info .minute').html('已结束');
            	}

            	//更新比分
            	$('#Info .score').html('<span class="host">' + data.hscore + '</span><span class="away">' + data.ascore + '</span>');
            	$('#Navigation p.score').html(data.hscore + ' - ' + data.ascore + ((parseInt(data.status) > 0 && parseInt(data.live) == 1) ? '<span>[直播]</span>' : ''));

                $('#Event .score tbody tr:first').html('<td>' + $('#Event .score tbody tr:first td:first').html() + '</td>' + 
                                                       '<td>' + (data.hscore_1st ? data.hscore_1st : '/') + '</td>' +
                                                       '<td>' + (data.hscore_2nd ? data.hscore_2nd : '/') + '</td>' +
                                                       '<td>' + (data.hscore_3rd ? data.hscore_3rd : '/') + '</td>' +
                                                       '<td>' + (data.hscore_4th ? data.hscore_4th : '/') + '</td>' +
                                                       '<td>' + data.hscore + '</td>');

                $('#Event .score tbody tr:last').html( '<td>' + $('#Event .score tbody tr:last td:first').html() + '</td>' + 
                                                       '<td>' + (data.ascore_1st ? data.ascore_1st : '/') + '</td>' +
                                                       '<td>' + (data.ascore_2nd ? data.ascore_2nd : '/') + '</td>' +
                                                       '<td>' + (data.ascore_3rd ? data.ascore_3rd : '/') + '</td>' +
                                                       '<td>' + (data.ascore_4th ? data.ascore_4th : '/') + '</td>' +
                                                       '<td>' + data.ascore + '</td>');

                //加时相关
                if (parseInt(data.status) != -1) {
                    $('#Event .score tbody tr:first td:eq(' + parseInt(data.status) + '),#Event .score tbody tr:last td:eq(' + parseInt(data.status) + ')').addClass('now');

                    if (parseInt(data.status) >= 5) {
                        OT(data.h_ot,data.a_ot,data.status);
                    }
                }else{
                    if (data.h_ot && data.a_ot) {
                        OT(data.h_ot,data.a_ot,data.status);
                    }
                }

                function OT (Hot,Aot,Status) {
                    var Round = parseInt(Status) >= 5 ? parseInt(Status) - 4 : (Hot ? Hot.toString().split(',').length : 0);
                    for (var i = 0; i < Round; i++) {
                        $('#Event .score th:last').before('<th>OT' + (i + 1) + '</th>');
                        $('#Event .score tbody tr:first td:last').before('<td>' + (Hot && Hot.toString().split(',')[i] ? Hot.toString().split(',')[i] : 0) + '</td>');
                        $('#Event .score tbody tr:last td:last').before('<td>' + (Aot && Aot.toString().split(',')[i] ? Aot.toString().split(',')[i] : 0) + '</td>');
                    }
                }

            	//进入更新比赛数据事件
            	getEventData (ID,data.status);
            }else{
            	setTimeout(function(){
            		getMatchData (ID);
            	},60000);
            }
        }
    })    
}

function getEventData (ID,Status) {
	$.ajax({
        url: RootUrl + '2/' + ID.slice(0,2) + '/' + ID.slice(2,4) + '/' + ID + '/tech.json',
        type: 'GET',
        dataType: 'jsonp',
        success: function (data) {
        	if (data) { //数据
	      		$('#Event .technology li').html($('#Event .technology li dl.team'));
				$.each(data, function(index, value) {
					$('#Event .technology li').append('<dl><dd class="host"><p>' + (value.h.indexOf('%') == -1 ? value.h : value.h.slice(value.h.indexOf('(') + 1,value.h.indexOf('%') + 1)) + '</p><span style="width: ' + (108 * value.h_p) + 'px;"></span></dd>' + 
                            						  '<dt>' + value.name + '</dt><dd class="away"><p>' + (value.a.indexOf('%') == -1 ? value.a : value.a.slice(value.a.indexOf('(') + 1,value.a.indexOf('%') + 1)) + '</p><span style="width: ' + (108 * value.a_p) + 'px;"></span></dd></dl>');
				});
			}

			if (parseInt(Status) != -1) { //如果不是已结束，60秒请求一次
				setTimeout(function(){
            		getMatchData (ID);
            	},60000);
			}
        }
    })
}
















