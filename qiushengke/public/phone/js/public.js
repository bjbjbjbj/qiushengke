//屏幕比例调整
function changeWindow () {
    var win_W = window.screen.width;
    var win_Dpr = 2; //不实用window.devicePixelRatio，因为有很多奇葩分辨率手机;
    if (win_Dpr * win_W >= 2000) {
        document.querySelector('meta[name="viewport"]').setAttribute('content','width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no');
    }else if (win_Dpr * win_W >= 1200) {
        document.querySelector('meta[name="viewport"]').setAttribute('content','width=device-width, initial-scale=0.75, maximum-scale=0.75, minimum-scale=0.75, user-scalable=no');
    }else if (win_Dpr * win_W < 650) {
        document.querySelector('meta[name="viewport"]').setAttribute('content','width=device-width, initial-scale=0.45, maximum-scale=0.45, minimum-scale=0.45, user-scalable=no');
    }else{
        document.querySelector('meta[name="viewport"]').setAttribute('content','width=device-width, initial-scale=0.5, maximum-scale=0.5, minimum-scale=0.5, user-scalable=no');
    }
}

changeWindow()

window.addEventListener('resize', function() {
    changeWindow()
}, false);

//获取链点参数
function GetQueryString(str,href) {
    var Href;
    if (href != undefined && href != '') {
        Href = href;
    }else{
        Href = location.href;
    };
    var rs = new RegExp("([\?&])(" + str + ")=([^&#]*)(&|$|#)", "gi").exec(Href);
    if (rs) {
        return decodeURI(rs[3]);
    } else {
        return '';
    }
}

//获取日期
function getTime(/** timestamp=0 **/) {
    var ts = arguments[0] || 0;
    var t, y, m, d, h, i, s;
    t = ts ? new Date(ts) : new Date();
    y = t.getFullYear();
    m = t.getMonth() + 1;
    d = t.getDate();
    h = t.getHours();
    i = t.getMinutes();
    s = t.getSeconds();
    // 可根据需要在这里定义时间格式
    return y + '-' + (m < 10 ? '0' + m : m) + '-' + (d < 10 ? '0' + d : d);
}

//获取滚动距离
function getPageScroll() {
  var xScroll, yScroll;
  if (self.pageYOffset) {
    yScroll = self.pageYOffset;
    xScroll = self.pageXOffset;
  } else if (document.documentElement && document.documentElement.scrollTop) { // Explorer 6 Strict
    yScroll = document.documentElement.scrollTop;
    xScroll = document.documentElement.scrollLeft;
  } else if (document.body) {// all other Explorers
    yScroll = document.body.scrollTop;
    xScroll = document.body.scrollLeft;  
  }
  arrayPageScroll = new Array(xScroll,yScroll);
  return arrayPageScroll;
};

//状态弹框
function Alert (type,text) { //type:loading\success\error，text为提示内容
    var AlertBox;
    if (document.getElementById('Alert') == undefined) {
        AlertBox = document.createElement('div');
        AlertBox.id = 'Alert';
        AlertBox.setAttribute('status','loading');
        AlertBox.innerHTML = '<div class="loading">-</div><div class="success">-</div><div class="error">-</div>';
        document.body.appendChild(AlertBox);
    }else{
        AlertBox = document.getElementById('Alert');
    };
    AlertBox.setAttribute('status',type);
    AlertBox.getElementsByClassName(type)[0].innerHTML = text;
    if (type != 'loading') {
        setTimeout('document.body.removeChild(document.getElementById("Alert"))',1500);
    };
}

function closeLoading () {
    document.body.removeChild(document.getElementById('Alert'));
}

//确认弹框
function ComfirmAlert (content,Event,Title,canText,comText) { //content为提示文案，Event为确认事件
    if (document.getElementById('ComfirmBox') == undefined) {
        if (canText == undefined) {
            canText = '取消';
        };
        if (comText == undefined) {
            comText = '确认';
        };
        var Box = document.createElement('div');
        Box.id = 'ComfirmBox';
        Box.innerHTML = '<div class="default"><div class="title">' + Title + '</div>' +
                        '<div class="comText">' + content + '</div>' +
                        '<div class="btn"><button onclick="ComfirmAlert()">' + canText + '</button><button class="comfirm">' + comText + '</button></div>';

        if (Event != '') {
            Box.getElementsByTagName('button')[1].setAttribute('onclick',Event);
        }else{
            Box.getElementsByClassName('btn')[0].removeChild(Box.getElementsByTagName('button')[1]);
        }

        document.body.appendChild(Box);
    }else{
        document.body.removeChild(document.getElementById('ComfirmBox'));
    }
}

//支付弹框
function PayCode (Cost,Num,Img) { //价格、单号、二维码图片
    if (document.getElementById('payCode') == undefined) {
        var CodeDiv = document.createElement('div');
        CodeDiv.id = 'payCode';
        CodeDiv.innerHTML = '<div class="default"><div class="title">微信扫一扫付款<button onclick="document.body.removeChild(this.parentNode.parentNode.parentNode)"></button></div>' +
                            '<img src="' + Img + '">' +
                            '<p class="name">料狗商城消费</p>' +
                            '<p class="cost">￥' + parseFloat(Cost).toFixed(2) + '</p>' +
                            '<p class="code">订单号：' + Num + '</p></div>';
        document.body.appendChild(CodeDiv);
    }else{
        return;
    }
}

//翻页到页底
function ScrollBottom (Even) {
    var ClientHeight,BodyHeight,ScrollTop;
    if(document.compatMode == "CSS1Compat"){
        ClientHeight = document.documentElement.clientHeight;
    }else{
        ClientHeight = document.body.clientHeight;
    }

    BodyHeight = document.body.offsetHeight;

    ScrollTop = document.body.scrollTop;

    if (BodyHeight - ScrollTop - ClientHeight < 20) {
        inTheEnd()
    };
}


//新推荐时触发弹框
//对象例子
//Parr = {
//  "id":123,
//  "title":"湿胸很帅",
//  "host":"皇家马德里",
//  "away":"巴塞罗那",
//  "league":"西甲",
//  "play":"大小球",
//  "time":"02-10"
//}
function NewPush (Parr) { //直接传比赛对象数组
    var Box = document.createElement('div');
    Box.id = 'ComfirmBox';

    Box.innerHTML = '<div class="default"><div class="title">新的套餐比赛推荐</div>' +
                    '<div class="comText">' + Parr.title + '</div>' +
                    '<div class="matchInfo"><p class="play">[亚盘]</p><p class="league">英超</p><p class="team">切尔西</p><p class="vs">&nbsp;vs&nbsp;</p><p class="team">西布朗</p></div>' +
                    '<div class="btn"><button onclick="ComfirmAlert()">我知道了</button><button class="comfirm">立即查看</button></div>';
                
    Box.getElementsByTagName('button')[1].setAttribute('onclick',"location.href='recommend.html?id=" + Parr.id + "'");

    document.body.appendChild(Box);
}

//添加代理提示
function AddAgent () {
    var Agent = document.createElement('div');
    Agent.id = 'Agent';
    Agent.innerHTML = '<p>这是你的代理分享页面，你可以将页面分享出去，用户通过分享的页面进行注册购买，你将获得相应的代理提成！</p><button onclick="DelAgent()">我知道了</button>';
    document.body.appendChild(Agent);
}
function DelAgent () {
    var NowTime = new Date();
    localStorage.setItem('delAgentTime',NowTime.getTime());
    document.body.removeChild(document.getElementById("Agent"))
}

//APP加载时适配
function AppLoad () {
    var Nav = navigator.userAgent;
    if (Nav.indexOf('LiaoGou') != -1 && document.getElementById('Navigation').getElementsByClassName('banner').length != 0) {
        document.getElementById('Navigation').style.minHeight = 0;
        document.getElementById('Navigation').getElementsByClassName('banner')[0].style.display = 'none';
        if (document.getElementsByClassName('home').length != 0) {
            document.getElementsByClassName('home')[0].style.display = 'none';
        };
        document.body.setAttribute('style','position: relative; top: -88px;');
    };
}
// AppLoad ()

//增加banner
//数组理应有三个对象
// var BannerArr = [
//     {
//         'id': '123',
//         'type:': 'article', //推荐
//         'face': 'https://img.liaogou168.com/images/lg_58fc92e37fcf9.jpg',
//         'content': '【辉常好球】芬超推荐8连红！数据可查，今日继续冲击红单！'
//     },
//     {
//         'id': '123',
//         'type': 'package', //套餐
//         'face': 'https://img.liaogou168.com/images/lg_58dba684225d5.jpg',
//         'content': '过绿！苏宁客场能否全身而退？'
//     },
//     {
//         'id': '123',
//         'type': 'merchant', //专家
//         'face': 'https://img.liaogou168.com/images/lg_5948ccc81c64b.jpg',
//         'content': '今日最后一推，挪超两没落冠军之争！'
//     }
// ]
function SetBanner (BannerArr) {
    if (!document.getElementById('Banner') && BannerArr.length == 3) {
        var Banner = document.createElement('div');
        Banner.id = 'Banner';
        Banner.setAttribute('type',0);
        Banner.className = 'show';
        Banner.innerHTML = '<div class="inner"><img src="https://shop.liaogou168.com/img/customer2/icon_face.png" class="icon"><p class="liaogou">让红单更简单</p><a href="https://app.liaogou168.com/appopen">打开</a></div>' + 
                           '<dl><dd></dd><dd></dd><dd></dd><dd></dd></dl>';

        for (var i = 0; i < BannerArr.length; i++) {
            var Inner = document.createElement('div');
            Inner.className = 'inner';
            Inner.innerHTML = '<img src="' + BannerArr[i].face + '"><p>' + BannerArr[i].content + '</p><a href="https://app.liaogou168.com/' + BannerArr[i].type + '/detail/' + BannerArr[i].id + '">打开</a>';
            Banner.insertBefore(Inner,Banner.getElementsByTagName('dl')[0]);
        };

        document.body.insertBefore(Banner,document.getElementById('Navigation'));
        if (document.getElementById('Search') && (document.getElementById('Search').tagName == 'A' || document.getElementById('Search').tagName == 'a')) {
            document.getElementById('Search').style.top = '120px';
        };

        setInterval('BannerRun()',3500);

        document.addEventListener('scroll', function (event) {
            if (document.getElementById('Banner')) {
                var Scroll = getPageScroll()[1];
                if (Scroll > 100) {
                    DelBanner ()
                }else{
                    AddBanner ()
                }
            }
        }, false)
    }
}

function AddBanner () {
    document.getElementById('Banner').className = 'show';

    if (document.getElementById('Search') && (document.getElementById('Search').tagName == 'A' || document.getElementById('Search').tagName == 'a')) {
        document.getElementById('Search').style.top = '120px';
    };
}
function DelBanner () {
    document.getElementById('Banner').className = 'hidden';

    if (document.getElementById('Search') && (document.getElementById('Search').tagName == 'A' || document.getElementById('Search').tagName == 'a')) {
        document.getElementById('Search').style.top = '0';
    };
}
function BannerRun() {
    var BannerNumber = parseInt(document.getElementById('Banner').getAttribute('type'));
    document.getElementById('Banner').setAttribute('type',(BannerNumber + 1)%4)
}
// SetBanner(BannerArr)

var AttArr = [
    {   
        'id': 1,
        'name': '最爱大小球',
        'face': 'img/customer2/icon_face.png',
        'bingo': '近3天10中7',
        'profit': '789%'
    },
    {   
        'id': 2,
        'name': '最爱大小球',
        'face': 'img/customer2/icon_face.png',
        'bingo': '近3天10中7',
        'profit': '789%'
    },
    {   
        'id': 3,
        'name': '最爱大小球',
        'face': 'img/customer2/icon_face.png',
        'bingo': '近3天10中7',
        'profit': '789%'
    },
    {   
        'id': 4,
        'name': '最爱大小球',
        'face': 'img/customer2/icon_face.png',
        'bingo': '近3天10中7',
        'profit': '789%'
    }
]
function ShowAtt (AttArr) {
    //避免样式错误，先加载css
    var link = document.createElement("link");
    link.type = "text/css";
    link.rel = "stylesheet";
    link.href = 'css/customer2/matList.css';
    document.getElementsByTagName("head")[0].appendChild(link);

    //插入内容
    var Att = document.createElement('div');
    Att.id = 'recTalent';

    Att.innerHTML = '<div class="default matchList"><div class="title">推荐关注<button class="del" onclick="document.body.removeChild(this.parentNode.parentNode.parentNode)"></button></div>' + 
                    '<div class="btn"><button class="comfirm">一键关注</button><button class="change">换一换</button></div></div>';


    for (var i = 0; i < AttArr.length; i++) {
        var Talent = document.createElement('div');
        Talent.className = 'matchEv';
        Talent.innerHTML = '<div class="info"><img src="' + AttArr[i].face + '"><p class="name">' + AttArr[i].name + '<span class="profit">' + AttArr[i].bingo + '</span></p>' + 
                           '<p class="slogan">3天盈利<span>' + AttArr[i].profit + '</span></p>' +
                           '<input type="checkbox" name="recTalent" id="recTalent_' + AttArr[i].id + '"><label for="recTalent_' + AttArr[i].id + '"></label></div>';

        Att.getElementsByClassName('matchList')[0].insertBefore(Talent,Att.getElementsByClassName('btn')[0]);
    }

    document.body.appendChild(Att);

    Att.addEventListener('touchmove', function(event) {
        // 如果这个元素的位置内只有一个手指的话
        if (event.targetTouches.length == 1) {
            event.preventDefault();// 阻止浏览器默认事件，重要 
        }
    }, false);
}
