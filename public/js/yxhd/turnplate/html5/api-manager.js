/*!
 * jQuery Cookie Plugin v1.4.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2013 Klaus Hartl
 * Released under the MIT license
 */
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // CommonJS
        factory(require('jquery'));
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {

    var pluses = /\+/g;

    function encode(s) {
        return config.raw ? s : encodeURIComponent(s);
    }

    function decode(s) {
        return config.raw ? s : decodeURIComponent(s);
    }

    function stringifyCookieValue(value) {
        return encode(config.json ? JSON.stringify(value) : String(value));
    }

    function parseCookieValue(s) {
        if (s.indexOf('"') === 0) {
            // This is a quoted cookie as according to RFC2068, unescape..
            s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
        }

        try {
            // Replace server-side written pluses with spaces.
            // If we can't decode the cookie, ignore it, it's unusable.
            // If we can't parse the cookie, ignore it, it's unusable.
            s = decodeURIComponent(s.replace(pluses, ' '));
            return config.json ? JSON.parse(s) : s;
        } catch(e) {}
    }

    function read(s, converter) {
        var value = config.raw ? s : parseCookieValue(s);
        return $.isFunction(converter) ? converter(value) : value;
    }

    var config = $.cookie = function (key, value, options) {

        // Write

        if (value !== undefined && !$.isFunction(value)) {
            options = $.extend({}, config.defaults, options);

            if (typeof options.expires === 'number') {
                var days = options.expires, t = options.expires = new Date();
                t.setTime(+t + days * 864e+5);
            }

            return (document.cookie = [
                encode(key), '=', stringifyCookieValue(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                options.path    ? '; path=' + options.path : '',
                options.domain  ? '; domain=' + options.domain : '',
                options.secure  ? '; secure' : ''
            ].join(''));
        }

        // Read

        var result = key ? undefined : {};

        // To prevent the for loop in the first place assign an empty array
        // in case there are no cookies at all. Also prevents odd result when
        // calling $.cookie().
        var cookies = document.cookie ? document.cookie.split('; ') : [];

        for (var i = 0, l = cookies.length; i < l; i++) {
            var parts = cookies[i].split('=');
            var name = decode(parts.shift());
            var cookie = parts.join('=');

            if (key && key === name) {
                // If second argument (value) is a function it's a converter..
                result = read(cookie, value);
                break;
            }

            // Prevent storing a cookie that we couldn't decode.
            if (!key && (cookie = read(cookie)) !== undefined) {
                result[name] = cookie;
            }
        }

        return result;
    };

    config.defaults = {};

    $.removeCookie = function (key, options) {
        if ($.cookie(key) === undefined) {
            return false;
        }

        // Must not alter options, thus extending a fresh object..
        $.cookie(key, '', $.extend({}, options, { expires: -1 }));
        return !$.cookie(key);
    };

}));



// var BASE_URL='http://testqishi.isart.me/api/'
var BASE_URL='http://app.knightcomment.com/api/'
//测试 http://testqishi.isart.me/api/
//正式 http://app.knightcomment.com/api/


//获取服务器的数据测试
function apiManagerTest(param, callBack){
    ajaxRequestImp(BASE_URL+"activity/scrolls/mobilePrefix/getListByCon", param, "GET", callBack);
}
/*
 * 封装API调用接口
 *
 * By caoyang 2018-11-09
 *
 * @param
 * @url:访问连接
 * @param：参数
 * @method：请求方法 get post
 * @callback：回调函数
 *
 */
function ajaxRequestImp(url, param, method, callBack) {
    if(judgeIsAnyNullStr($.cookie('language'))){
        param.language='en'
    }else{
        param.language=$.cookie('language')
    }

    if($.cookie('activity_user_id')){
        param.activity_user_id=$.cookie('activity_user_id')
    }

    if($.cookie('activity_id')){
        param.activity_id=$.cookie('activity_id')
    }
    if(judgeIsAnyNullStr($.cookie('invite_code'))){
    }else{
        param.invite_code=$.cookie('invite_code')
    }

 /*   if($.cookie('invite_code')){
        param.invite_code=$.cookie('invite_code')
    }*/
    console.log("url:" + url + " method:" + method + " param:" + JSON.stringify(param));
    $.ajax({
        type: method,  //提交方式
        headers: {
            Accept: "application/json; charset=utf-8",
        },
        url: url,//路径

        data: param,//数据，这里使用的是Json格式进行传输
        dataType: "json",
        success: function (ret) {//返回数据根据结果进行相应的处理
            console.log("ret:" + JSON.stringify(ret));
            callBack(ret)
        },
        error: function (ret) {
            console.log(JSON.stringify(ret));
            console.log("responseText:" + ret.responseText);
            callBack(ret)
        }
    });
}
//获取国家列表
function mobilePrefix_getListByCon(param, successCallBack, errorCallBack) {
    ajaxRequestImp(BASE_URL + "activity/scrolls/mobilePrefix/getListByCon", param, "GET", successCallBack);
}


// 根据参数名称获取参数值
function getParamValue(name) {
    var paramsArray = getUrlParams();
    if (paramsArray != null) {
        for (var i = 0 ; i < paramsArray.length ; i++) {
            for (var j in paramsArray[i]) {
                if (j == name) {
                    return paramsArray[i][j];
                }
            }
        }
    }
    return null;
}

// 获取地址栏的参数数组
function getUrlParams() {
    var search = window.location.search;
    // 写入数据字典
    var tmparray = search.substr(1, search.length).split("&");
    var paramsArray = new Array;
    if (tmparray != null) {
        for (var i = 0; i < tmparray.length; i++) {
            var reg = /[=|^==]/;    // 用=进行拆分，但不包括==
            var set1 = tmparray[i].replace(reg, '&');
            var tmpStr2 = set1.split('&');
            var array = new Array;
            array[tmpStr2[0]] = tmpStr2[1];
            paramsArray.push(array);
        }
    }
    // 将参数数组进行返回
    return paramsArray;
}

//设置值
function setDefaultValue(val, default_val, more_val) {
    if (judgeIsAnyNullStr(val)) {
        return default_val;
    } else {
        return val + more_val;
    }
}

function judgeIsAnyNullStr(argument) {

    console.log('judgeIsAnyNullStr argument is : '+argument)
    if (argument == null ||argument == 'null' || argument == "" || argument == undefined || argument == "未设置" || argument == "undefined") {
        return true
    }
    return false
}

