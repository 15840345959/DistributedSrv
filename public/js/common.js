// 接口部分
//基本的ajax访问后端接口类
function ajaxRequest(url, param, method, callBack) {
    console.log("url:" + url + " method:" + method + " param:" + JSON.stringify(param));
    //web类路由必须有crsf_token，如果参数中传入_token，则设定X=CSRF-TOKEN
    if (param._token != undefined) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': param._token,
            }
        });
    }
    console.log("param:" + JSON.stringify(param));
    //ajax访问后台
    $.ajax({
        type: method,  //提交方式
        url: url,//路径
        data: param,//数据，这里使用的是Json格式进行传输
        // contentType: "application/json; charset=utf-8",
        contentType: "application/x-www-form-urlencoded",
        dataType: "json",
        success: function (ret) {//返回数据根据结果进行相应的处理
            consoledebug.log("ret:" + JSON.stringify(ret));
            callBack(ret)
        },
        error: function (err) {
            console.log(JSON.stringify(err));
            console.log("responseText:" + err.responseText);
            callBack(err)
        }
    });
}

//是否输出打印信息的开关，为true 时输出打印信息
var DEBUG = true;

var consoledebug = (DEBUG) ? console : new nodebug();

function nodebug() {
}

nodebug.prototype.log = function (str) {
}
nodebug.prototype.warn = function (str) {
}

//基础信息/////////////////////////////////////////////////////////////////////
//设置管理员状态
function admin_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/admin/setStatus/" + param.id, param, "GET", callBack);
}

//设置用户状态
function user_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/user/setStatus/" + param.id, param, "GET", callBack);
}

//设置用户状态
function user_setType(url, param, callBack) {
    ajaxRequest(url + "admin/user/setType/" + param.id, param, "GET", callBack);
}

//删除广告图
function ad_del(url, param, callBack) {
    ajaxRequest(url + "admin/ad/del/" + param.id, param, "GET", callBack);
}

//设置广告图状态
function ad_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/ad/setStatus/" + param.id, param, "GET", callBack);
}

//设置作品状态
function article_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/article/setStatus/" + param.id, param, "GET", callBack);
}

//设置评论状态
function comment_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/comment/setStatus/" + param.id, param, "GET", callBack);
}

//获取作品分类列表
function articleType_getListByCon(url, param, callBack) {
    ajaxRequest(url + "api/articleType/getListByCon", param, "GET", callBack);
}

//设置作品状态
function articleType_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/articleType/setStatus/" + param.id, param, "GET", callBack);
}

//设置规则状态
function rule_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/rule/setStatus/" + param.id, param, "GET", callBack);
}

//获取商品分类列表
function goodsType_getListByCon(url, param, callBack) {
    ajaxRequest(url + "api/goodsType/getListByCon", param, "GET", callBack);
}

//设置商品状态
function goods_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/goods/setStatus/" + param.id, param, "GET", callBack);
}

//设置商品分类状态
function goodsType_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/goodsType/setStatus/" + param.id, param, "GET", callBack);
}

//设置物流配置信息
function logisticsSetting_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/logisticsSetting/setStatus/" + param.id, param, "GET", callBack);
}

/////////////////////////////////////////////////////////////////////////////////////////

///公众号相关////////////////////////////////////////////////////////////////////////////////////////

//删除公众号素材
function material_del(url, param, callBack) {
    ajaxRequest(url + "admin/gzh/material/del/" + param.id, param, "GET", callBack);
}

//删除公众号自动回复
function reply_del(url, param, callBack) {
    ajaxRequest(url + "admin/gzh/reply/del/" + param.id, param, "GET", callBack);
}

//删除公众号业务话术
function busiWord_del(url, param, callBack) {
    ajaxRequest(url + "admin/gzh/busiWord/del/" + param.id, param, "GET", callBack);
}

//删除菜单子项目
function menu_del(url, param, callBack) {
    ajaxRequest(url + "admin/gzh/menu/del/" + param.id, param, "GET", callBack);
}

//生成菜单项目
function menu_create(url, param, callBack) {
    ajaxRequest(url + "admin/gzh/menu/create", param, "GET", callBack);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////

////投票相关//////////////////////////////////////////////////////////////////////////

//设置投票礼品状态
function vote_voteGift_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/vote/voteGift/setStatus/" + param.id, param, "GET", callBack);
}

//设置地推团队状态
function vote_voteTeam_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/vote/voteTeam/setStatus/" + param.id, param, "GET", callBack);
}

//设置参赛选手状态
function vote_voteUser_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/vote/voteUser/setStatus/" + param.id, param, "GET", callBack);
}

//审核报名信息
function vote_voteUser_setAuditStatus(url, param, callBack) {
    ajaxRequest(url + "admin/vote/voteUser/setAuditStatus/" + param.id, param, "GET", callBack);
}

//设置投票大赛状态
function vote_voteActivity_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/vote/voteActivity/setStatus/" + param.id, param, "GET", callBack);
}

//复制投票大赛
function vote_voteActivity_copy(url, param, callBack) {
    ajaxRequest(url + "admin/vote/voteActivity/copy", param, "GET", callBack);
}

//设置投票广告状态
function vote_voteAD_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/vote/voteAD/setStatus/" + param.id, param, "GET", callBack);
}

//设置投诉状态
function vote_voteComplain_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/vote/voteComplain/setStatus/" + param.id, param, "GET", callBack);
}

//设置大赛规则状态
function vote_VoteRule_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/vote/voteRule/setStatus/" + param.id, param, "GET", callBack);
}

//设置订单状态
function voteOrder_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/vote/voteOrder/setStatus/" + param.id, param, "GET", callBack);
}

// 地推团队接口 //////////////////////////////////////////////////////////////////////////////////////////////////
//复制投票大赛
function vote_team_activity_copy(url, param, callBack) {
    ajaxRequest(url, param, "GET", callBack);
}

//设置投票大赛状态
function vote_team_activity_setStatus(url, param, callBack) {
    ajaxRequest(url, param, "GET", callBack);
}

//设置参赛选手状态
function vote_team_voteUser_setStatus(url, param, callBack) {
    ajaxRequest(url, param, "GET", callBack);
}

//审核报名信息
function vote_team_voteUser_setAuditStatus(url, param, callBack) {
    ajaxRequest(url, param, "GET", callBack);
}

// 地推团队接口 //////////////////////////////////////////////////////////////////////////////////////////////////


//设置规则状态
function message_setStatus(url, param, callBack) {
    ajaxRequest(url, param, "GET", callBack);
}

//////////////////////////////////////////////////////////////////////////////////////////////////


////每日一画//////////////////////////////////////////////////////////////////////////

//设置广告状态
function mryh_mryhAD_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/mryh/mryhAD/setStatus/" + param.id, param, "GET", callBack);
}

//设置活动状态
function mryh_mryhGame_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/mryh/mryhGame/setStatus/" + param.id, param, "GET", callBack);
}

//复制活动
function mryh_mryhGame_copy(url, param, callBack) {
    ajaxRequest(url + "admin/mryh/mryhGame/copy", param, "GET", callBack);
}

//设置配置状态
function mryh_mryhSetting_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/mryh/mryhSetting/setStatus/" + param.id, param, "GET", callBack);
}

//设置优惠券状态
function mryh_mryhCoupon_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/mryh/mryhCoupon/setStatus/" + param.id, param, "GET", callBack);
}

//////////////////////////////////////////////////////////////////////////////////////////////////

//////艺术榜//////////////////////////////////////////////////////////////////////////////////

//设置广告状态
function ysb_ysbAD_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/ysb/ysbAD/setStatus/" + param.id, param, "GET", callBack);
}

//////////////////////////////////////////////////////////////////////////////////////////////////


//////小艺商城//////////////////////////////////////////////////////////////////////////////////

//设置订单状态
function shopOrder_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/shop/shopOrder/setStatus/" + param.id, param, "GET", callBack);
}

//设置广告状态
function shop_shopAD_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/shop/shopAD/setStatus/" + param.id, param, "GET", callBack);
}


//////////////////////////////////////////////////////////////////////////////////////////////////


//////营销活动//////////////////////////////////////////////////////////////////////////////////
function yxhd_yxhdActivity_setStatus(url, param, callBack) {
    ajaxRequest(url + "admin/yxhd/yxhdActivity/setStatus/" + param.id, param, "GET", callBack);
}


//////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * 校验手机号js
 *
 * By TerryQi
 */

function isPoneAvailable(phone_num) {
    var myreg = /^[1][3,4,5,7,8][0-9]{9}$/;
    if (!myreg.test(phone_num)) {
        return false;
    } else {
        return true;
    }
}

// 判断参数是否为空
function judgeIsNullStr(val) {
    if (val == null || val == "" || val == undefined || val == "未设置") {
        return true
    }
    return false
}

// 判断参数是否为空
function judgeIsAnyNullStr() {
    if (arguments.length > 0) {
        for (var i = 0; i < arguments.length; i++) {
            if (!isArray(arguments[i])) {
                if (arguments[i] == null || arguments[i] == "" || arguments[i] == undefined || arguments[i] == "未设置" || arguments[i] == "undefined") {
                    return true
                }
            }
        }
    }
    return false
}

// 判断数组时候为空, 服务于 judgeIsAnyNullStr 方法
function isArray(object) {
    return Object.prototype.toString.call(object) == '[object Array]';
}


// 七牛云图片裁剪
function qiniuUrlTool(img_url, type) {
    //如果不是七牛的头像，则直接返回图片
    //consoledebug.log("img_url:" + img_url + " indexOf('isart.me'):" + img_url.indexOf('isart.me'));
    if (img_url.indexOf('7xku37.com') < 0 && img_url.indexOf('isart.me') < 0) {
        return img_url;
    }
    //七牛链接
    var qn_img_url;
    const size_w_500_h_200 = '?imageView2/2/w/500/h/200/interlace/1/q/75|imageslim'
    const size_w_200_h_200 = '?imageView2/2/w/200/h/200/interlace/1/q/75|imageslim'
    const size_w_500_h_300 = '?imageView2/2/w/500/h/300/interlace/1/q/75|imageslim'
    const size_w_500_h_250 = '?imageView2/2/w/500/h/250/interlace/1/q/75|imageslim'

    const size_w_500 = '?imageView1/1/w/500/interlace/1/q/75'

    //除去参数
    if (img_url.indexOf("?") >= 0) {
        img_url = img_url.split('?')[0]
    }
    //封装七牛链接
    switch (type) {
        case "ad":  //广告图片
            qn_img_url = img_url + size_w_500_h_300
            break
        case "folder_list":  //作品列表图片样式
            qn_img_url = img_url + size_w_500_h_200
            break;
        case "folder_list_500":  //作品列表
            qn_img_url = img_url + size_w_500_h_300
            break;
        case  'head_icon':      //头像信息
            qn_img_url = img_url + size_w_200_h_200
            break
        case  'work_detail':      //作品详情的图片信息
            qn_img_url = img_url + size_w_500
            break
        default:
            qn_img_url = img_url
            break
    }
    return qn_img_url
}


// 文字转html，主要是进行换行转换
function Text2Html(str) {
    if (str == null) {
        return "";
    } else if (str.length == 0) {
        return "";
    }
    str = str.replace(/\r\n/g, "<br>")
    str = str.replace(/\n/g, "<br>");
    return str;
}

//null变为空str
function nullToEmptyStr(str) {
    if (judgeIsNullStr(str)) {
        str = "";
    }
    return str;
}


/*
 * 获取url中get的参数
 *
 * By TerryQi
 *
 * 2017-12-23
 *
 */
function getQueryString(name) {
    var reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i');
    var r = window.location.search.substr(1).match(reg);
    if (r != null) {
        return unescape(r[2]);
    }
    return null;
}

/*
 * 根据input文件路径，获取文件名称
 *
 * By TerryQi
 *
 * 2018-07-05
 *
 */

//获取文件名称
function getFileName(path) {
    var pos1 = path.lastIndexOf('/');
    var pos2 = path.lastIndexOf('\\');
    var pos = Math.max(pos1, pos2);
    if (pos < 0) {
        return path;
    }
    else {
        return path.substring(pos + 1);
    }
}


//在数组中找到对象
/*
 * arr为对象数组 其中id为键值
 *
 * By TerryQi
 *
 * 2018-07-31
 */
function getObjInArrById(id, arr) {
    for (var i = 0; i < arr.length; i++) {
        if (id == arr[i].id) {
            return arr[i]
        }
    }
    return null;
}


/*
 * 判断dom元素是否存在
 * 
 * 根据id判断dom元素是否存在
 * 
 * By TerryQi
 * 
 * 2018-09-03
 * 
 */
function is_dom_exist(dom_id) {
    if ($("#" + dom_id).length > 0) {
        return true;
    }
    else {
        return false;
    }
}


//描绘趋势图
/*
 * chart_dom_id：chart的id，例如user_chart
 *
 * data：数据，一般为数组
 *
 * legend_str：标题，例如 新增用户
 *
 */
function loadLineChart(chart_dom_id, data, legend_str, unit_str) {
    //获取线性图标
    var lineChart = echarts.init(document.getElementById(chart_dom_id))
    lineChart.showLoading({
        type: 'default'
    })
    var date_array = [];
    var value_array = [];
    //配置数据
    for (var i = 0; i < data.length; i++) {
        date_array.push(data[i].date)
        value_array.push(data[i].value)
    }
    var setOption = {
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'cross',
                crossStyle: {
                    color: '#999'
                }
            }
        },
        legend: {
            data: [legend_str]
        },
        xAxis: [
            {
                type: 'category',
                data: date_array,
                axisPointer: {
                    type: 'shadow'
                }
            }
        ],
        yAxis: [
            {
                type: 'value',
                name: legend_str,
                axisLabel: {
                    formatter: '{value} ' + unit_str
                },
                minInterval: 1
            }
        ],
        series: [
            {
                name: legend_str,
                type: 'line',
                data: value_array
            }
        ]
    }
    lineChart.setOption(setOption)
    lineChart.hideLoading()

    return lineChart;
}


//描绘双曲线
/*
 * chart_dom_id：chart的id，例如user_chart
 *
 * data1、data2：数据，一般为数组
 *
 * legend_str1、legend_str2：标题，例如 新增用户
 *
 * unit_str1、unit_str2：单位
 *
 */
function loadTwoLineChart(chart_dom_id, data1, data2, legend_str1, legend_str2, unit_str1, unit_str2) {

    var lineChart = echarts.init(document.getElementById(chart_dom_id))

    lineChart.showLoading({
        type: 'default'
    })

    var date_array = []
    var data1_array = []
    var data2_array = []

    for (var i = 0; i < data1.length; i++) {
        date_array.push(data1[i].date)
        data1_array.push(data1[i].value)
        data2_array.push(data2[i].value)
    }

    var setOption = {
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'cross',
                crossStyle: {
                    color: '#999'
                }
            }
        },
        legend: {
            data: [legend_str1, legend_str2]
        },
        xAxis: [
            {
                type: 'category',
                data: date_array,
                axisPointer: {
                    type: 'shadow'
                }
            }
        ],
        yAxis: [
            {
                type: 'value',
                name: legend_str1,
                axisLabel: {
                    formatter: '{value} ' + unit_str1
                },
                minInterval: 1
            },
            {
                type: 'value',
                name: legend_str2,
                axisLabel: {
                    formatter: '{value} ' + unit_str2
                },
                minInterval: 1
            }
        ],
        series: [
            {
                name: legend_str1,
                type: 'line',
                data: data1_array
            },
            {
                name: legend_str2,
                type: 'line',
                data: data2_array,
                yAxisIndex: 1
            },
        ]
    }

    lineChart.setOption(setOption)
    lineChart.hideLoading()

    return lineChart;
}


/*
 * 描绘柱状图趋势
 *
 * By TerryQi
 *
 * 2018-11-28
 *
 */
function loadTwoBarChart(chart_dom_id, data1, data2, legend_str1, legend_str2, unit_str1, unit_str2) {

    var barChart = echarts.init(document.getElementById(chart_dom_id))
    barChart.showLoading({
        type: 'default'
    })

    var date_array = []
    var data1_array = []
    var data2_array = []

    for (var i = 0; i < data1.length; i++) {
        date_array.push(data1[i].date)
        data1_array.push(data1[i].value)
        data2_array.push(data2[i].value)
    }

    var setOption = {
        tooltip: {
            trigger: 'axis',
            // formatter: "{b} : {c} 笔"
        },
        legend: {
            data: [legend_str1, legend_str2]
        },
        xAxis: {
            type: 'category',
            data: date_array
        },
        yAxis: [
            {
                type: 'value',
                axisLabel: {
                    formatter: '{value} ' + unit_str1
                }
            }, {
                type: 'value',
                axisLabel: {
                    formatter: '{value} ' + unit_str2
                }
            }
        ],
        series: [{
            name: legend_str1,
            data: data1_array,
            type: 'bar'
        }, {
            name: legend_str2,
            data: data2_array,
            type: 'bar'
        }
        ]
    }
    barChart.setOption(setOption)
    barChart.hideLoading()
    return barChart;
}