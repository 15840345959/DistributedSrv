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

/*
 * 点击返回
 *
 * By TerryQi
 *
 * 2018-06-18
 */
function clickBack() {
    window.history.go(-1);
}


//新建toast变量
var toast = new auiToast({})

//加载中
function toast_loading(msg) {
    if (judgeIsNullStr(msg)) {
        msg = "加载中";
    }
    toast.loading({
        title: msg,
        duration: 2000
    }, function (ret) {
        console.log(ret);
        setTimeout(function () {
            toast.hide();
        }, 3000)
    });
}

//成功
function toast_success(msg) {
    if (judgeIsNullStr(msg)) {
        msg = "提交成功";
    }
    toast.success({
        title: msg,
        duration: 2000
    });
}

//失败
function toast_fail(msg) {
    if (judgeIsNullStr(msg)) {
        msg = "提交成功";
    }
    toast.fail({
        title: msg,
        duration: 2000
    });
}

//隐藏
function toast_hide() {
    toast.hide();
}

//提示对话框
var dialog = new auiDialog();

function dialog_show(param, callback) {
    var dialog_param = {
        title: "提示信息",
        msg: "确认执行操作？",
        buttons: ['取消', '确定'],
        input: false
    }
    if (!judgeIsNullStr(param.title)) {
        dialog_param.title = param.title;
    }
    if (!judgeIsNullStr(param.title)) {
        dialog_param.title = param.title;
    }
    if (!judgeIsNullStr(param.msg)) {
        dialog_param.msg = param.msg;
    }
    if (!judgeIsNullStr(param.buttons)) {
        dialog_param.buttons = param.buttons;
    }
    if (!judgeIsNullStr(param.input)) {
        dialog_param.input = param.input;
    }
    dialog.alert(dialog_param, function (ret) {
        if (typeof callback === "function") {
            callback(ret)
        }
    })
}


/////////////////////////////////////////////////

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
    if (val == null || val == "" || val == undefined || val == "未设置" || val == NaN) {
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
 * 用于对象克隆
 *
 * obj 对象，返回克隆对象
 *
 */
function clone(obj) {
    // Handle the 3 simple types, and null or undefined
    if (null == obj || "object" != typeof obj) return obj;

    // Handle Date
    if (obj instanceof Date) {
        var copy = new Date();
        copy.setTime(obj.getTime());
        return copy;
    }

    // Handle Array
    if (obj instanceof Array) {
        var copy = [];
        for (var i = 0, len = obj.length; i < len; ++i) {
            copy[i] = clone(obj[i]);
        }
        return copy;
    }

    // Handle Object
    if (obj instanceof Object) {
        var copy = {};
        for (var attr in obj) {
            if (obj.hasOwnProperty(attr)) copy[attr] = clone(obj[attr]);
        }
        return copy;
    }

    throw new Error("Unable to copy obj! Its type isn't supported.");
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
 * 生成随机字符串
 * 
 * By TerryQi
 * 
 * 2018-07-27
 */
function randChars(num) {
    var seed = new Array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'm', 'n', 'p', 'Q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
        '2', '3', '4', '5', '6', '7', '8', '9'
    );//数组
    var seedlength = seed.length;//数组长度
    var chars = '';
    for (var i = 0; i < num; i++) {
        var j = Math.floor(Math.random() * seedlength);
        chars += seed[j];
    }
    return chars;

}