// var RootUrl = 'http://qiushengke.com/test?url=http://match.qiushengke.com/static/terminal/';
var RootUrl = 'http://match.qiushengke.com/static/terminal/';


function setDataUpdate (ID) {
    getMatchData(ID.toString());
}

function getMatchData (ID) {
    $.ajax({
        url: RootUrl + '1/' + ID.slice(0,2) + '/' + ID.slice(2,4) + '/' + ID + '/match.json',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
        	if (parseInt(data.status) != 0) {
        		/*
        		0-未开始
        		1-上半场
        		2-中场
        		3-下半场
        		4-加时
        		-1-已结束
        		*/

        		//更新时间
        		if (parseInt(data.status) == 2) {
            		$('#Info .minute').html('中场');
            	}else if (parseInt(data.status) == 4) {
            		$('#Info .minute').html('加时');
            	}else if (parseInt(data.status) == -1) {
            		$('#Info .minute').html('已结束');
            	}else{
            		var MatchTime = data.timehalf > 0 ? Date.parse(new Date())/1000 - data.timehalf : Date.parse(new Date())/1000 - data.time;
            		MatchTime = Math.floor(MatchTime / 60);
            		MatchTime = data.status == 3 && MatchTime < 45 ? MatchTime + 45 : MatchTime;
            		$('#Info .minute').html(MatchTime + "<span>'</span>");
            	}

            	//更新比分
            	$('#Info .score').html('<span class="host">' + data.hscore + '</span><span class="away">' + data.ascore + '</span>');
            	$('#Navigation p.score').html(data.hscore + ' - ' + data.ascore + (parseInt(data.live) == 1 ? '<span>[直播]</span>' : ''));

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
        url: RootUrl + '1/' + ID.slice(0,2) + '/' + ID.slice(2,4) + '/' + ID + '/tech.json',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
        	if (data.tech) { //数据
	      		$('#Event .technology li').html($('#Event .technology li dl.team'));
				$.each(data.tech, function(index, value) {
					$('#Event .technology li').append('<dl><dd class="host"><p>' + value.h + '</p><span style="width: ' + (108 * value.h_p) + 'px;"></span></dd>' + 
                            						  '<dt>' + value.name + '</dt><dd class="away"><p>' + value.a + '</p><span style="width: ' + (108 * value.a_p) + 'px;"></span></dd></dl>');
				});
			}

			if (data.event) { //事件
				$('#Event .event dd').remove();
				$.each(data.event.events, function(index, value) {
					var Inner = '';
					if (parseInt(value.kind) == 1 || parseInt(value.kind) == 7) { //1-进球，7-点球
						Inner = '<li><img src="img/icon_video_goal.png">' + value.player_name_j + (parseInt(value.kind) == 7 ? '（点球）' : '') + '</li>';
					}else if (parseInt(value.kind) == 8) { //8-乌龙
						Inner = '<li><img src="img/icon_video_own.png">' + value.player_name_j + '（乌龙）</li>';
					}else if (parseInt(value.kind) == 2 || parseInt(value.kind) == 9) { //2-红牌, 9-两黄一红
						Inner = '<li><img src="img/icon_video_red.png">' + value.player_name_j + (parseInt(value.kind) == 9 ? '（两黄一红）' : '') + '</li>';
					}else if (parseInt(value.kind) == 3) { //3-黄牌
						Inner = '<li><img src="img/icon_video_yellow.png">' + value.player_name_j + '</li>';
					}else if (parseInt(value.kind) == 11) { //11-换人
						Inner = '<li><img src="img/icon_video_up.png">' + value.player_name_j + '换上</li>' + 
								'<li><img src="img/icon_video_down.png">' + value.player_name_j2 + '换下</li>';
					}
					$('#Event .event dt.end').after('<dd class="' + (parseInt(value.is_home) == 1 ? 'host' : 'away') + '"><p class="minute">' + value.happen_time + '<span>' + 
													"'</span></p><ul>" + Inner + '</ul></dd>');
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
















