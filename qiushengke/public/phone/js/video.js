// var RootUrl = 'http://qiushengke.com/test?url=http://match.qiushengke.com/static/terminal/';
var RootUrl = 'http://match.qiushengke.com/static/terminal/';

function setPage () {
    $('.tab input').click(function(){
        $('#Info, #Event, #Player').css('display','none');
        $('#' + $(this).val()).css('display','');
        $('body').scrollTop(0);
    })

    $('#Info .score button').click(function(){
    	if ($('#Info .score p').css('display') == 'none') {
    		$('#Info .score p').css('display','');
    		$(this).html('隐藏比分');
    	}else{
    		$('#Info .score p').css('display','none');
    		$(this).html('显示比分');
    	}
    })
}

function setDataUpdate (Type,ID) {
	ID = ID.toString();
	if (Type == '1') {
		FootballData(ID)
	}else if (Type == '2') {
		BasketballData(ID)
	}
}

function FootballData (ID) {
	$.ajax({
        url: RootUrl + '1/' + ID.slice(0,2) + '/' + ID.slice(2,4) + '/' + ID + '/match.json',
        type: 'GET',
        dataType: 'jsonp',
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

            	//更新比分
            	$('#Info .score p').html(data.hscore + ' - ' + data.ascore);

            	//进入更新比赛数据事件
            	getFootballEventData (ID,data.status);
            }else{
            	setTimeout(function(){
            		FootballData (ID);
            	},60000);
            }
        }
    })
}

function getFootballEventData (ID,Status) {
	$.ajax({
        url: RootUrl + '1/' + ID.slice(0,2) + '/' + ID.slice(2,4) + '/' + ID + '/tech.json',
        type: 'GET',
        dataType: 'jsonp',
        success: function (data) {
        	if (data.tech) { //数据
	      		$('#Info ul li').remove();
				$.each(data.tech, function(index, value) {
					$('#Info ul').append('<li><p class="val">' + value.h + '</p><p class="line"><span style="width: ' + (value.h_p * 100) + '%;"></span></p>' +
                						 '<p class="item">' + value.name + '</p>' +
                						 '<p class="line"><span style="width: ' + (value.a_p * 100) + '%;"></span></p><p class="val">' + value.a + '</p></li>');
				});
			}

			if (data.event) { //事件
				$('#Event dd').remove();
				$.each(data.event.events, function(index, value) {
					var Inner = '';
					if (parseInt(value.kind) == 1 || parseInt(value.kind) == 7) { //1-进球，7-点球
						Inner = '<li><img src="' + CDN + '/img/icon_video_goal.png">' + value.player_name_j + (parseInt(value.kind) == 7 ? '（点球）' : '') + '</li>';
					}else if (parseInt(value.kind) == 8) { //8-乌龙
						Inner = '<li><img src="' + CDN + '/img/icon_video_own.png">' + value.player_name_j + '（乌龙）</li>';
					}else if (parseInt(value.kind) == 2 || parseInt(value.kind) == 9) { //2-红牌, 9-两黄一红
						Inner = '<li><img src="' + CDN + '/img/icon_video_red.png">' + value.player_name_j + (parseInt(value.kind) == 9 ? '（两黄一红）' : '') + '</li>';
					}else if (parseInt(value.kind) == 3) { //3-黄牌
						Inner = '<li><img src="' + CDN + '/img/icon_video_yellow.png">' + value.player_name_j + '</li>';
					}else if (parseInt(value.kind) == 11) { //11-换人
						Inner = '<li><img src="' + CDN + '/img/icon_video_up.png">' + value.player_name_j + '换上</li>' + 
								'<li><img src="' + CDN + '/img/icon_video_down.png">' + value.player_name_j2 + '换下</li>';
					}
					$('#Event dt.end').after('<dd class="' + (parseInt(value.is_home) == 1 ? 'host' : 'away') + '"><p class="minute">' + value.happen_time + '<span>' + 
													"'</span></p><ul>" + Inner + '</ul></dd>');
				});
			}

			if (parseInt(Status) != -1) { //如果不是已结束，60秒请求一次
				setTimeout(function(){
            		FootballData (ID);
            	},60000);
			}
        }
    })
}


function BasketballData (ID) {
	$.ajax({
        url: RootUrl + '2/' + ID.slice(0,2) + '/' + ID.slice(2,4) + '/' + ID + '/match.json',
        type: 'GET',
        dataType: 'jsonp',
        success: function (data) {
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

            	//更新比分
            	$('#Info .score p').html(data.hscore + ' - ' + data.ascore);

            	//进入更新比赛数据事件
            	getBasketBallEventData (ID,data.status);
            }else{
            	setTimeout(function(){
            		BasketballData (ID);
            	},60000);
            }
        }
    })
}

function getBasketBallEventData (ID,Status) {
	$.ajax({
        url: RootUrl + '2/' + ID.slice(0,2) + '/' + ID.slice(2,4) + '/' + ID + '/tech.json',
        type: 'GET',
        dataType: 'jsonp',
        success: function (data) {
        	if (data) { //数据
	      		$('#Info ul li').remove();
				$.each(data, function(index, value) {
					$('#Info ul').append('<li><p class="val">' + (value.h.indexOf('%') == -1 ? value.h : value.h.slice(value.h.indexOf('(') + 1,value.h.indexOf('%') + 1)) + '</p><p class="line"><span style="width: ' + (value.h_p * 100) + '%;"></span></p>' +
                						 '<p class="item">' + value.name + '</p>' +
                						 '<p class="line"><span style="width: ' + (value.a_p * 100) + '%;"></span></p><p class="val">' + (value.a.indexOf('%') == -1 ? value.a : value.a.slice(value.a.indexOf('(') + 1,value.a.indexOf('%') + 1)) + '</p></li>');
				});
			}

			//进入更新球员数据
            getPlayerEventData (ID,data.status);
        }
    })
}

function getPlayerEventData (ID,Status) {
	$.ajax({
        url: RootUrl + '2/' + ID.slice(0,2) + '/' + ID.slice(2,4) + '/' + ID + '/player.json',
        type: 'GET',
        dataType: 'jsonp',
        success: function (data) {
        	if (data.home) { //主队数据
	      		$('#Player .list dl:first dd, #Player .list table:first tbody tr').remove();
	      		$.each(data.home, function(index, value) {
	      			if (value.name) {
						$('#Player .list dl:first').append('<dd>' + value.name + '</dd>');
						$('#Player .list table:first tbody').append('<tr><td>' + (value.location == 'G' ? '后卫' : (value.location == 'F' ? '前锋' : '中锋')) + '</td>' + 
																	'<td>' + value.pts + '</td><td>' + value.fg + '</td><td>' + value['3pt'] + '</td><td>' + value.ft + '</td><td>' + value.tot + '</td>' + 
																	"<td>" + value.ast + "</td><td>" + value.pf + "</td><td>" + value.stl + "</td><td>" + value.to + "</td><td>" + value.blk + "</td><td>" + value.min + "'</td></tr>");
					}
				});
			}

			if (data.away) { //客队数据
	      		$('#Player .list dl:last dd, #Player .list table:last tbody tr').remove();
	      		$.each(data.away, function(index, value) {
	      			if (value.name) {
						$('#Player .list dl:last').append('<dd>' + value.name + '</dd>');
						$('#Player .list table:last tbody').append('<tr><td>' + (value.location == 'G' ? '后卫' : (value.location == 'F' ? '前锋' : '中锋')) + '</td>' + 
																	'<td>' + value.pts + '</td><td>' + value.fg + '</td><td>' + value['3pt'] + '</td><td>' + value.ft + '</td><td>' + value.tot + '</td>' + 
																	"<td>" + value.ast + "</td><td>" + value.pf + "</td><td>" + value.stl + "</td><td>" + value.to + "</td><td>" + value.blk + "</td><td>" + value.min + "'</td></tr>");
					}
				});
			}

			if (parseInt(Status) != -1) { //如果不是已结束，60秒请求一次
				setTimeout(function(){
            		BasketballData (ID);
            	},60000);
			}
        }
    })
}

