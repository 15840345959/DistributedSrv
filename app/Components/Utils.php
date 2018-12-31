<?php
/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2017/12/4
 * Time: 9:23
 */

namespace App\Components;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class Utils
{
    //分页配置
    const PAGE_SIZE = 15;

    //超级管理员电话
    const SUPER_ADMIN_PHONENUM = "18624327300";        //用于接收管理后天验证码

    //测试主机和生产主机URL地址
    const PRODUCT_SERVER_URL = 'http://art.syatc.cn/';

    //小程序模板/////////////////////////////////////////////////////////

    //艺术榜
    const XCX_YSB_SCHEDULE_NOTIFY = "vqeAY6-cbXEqebt6WY-jmgwGSM_ICi-VzUZ0MA0WNlI";         //日程提醒

    //每天一画
    const XCX_MRYH_JOIN_RESULT_NOFITY = "GdN0qfA0eYuY1LeNz0Wlh0qM_UwwKfz75koxWGr-EFQ";      //挑战结果通知
    const XCX_MRYH_WITHDRAW_NOFITY = "KowoHOLbbYV0iKUSo8vON5Mb4-zqLkphnYjkGMqsStI";      //提现到账提醒
    const XCX_MRYH_JOIN_SCHEDULE_NOTIFY = "fx9LJQ9fvwiSmEi28l_5KPBSuU1FOE6DbqRJEpZt1JI";      //日程提醒

    //////////////////////////////////////////////////////////////////////////


    //短信模板//////////////////////////////////////////////////////////////////////////////

    //投票大赛
    const VOTE_SMS_TEMPLATE_COMPLAIN = "423564461";     //投诉提醒-对管理员
    const VOTE_SMS_TEMPLATE_ACTIVITY_NOTE_VALID = "423570740";      //活动未激活提醒-对管理员
    const VOTE_SMS_TEMPLATE_AUDIT_NOTICE = "430645280";  //选手待审核提醒-对管理员

    //小艺商城
    const SHOP_SMS_TEMPLATE_PAYORDER = "905649492";   //下单提醒-对管理员
    const SHOP_SMS_TEMPLATE_SENDGOODS = "905652097"; //发货通知-对客户
    const SHOP_SMS_TEMPLATE_REFUND = "915362984";     //退款提醒-对客户

    //每日一画
    const SUPER_ADMIN_VERTIFY_CODE = "1105336651";       //下发短信验证码

    //短信模板//////////////////////////////////////////////////////////////////////////////


    //公众号账号配置
    const OFFICIAL_ACCOUNT_CONFIG_VAL = ['isart' => 'wechat.official_account.isart'];

    //小程序账号配置
    const XCX_ACCOUNT_CONFIG_VAL = ['ysb' => "wechat.mini_program.ysb"];

    //获取配置信息
    public static function getPaymentConfig($busi_name)
    {
        $config = null;
        switch ($busi_name) {
            case "isart":           //投票大赛
                $config = [
                    'appid' => '', // APP APPID
                    'app_id' => env('ISART_FWH_APPID', ''), // 公众号 APPID
                    'miniapp_id' => '',       //小程序miniapp_id
                    'mch_id' => env('ISART_FWH_MCH_ID', ''),                    // 微信商户号
                    'key' => env('ISART_FWH_MCH_KEY', ''),                     // 微信支付签名秘钥  ImpYNtH4B5x7C587qy5ujzS6fbZnNv6T
                    'notify_url' => env('ISART_FWH_MCH_NOTIFY_URL', ''),//'http://waibao.isart.me/api/sjdl/order/payNotify',      //支付结果通知地址  https://tclm.isart.me/api/wechat/notify
                    'cert_client' => app_path() . '/cert/isart_apiclient_cert.pem',        // 客户端证书路径，退款时需要用到
                    'cert_key' => app_path() . '/cert/isart_apiclient_key.pem',             // 客户端秘钥路径，退款时需要用到
                    'log' => [ // optional
                        'file' => app_path() . '/../storage/logs/isart_wechat.log',
                        'level' => 'debug'
                    ]
                ];
                break;
            case "mryh":            //每天一画
                $config = [
                    'appid' => '', // APP APPID
                    'app_id' => '', // 公众号 APPID
                    'miniapp_id' => env('MRYH_XCX_APPID', ''),       //小程序miniapp_id
                    'mch_id' => env('ISART_FWH_MCH_ID', ''),                    // 微信商户号
                    'key' => env('ISART_FWH_MCH_KEY', ''),                     // 微信支付签名秘钥  ImpYNtH4B5x7C587qy5ujzS6fbZnNv6T
                    'notify_url' => env('MRYH_FWH_MCH_NOTIFY_URL', ''),//'http://waibao.isart.me/api/sjdl/order/payNotify',      //支付结果通知地址  https://tclm.isart.me/api/wechat/notify
                    'cert_client' => app_path() . '/cert/isart_apiclient_cert.pem',        // 客户端证书路径，退款时需要用到
                    'cert_key' => app_path() . '/cert/isart_apiclient_key.pem',             // 客户端秘钥路径，退款时需要用到
                    'log' => [ // optional
                        'file' => app_path() . '/../storage/logs/mryh_wechat.log',
                        'level' => 'debug'
                    ]
                ];
                break;
            case "shop":            //小艺商城
                $config = [
                    'appid' => '', // APP APPID
                    'app_id' => env('ISART_FWH_APPID', ''), // 公众号 APPID
                    'miniapp_id' => '',       //小程序miniapp_id
                    'mch_id' => env('ISART_FWH_MCH_ID', ''),                    // 微信商户号
                    'key' => env('ISART_FWH_MCH_KEY', ''),                     // 微信支付签名秘钥  ImpYNtH4B5x7C587qy5ujzS6fbZnNv6T
                    'notify_url' => env('ISART_SHOP_MCH_NOTIFY_URL', ''),//'http://waibao.isart.me/api/sjdl/order/payNotify',      //支付结果通知地址  https://tclm.isart.me/api/wechat/notify
                    'cert_client' => app_path() . '/cert/isart_apiclient_cert.pem',        // 客户端证书路径，退款时需要用到
                    'cert_key' => app_path() . '/cert/isart_apiclient_key.pem',             // 客户端秘钥路径，退款时需要用到
                    'log' => [ // optional
                        'file' => app_path() . '/../storage/logs/isart_shop.log',
                        'level' => 'debug'
                    ]
                ];
                break;
        }
        return $config;
    }

    //常量配置
    //管理员角色
    const admin_role_val = ['0' => '超级管理员', '1' => '运营管理员'];

    //通用的status
    const COMMON_STATUS_VAL = ['0' => '失效', '1' => '生效'];
    const COMMON_PAY_STATUS_VAL = ['0' => '未支付', '1' => '已支付'];
    const COMMON_USED_STATUS_VAL = ['0' => '未使用', '1' => '已使用'];

    //登录账号类型
    const ACCOUNT_TYPE_TEL_PASSWD = "tel_passwd";       //手机号加密码
    const ACCOUNT_TYPE_TEL_CODE = "tel_code";        //手机号加随机密码
    const ACCOUNT_TYPE_XCX = "xcx";     //小程序
    const ACCOUNT_TYPE_FWH = "fwh";     //公众号

    //小程序form表单相关
    const XCX_FORM_USED_FLAG_VAL = ['0' => '未用光', '1' => '已用光'];


    //登录账号
    const ACCOUNT_TYPE_VAL = ['xcx' => '小程序', 'fwh' => '公众号', 'tel_passwd' => '手机号密码', '手机号随机码' => 'tel_code'];

    //用户性别
    const user_gender_val = ['0' => '保密', '1' => '男', '2' => '女'];
    //用户状态
    const user_status_val = ['0' => '冻结', '1' => '正常'];
    //用户类型
    const USER_TYPE_VAL = ['0' => '普通用户', '1' => '系统管理员'];

    //评论状态
    const COMMENT_READ_STATUS_VAL = ['0' => '未读', '1' => '已读'];
    const COMMENT_AUDIT_STATUS_VAL = ['0' => '待审核', '1' => '审核通过', '2' => '审核驳回'];
    const COMMENT_RECOMM_FLAG_VAL = ['0' => '未推荐', '1' => '已推荐'];

    //业务名称
    const BUSI_NAME_VAL = ['isart' => 'ISART公众号', 'mryh' => '每天一画', 'ysb' => '艺术榜', 'shop' => '小艺商城', 'vote' => '投票大赛', 'none' => '无业务'];

    const BUSI_NAME_YSB = "ysb";      //艺术榜
    const BUSI_NAME_MRYH = "mryh";        //每天一画
    const BUSI_NAME_SHOP = "shop";          //小艺商城

    //作品相关标示位
    const ARTICLE_STAUS_VAL = ['0' => '失效', '1' => '生效'];
    const ARTICLE_PRI_FLAG_VAL = ['0' => '公开', '1' => '私有'];
    const ARTICLE_ALLOW_COMMENT_FLAG_VAL = ['0' => '不允许评论', '1' => '允许评论'];
    const ARTICLE_ORI_FLAG_VAL = ['0' => '非原创', '1' => '原创'];
    const ARTICLE_APPLY_RECOMM_FLAG_VAL = ['0' => '不申请精华', '1' => '申请精华'];
    const ARTICLE_RECOMM_FLAG_VAL = ['0' => '不推荐', '1' => '推荐'];
    const ARTICLE_AUDIT_STATUS_VAL = ['0' => '待审核', '1' => '审核通过', '2' => '审核驳回'];
    const ARTICLE_SYS_FLAG_VAL = ['0' => '非系统', '1' => '系统'];

    //作品类别状态
    const ARTICLE_TYPE_STATUS_VAL = ['0' => '失效', '1' => '生效'];

    //商品类别状态
    const GOODS_TYPE_STATUS_VAL = ['0' => '失效', '1' => '生效'];

    //商品信息
    const GOODS_STATUS_VAL = ['0' => '失效', '1' => '生效'];
    const GOODS_RECOMM_FLAG_VAL = ['0' => '不推荐', '1' => '推荐'];

    //商品价格模式
    const GOODS_PRICE_MODE_VAL = ['0' => '现金', '1' => '积分', '现金+积分'];
    const GOODS_PRICE_STATUS_VAL = ['0' => '失效', '1' => '生效'];

    //物流配置
    const LOGISTICS_STATUS_VAL = ['0' => '失效', '1' => '生效'];
    const LOGISTICS_COM_VAL = ['sf' => '顺丰', 'sto' => '申通', 'yt' => '圆通', 'yd' => '韵达', 'ems' => 'EMS', 'zto' => '中通', 'ht' => '汇通'];

    //FTable定义
    const F_TABLB_ARTICLE = "article";
    const F_TABLB_GOODS = "goods";
    const F_TABLE_MRYH_JOIN = 'mryh_join';
    const F_TABLE_MRYH_WITHDRAW = 'mryh_withdraw';
    const F_TABLE_MRYH_GAME = 'mryh_game';

    const F_TABLE_YXHD_ORDER = 'yxhd_order_info';
    const F_TABLE_YXHD_ACTIVITY = 'yxhd_activity_info';


    const F_TABLE_ARTICLE_VAL = [self::F_TABLB_ARTICLE => '作品', self::F_TABLB_GOODS => '商品'
        , self::F_TABLE_MRYH_JOIN => '每天一画参赛信息', self::F_TABLE_MRYH_WITHDRAW => '每天一画提现信息'
        , self::F_TABLE_MRYH_GAME => '每天一画活动', self::F_TABLE_YXHD_ORDER => '营销活动订单'];

    //关联关系级别
    const USER_REL_LEVEL_VAL = ['0' => '新用户', '1' => '促活'];


    const OPT_F_TABLE_VAL = ['order' => '通用订单', 'activity' => '活动相关', 'mryh_game' => '每日一画活动'];

    // 操作值定义
    const OPT_FINISHED = "FINISHED";        //已完成
    const OPT_HANDLING = "HANDLING";        //处理中

    //操作类型定义
    const OPT_TYPE_ACTIVITY = "activity";   //活动

    //规则位置定义-规则位置完全自定义，需要与技术组明确
    const RULE_POSITION_VAL = ['0' => '0号位置', '1' => '1号位置', '2' => '2号位置', '3' => '3号位置', '4' => '4号位置', '5' => '5号位置'];

    //广告位置定义-广告位置完全自定义，需要与技术组明确
    const AD_POSITION_VAL = ['0' => '0号位置', '1' => '1号位置', '2' => '2号位置', '3' => '3号位置', '4' => '4号位置', '5' => '5号位置'];

    //处理状态
    const FEEDBACK_STATUS_VAL = ['0' => '待处理', '1' => '处理中', '2' => '处理完成'];

    /******公众号*************/

    //公众号素材类型
    const MATERIAL_TYPE_VAL = ['image' => '图片', 'voice' => '语音', 'video' => '视频', 'thumb' => '缩略图', 'article' => '图文'];
    //公众号消息种类
    const MESSAGE_TYPE_VAL = ['text' => '文本消息', 'image' => '图片消息', 'video' => '视频消息', 'voice' => '声音消息', 'news' => '图文消息'];
    //公众号业务话术
    const NORMAL_TEMPLATE_VAL = ['TEMPLATE_SUBSCRIBE' => '关注事件模板'];
    //公众号菜单类型
    const MENU_TYPE_VAL = ['view' => '网页链接', 'media_id' => '素材', 'miniprogram' => '小程序', 'click' => '自定义事件'];

    /**************************/


    /******投票活动*************/

    //单日单用户投票数大于多少启动校验码机制
    const DAILY_START_VOTE_VERTIFY_NUM = 50;

    //投票分享标签-是否启用泛域名
    const VOTE_SHARE_DEBUG = false;

    //投票模式
    const VOTE_MODE_VAL = ['0' => '投票、礼物模式', '1' => '投票模式', '2' => '礼物模式'];
    //关注设置
    const SUBSCRIBE_MODE_VAL = ['0' => '不需要关注', '1' => '投票需要关注', '2' => '参加活动需要关注', '3' => '参加、投票都需要关注'];
    //投票审核模式
    const VOTE_AUDIT_MODE_VAL = ['0' => '自动审核', '1' => '人工审核'];
    //验证码模式
    const VOTE_VERTIFY_MODE_VAL = ['0' => '无验证码', '1' => '有验证码'];
    //投票提醒模式
    const VOTE_NOTICE_MODE_VAL = ['0' => '不提醒', '1' => '提醒'];
    //投票广告位置
    const VOTE_AD_MODE_VAL = ['normal' => '普通广告', 'day5' => '倒计时5天广告', 'day4' => '倒计时4天广告'
        , 'day3' => '倒计时3天广告', 'day2' => '倒计时2天广告', 'day1' => '倒计时1天广告'];
    //广告显示模式
    const VOTE_SHOW_AD_MODE_VAL = ['0' => '不启用倒计时广告', '1' => '启用倒计时广告'];
    //活动状态
    const VOTE_ACTIVITY_STATUS_VAL = ['0' => '未开始', '1' => '已开始', '2' => '已结束'];
    //活动激活状态
    const VOTE_ACTIVITY_VALID_STATUS_VAL = ['0' => '未激活', '1' => '已激活'];
    //结算状态
    const VOTE_ACTIVITY_IS_SETTLE_VAL = ['0' => '未结算', '1' => '已结算'];
    //投票状态
    const VOTE_VOTE_STATUS_VAL = ['0' => '未开始', '1' => '已开始', '2' => '已结束'];
    //冻结状态
    const VOTE_STATUS_VAL = ['0' => '冻结', '1' => '正常'];
    //报名状态
    const VOTE_APPLY_STATUS_VAL = ['0' => '未开始', '1' => '已开始', '2' => '已结束'];
    //用户审核状态
    const VOTE_USER_AUDIT_STATUS_VAL = ['0' => '未审核', '1' => '审核通过', '2' => '审核驳回'];
    //选手激活状态
    const VOTE_USER_VALID_STATUS_VAL = ['0' => '未激活', '1' => '已激活'];
    //选手基本状态
    const VOTE_USER_STATUS_VAL = ['0' => '停用', '1' => '正常'];
    //投诉状态
    const VOTE_COMPLAIN_STATUS_VAL = ['0' => '待解决', '1' => '已解决'];
    //选手类型
    const VOTE_USER_TYPE_VAL = ['0' => '机构报名', '1' => '自主报名'];
    //投票类型
    const VOTE_TYPE_VAL = ['0' => '普通投票', '1' => '付费投票'];
    //分享选手类型
    const VOTE_SHARE_TYPE_VAL = ['0' => '朋友圈分享', '1' => 'APP消息分享'];
    //投票订单支付状态
    const VOTE_ORDER_PAY_STATUS_VAL = ['0' => '待支付', '1' => '支付成功', '2' => '已关闭', '3' => '退款中', '4' => '退款成功', '5' => '退款失败'];

    //地推团队状态
    const VOTE_TEAM_STATUS_VAL = ['0' => '失效', '1' => '生效'];

    /**************************/


    /******每天一画*************/

    const MRYH_AD_TYPE_VAL = ['none' => '不跳转', 'page' => '跳转页面', 'url' => '跳转链接', 'content' => '配置内容'];

    const MRYH_GAME_TYPE_VAL = ['0' => '自定义活动', '1' => '周活动', '2' => '月活动', '3' => '主题活动'];
    const MRYH_GAME_ADMIN_TYPE_VAL = ['1' => '周活动', '2' => '月活动', '3' => '主题活动'];
    const MRYH_GAME_MODE_VAL = ['0' => '连续参与', '1' => '总计参与'];
    const MRYH_GAME_SHOW_FLAG_VAL = ['0' => '不展示', '1' => '展示'];
    const MRYH_GAME_RECOMM_FLAG_VAL = ['0' => '不推荐', '1' => '推荐'];
    const MRYH_GAME_STATUS_VAL = ['0' => '失效', '1' => '生效'];
    const MRYH_GAME_SHOW_STATUS_VAL = ['0' => '不展示', '1' => '展示'];
    const MRYH_GAME_JOIN_STATUS_VAL = ['0' => '不可参与', '1' => '可参与'];
    const MRYH_GAME_GAME_STATUS_VAL = ['0' => '未开始', '1' => '已开始', '2' => '已结束'];
    const MRYH_GAME_JIESUAN_STATUS_VAL = ['0' => '未清分', '1' => '已清分'];
    const MRYH_GAME_CREATOR_TYPE_VAL = ['0' => '管理员', '1' => '用户'];

    //订单支付状态
    const MRYH_ORDER_PAY_STATUS_VAL = ['0' => '待支付', '1' => '支付成功', '2' => '已关闭', '3' => '退款中', '4' => '退款成功', '5' => '退款失败'];

    //参赛信息
    const MRYH_JOIN_GAME_STATUS_VAL = ['0' => '正在进行', '1' => '成功', '2' => '失败'];
    const MRYH_JOIN_JIESUAN_STATUS_VAL = ['0' => '未结算', '1' => '已结算'];
    const MRYH_JOIN_CLEAR_STATUS_VAL = ['0' => '未清分', '1' => '已清分'];

    //迎新优惠券编码
    const MRYH_COUPONS_CODE_VAL = ['FOR_FIRST_NEW', '迎新优惠券'];

    //优惠券使用状态
    const MRYH_COUPONS_USED_STATUS_VAL = ['0' => '未使用', '1' => '已使用', '2' => '已过期'];

    //提现相关
    const MRYH_WITHDRAW_CASH_WITHDRAW_STATUS = ['0' => '提交中', '1' => '提现成功', '2' => '提现失败'];

    //清分执行情况
    const MRYH_COMPUTE_PRIZE_COMPUTE_STATUS = ['0' => '未执行', '1' => '已执行'];


    /**************************/


    /*****艺术榜***************************/


    const YSB_AD_TYPE_VAL = ['none' => '不跳转', 'url' => '跳转链接', 'content' => '配置内容'];


    /**************************/

    /*****小艺商城***************************/


    const SHOP_AD_TYPE_VAL = ['none' => '不跳转', 'url' => '跳转链接', 'content' => '配置内容'];

    const SHOP_ORDER_PAY_STATUS_VAL = ['0' => '待支付', '1' => '支付成功', '2' => '已关闭'];

    const SHOP_ORDER_SEND_STATUS_VAL = ['0' => '待发货', '1' => '已发货'];

    const SHOP_ORDER_PAY_TYPE_VAL = ['0' => '微信', '1' => '支付宝'];

    const SHOP_ORDER_REFUND_STATUS_VAL = ['0' => '未退款', '1' => '已退款'];

    /**************************/


    /*****2B业务***************************/

//    const TO_B_MESSAGE_TYPE = ['0' => '全体消息', '1' => '定向消息'];
    const TO_B_MESSAGE_TYPE = ['0' => '全体消息'];
    const TO_B_MESSAGE_TOP = ['0' => '普通', '1' => '置顶'];
    const TO_B_MESSAGE_LEVEL = ['0' => '普通', '1' => '重要', '2' => '紧急'];

    /**************************/


    ///营销模块/////////////////////////////////////////////////////

    const YXHD_TYPE_VAL = ['0' => '大转盘活动'];

    const YXHD_PRIZE_TYPE_VAL = ['0' => '电子凭证'];       //营销活动礼品样式

    const YXHD_ORDER_WINNING_STATUS_VAL = ['0' => '未中奖', '1' => '已中奖'];


    //投票大赛获得积分的规则
    const YXHD_VOTE_ADD_SCORE_VAL = 10;       //投票大赛营销活动进行积分增加 10积分
    const YXHD_ORDER_MULTI_SCORE_VAL = 5;     //投票大赛影戏小活动进行积分增加 为5*打赏金额 积分


    ////////////////////////////////////////////////////////////////
    ///
    /// 随机头像
    ///
    ///
    const AVATAR_ARR = ["http://twst.isart.me/avatar/20181228975497986334.png",
        "http://twst.isart.me/avatar/20181228999849491952.png",
        "http://twst.isart.me/avatar/20181228514955485596.png",
        "http://twst.isart.me/avatar/20181228565255104128.png",
        "http://twst.isart.me/avatar/20181228549951512851.png",
        "http://twst.isart.me/avatar/20181228101509857390.png",
        "http://twst.isart.me/avatar/20181228995548578747.png",
        "http://twst.isart.me/avatar/20181228100559855801.png",
        "http://twst.isart.me/avatar/20181228101491000423.png",
        "http://twst.isart.me/avatar/20181228984952100085.png",
        "http://twst.isart.me/avatar/20181228569753106779.png",
        "http://twst.isart.me/avatar/20181228564854995466.png",
        "http://twst.isart.me/avatar/20181228555252505399.png",
        "http://twst.isart.me/avatar/20181228541005457981.png",
        "http://twst.isart.me/avatar/20181228494948489163.png",
        "http://twst.isart.me/avatar/20181228574957559963.png",
        "http://twst.isart.me/avatar/20181228565510150753.png",
        "http://twst.isart.me/avatar/20181228985748544483.png",
        "http://twst.isart.me/avatar/20181228489851520201.png",
        "http://twst.isart.me/avatar/20181228102485554130.png",
        "http://twst.isart.me/avatar/20181228100101983527.png",
        "http://twst.isart.me/avatar/20181228100491022496.png",
        "http://twst.isart.me/avatar/20181228995355548900.png",
        "http://twst.isart.me/avatar/20181228102985246465.png",
        "http://twst.isart.me/avatar/20181228985256578731.png",
        "http://twst.isart.me/avatar/20181228971014954111.png",
        "http://twst.isart.me/avatar/20181228102100494621.png",
        "http://twst.isart.me/avatar/20181228101495359509.png",
        "http://twst.isart.me/avatar/20181228565150563294.png",
        "http://twst.isart.me/avatar/20181228575197502148.png",
        "http://twst.isart.me/avatar/20181228535653102397.png",
        "http://twst.isart.me/avatar/20181228489999549460.png",
        "http://twst.isart.me/avatar/20181228995254536938.png",
        "http://twst.isart.me/avatar/20181228571005590292.png",
        "http://twst.isart.me/avatar/20181228531015256059.png",
        "http://twst.isart.me/avatar/20181228521005759946.png",
        "http://twst.isart.me/avatar/20181228559752977517.png",
        "http://twst.isart.me/avatar/20181228515351552878.png",
        "http://twst.isart.me/avatar/20181228489957528090.png",
        "http://twst.isart.me/avatar/20181228545310255176.png",
        "http://twst.isart.me/avatar/20181228515710296413.png",
        "http://twst.isart.me/avatar/20181228535649547256.png",
        "http://twst.isart.me/avatar/20181228102545294531.png",
        "http://twst.isart.me/avatar/20181228101995694048.png",
        "http://twst.isart.me/avatar/20181228991024898612.png",
        "http://twst.isart.me/avatar/20181228995498106387.png",
        "http://twst.isart.me/avatar/20181228989798578355.png",
        "http://twst.isart.me/avatar/20181228539950104934.png",
        "http://twst.isart.me/avatar/20181228101505352668.png",
        "http://twst.isart.me/avatar/20181228575349538737.png",
        "http://twst.isart.me/avatar/20181228529950564699.png",
        "http://twst.isart.me/avatar/20181228511001027320.png",
        "http://twst.isart.me/avatar/20181228100485357036.png",
        "http://twst.isart.me/avatar/20181228575549513185.png",
        "http://twst.isart.me/avatar/20181228505054489605.png",
        "http://twst.isart.me/avatar/20181228991005450545.png",
        "http://twst.isart.me/avatar/20181228524950571907.png",
        "http://twst.isart.me/avatar/20181228984956988305.png",
        "http://twst.isart.me/avatar/20181228559910154589.png",
        "http://twst.isart.me/avatar/20181228505652977632.png",
        "http://twst.isart.me/avatar/20181228979798984089.png",
        "http://twst.isart.me/avatar/20181228102975556035.png",
        "http://twst.isart.me/avatar/20181228999755578077.png",
        "http://twst.isart.me/avatar/20181228975410051580.png",
        "http://twst.isart.me/avatar/20181228555650492440.png",
        "http://twst.isart.me/avatar/20181228102489856066.png",
        "http://twst.isart.me/avatar/20181228531009859127.png",
        "http://twst.isart.me/avatar/20181228985255544813.png",
        "http://twst.isart.me/avatar/20181228531019756353.png",
        "http://twst.isart.me/avatar/20181228535256538429.png",
        "http://twst.isart.me/avatar/20181228984954548615.png",
        "http://twst.isart.me/avatar/20181228575210190448.png",
        "http://twst.isart.me/avatar/20181228561005319613.png",
        "http://twst.isart.me/avatar/20181228989910019464.png",
        "http://twst.isart.me/avatar/20181228565051555672.png",
        "http://twst.isart.me/avatar/20181228101544818579.png",
        "http://twst.isart.me/avatar/20181228100985451750.png",
        "http://twst.isart.me/avatar/20181228559749108340.png",
        "http://twst.isart.me/avatar/20181228102100547128.png",
        "http://twst.isart.me/avatar/20181228999750484101.png",
        "http://twst.isart.me/avatar/20181228525210053175.png",
        "http://twst.isart.me/avatar/20181228995650523704.png",
        "http://twst.isart.me/avatar/20181228974948978838.png",
        "http://twst.isart.me/avatar/20181228565548528840.png",
        "http://twst.isart.me/avatar/20181228999810250717.png",
        "http://twst.isart.me/avatar/20181228571001029045.png",
        "http://twst.isart.me/avatar/20181228555353102395.png",
        "http://twst.isart.me/avatar/20181228549753992220.png",
        "http://twst.isart.me/avatar/20181228525298535259.png",
        "http://twst.isart.me/avatar/20181228102579950872.png",
        "http://twst.isart.me/avatar/20181228984949986560.png",
        "http://twst.isart.me/avatar/20181228495351103366.png",
        "http://twst.isart.me/avatar/20181228985549534837.png",
        "http://twst.isart.me/avatar/20181228101545117929.png",
        "http://twst.isart.me/avatar/20181228971015219040.png",
        "http://twst.isart.me/avatar/20181228565299575299.png",
        "http://twst.isart.me/avatar/20181228535210050518.png",
        "http://twst.isart.me/avatar/20181228975355101638.png",
        "http://twst.isart.me/avatar/20181228505056109089.png",
        "http://twst.isart.me/avatar/20181228971024959279.png",
        "http://twst.isart.me/avatar/20181228984848517879.png",
        "http://twst.isart.me/avatar/20181228575197519835.png",
        "http://twst.isart.me/avatar/20181228565051571219.png",
        "http://twst.isart.me/avatar/20181228555554101640.png",
        "http://twst.isart.me/avatar/20181228489897570691.png",
        "http://twst.isart.me/avatar/20181228101541020487.png",
        "http://twst.isart.me/avatar/20181228574898991488.png",
        "http://twst.isart.me/avatar/20181228545349510538.png",
        "http://twst.isart.me/avatar/20181228535055982249.png",
        "http://twst.isart.me/avatar/20181228511005046680.png",
        "http://twst.isart.me/avatar/20181228505455561368.png",
        "http://twst.isart.me/avatar/20181228499998500340.png",
        "http://twst.isart.me/avatar/20181228541011022805.png",
        "http://twst.isart.me/avatar/20181228529898108232.png",
        "http://twst.isart.me/avatar/20181228481004959539.png",
        "http://twst.isart.me/avatar/20181228102524892586.png",
        "http://twst.isart.me/avatar/20181228575552104772.png",
        "http://twst.isart.me/avatar/20181228575051554155.png",
        "http://twst.isart.me/avatar/20181228565352986629.png",
        "http://twst.isart.me/avatar/20181228519755107255.png",
        "http://twst.isart.me/avatar/20181228515252507264.png",
        "http://twst.isart.me/avatar/20181228975357106725.png",
        "http://twst.isart.me/avatar/20181228525010294147.png",
        "http://twst.isart.me/avatar/20181228495410218872.png",
        "http://twst.isart.me/avatar/20181228561019792848.png",
        "http://twst.isart.me/avatar/20181228549810093229.png",
        "http://twst.isart.me/avatar/20181228101535350440.png",
        "http://twst.isart.me/avatar/20181228985348527988.png",
        "http://twst.isart.me/avatar/20181228539898522638.png",
        "http://twst.isart.me/avatar/20181228535049515238.png",
        "http://twst.isart.me/avatar/20181228525755108564.png",
        "http://twst.isart.me/avatar/20181228509850995507.png",
        "http://twst.isart.me/avatar/20181228494957991147.png",
        "http://twst.isart.me/avatar/20181228100569752604.png",
        "http://twst.isart.me/avatar/20181228575257510319.png",
        "http://twst.isart.me/avatar/20181228535198521109.png",
        "http://twst.isart.me/avatar/20181228515610159306.png",
        "http://twst.isart.me/avatar/20181228501009857177.png",
        "http://twst.isart.me/avatar/20181228485098548947.png",
        "http://twst.isart.me/avatar/20181228975210012606.png",
        "http://twst.isart.me/avatar/20181228491015254011.png",
        "http://twst.isart.me/avatar/20181228101985095507.png",
        "http://twst.isart.me/avatar/20181228511015258907.png",
        "http://twst.isart.me/avatar/20181228499750992437.png",
        "http://twst.isart.me/avatar/20181228494853106358.png",
        "http://twst.isart.me/avatar/20181228485652100970.png",
        "http://twst.isart.me/avatar/20181228569710157783.png",
        "http://twst.isart.me/avatar/20181228101519959783.png",
        "http://twst.isart.me/avatar/20181228100549917533.png",
        "http://twst.isart.me/avatar/20181228545454987033.png",
        "http://twst.isart.me/avatar/20181228499857561593.png",
        "http://twst.isart.me/avatar/20181228985256482959.png",
        "http://twst.isart.me/avatar/20181228975599531646.png",
        "http://twst.isart.me/avatar/20181228565553549256.png",
        "http://twst.isart.me/avatar/20181228515257572974.png",
        "http://twst.isart.me/avatar/20181228575354108981.png",
        "http://twst.isart.me/avatar/20181228534954534468.png",
        "http://twst.isart.me/avatar/20181228515450559625.png",
        "http://twst.isart.me/avatar/20181228101571011611.png",
        "http://twst.isart.me/avatar/20181228569810053935.png",
        "http://twst.isart.me/avatar/20181228555599990668.png",
        "http://twst.isart.me/avatar/20181228549854501315.png",
        "http://twst.isart.me/avatar/20181228505610256997.png",
        "http://twst.isart.me/avatar/20181228100555613900.png",
        "http://twst.isart.me/avatar/20181228574852547881.png",
        "http://twst.isart.me/avatar/20181228481011024280.png",
        "http://twst.isart.me/avatar/20181228981011014167.png",
        "http://twst.isart.me/avatar/20181228985352986972.png",
        "http://twst.isart.me/avatar/20181228101555050052.png",
        "http://twst.isart.me/avatar/20181228100525259728.png",
        "http://twst.isart.me/avatar/20181228575410297505.png",
        "http://twst.isart.me/avatar/20181228494997551618.png",
        "http://twst.isart.me/avatar/20181228100985591434.png",
        "http://twst.isart.me/avatar/20181228979749542997.png",
        "http://twst.isart.me/avatar/20181228544849984756.png",
        "http://twst.isart.me/avatar/20181228101999743278.png",
        "http://twst.isart.me/avatar/20181228985655101924.png",
        "http://twst.isart.me/avatar/20181228485310054786.png",
        "http://twst.isart.me/avatar/20181228102101482677.png",
        "http://twst.isart.me/avatar/20181228101555045950.png",
        "http://twst.isart.me/avatar/20181228999852520351.png",
        "http://twst.isart.me/avatar/20181228579857100183.png",
        "http://twst.isart.me/avatar/20181228509953106730.png",
        "http://twst.isart.me/avatar/20181228974956975542.png",
        "http://twst.isart.me/avatar/20181228569849483609.png",
        "http://twst.isart.me/avatar/20181228525610059563.png",
        "http://twst.isart.me/avatar/20181228579755539074.png",
        "http://twst.isart.me/avatar/20181228981005098308.png",
        "http://twst.isart.me/avatar/20181228975254566938.png",
        "http://twst.isart.me/avatar/20181228499857536175.png",
        "http://twst.isart.me/avatar/20181228995355571953.png",
        "http://twst.isart.me/avatar/20181228985552534501.png",
        "http://twst.isart.me/avatar/20181228571025245695.png",
        "http://twst.isart.me/avatar/20181228561015096811.png",
        "http://twst.isart.me/avatar/20181228555351973366.png",
        "http://twst.isart.me/avatar/20181228485799517104.png",
        "http://twst.isart.me/avatar/20181228101531025176.png",
        "http://twst.isart.me/avatar/20181228544810059424.png",
        "http://twst.isart.me/avatar/20181228491005658465.png",
        "http://twst.isart.me/avatar/20181228485099104113.png",
        "http://twst.isart.me/avatar/20181228985454539180.png",
        "http://twst.isart.me/avatar/20181228995052562815.png",
        "http://twst.isart.me/avatar/20181228975450985039.png",
        "http://twst.isart.me/avatar/20181228574898516240.png",
        "http://twst.isart.me/avatar/20181228485451521165.png",
        "http://twst.isart.me/avatar/20181228985156483988.png",
        "http://twst.isart.me/avatar/20181228575555100835.png",
        "http://twst.isart.me/avatar/20181228515498532846.png",
        "http://twst.isart.me/avatar/20181228545156537230.png",
        "http://twst.isart.me/avatar/20181228515450989437.png",
        "http://twst.isart.me/avatar/20181228505710093093.png",
        "http://twst.isart.me/avatar/20181228535052528502.png",
        "http://twst.isart.me/avatar/20181228541025153080.png",
        "http://twst.isart.me/avatar/20181228549855549939.png",
        "http://twst.isart.me/avatar/20181228515797997051.png",
        "http://twst.isart.me/avatar/20181228495249480405.png",
        "http://twst.isart.me/avatar/20181228561005191968.png",
        "http://twst.isart.me/avatar/20181228985497511813.png",
        "http://twst.isart.me/avatar/20181228544948538360.png",
        "http://twst.isart.me/avatar/20181228495010158490.png",
        "http://twst.isart.me/avatar/20181228991009955938.png",
        "http://twst.isart.me/avatar/20181228575552509089.png",
        "http://twst.isart.me/avatar/20181228574953511465.png",
        "http://twst.isart.me/avatar/20181228535648557900.png",
        "http://twst.isart.me/avatar/20181228485310049925.png",
        "http://twst.isart.me/avatar/20181228989710010147.png",
        "http://twst.isart.me/avatar/20181228545752107328.png",
        "http://twst.isart.me/avatar/20181228545553550934.png",
        "http://twst.isart.me/avatar/20181228101574958023.png",
        "http://twst.isart.me/avatar/20181228974810219408.png",
        "http://twst.isart.me/avatar/20181228985455105794.png",
        "http://twst.isart.me/avatar/20181228545510148330.png",
        "http://twst.isart.me/avatar/20181228101555492084.png",
        "http://twst.isart.me/avatar/20181228505052996693.png",
        "http://twst.isart.me/avatar/20181228499748978312.png",
        "http://twst.isart.me/avatar/20181228102559813344.png",
        "http://twst.isart.me/avatar/20181228100505293082.png",
        "http://twst.isart.me/avatar/20181228575251979992.png",
        "http://twst.isart.me/avatar/20181228991014858187.png",
        "http://twst.isart.me/avatar/20181228525797982024.png",
        "http://twst.isart.me/avatar/20181228481014850467.png",
        "http://twst.isart.me/avatar/20181228985353997076.png",
        "http://twst.isart.me/avatar/20181228561009850615.png",
        "http://twst.isart.me/avatar/20181228525449981198.png",
        "http://twst.isart.me/avatar/20181228101545353115.png",
        "http://twst.isart.me/avatar/20181228984997567119.png",
        "http://twst.isart.me/avatar/20181228975749102628.png",
        "http://twst.isart.me/avatar/20181228505353554067.png",
        "http://twst.isart.me/avatar/20181228102575114233.png",
        "http://twst.isart.me/avatar/20181228985151997085.png",
        "http://twst.isart.me/avatar/20181228559910242976.png",
        "http://twst.isart.me/avatar/20181228555010050479.png",
        "http://twst.isart.me/avatar/20181228975756553400.png",
        "http://twst.isart.me/avatar/20181228565256106000.png",
        "http://twst.isart.me/avatar/20181228519710255652.png",
        "http://twst.isart.me/avatar/20181228974910259439.png",
        "http://twst.isart.me/avatar/20181228549748549145.png",
        "http://twst.isart.me/avatar/20181228505254974767.png",
        "http://twst.isart.me/avatar/20181228101525258502.png",
        "http://twst.isart.me/avatar/20181228565255537334.png",
        "http://twst.isart.me/avatar/20181228515456100863.png",
        "http://twst.isart.me/avatar/20181228575056998054.png",
        "http://twst.isart.me/avatar/20181228565210056972.png",
        "http://twst.isart.me/avatar/20181228504910117936.png",
        "http://twst.isart.me/avatar/20181228101485759080.png",
        "http://twst.isart.me/avatar/20181228565498108993.png",
        "http://twst.isart.me/avatar/20181228514953535234.png",
        "http://twst.isart.me/avatar/20181228995457972732.png",
        "http://twst.isart.me/avatar/20181228485310299491.png",
        "http://twst.isart.me/avatar/20181228545210113454.png",
        "http://twst.isart.me/avatar/20181228505054532985.png",
        "http://twst.isart.me/avatar/20181228979952107706.png",
        "http://twst.isart.me/avatar/20181228102521016973.png",
        "http://twst.isart.me/avatar/20181228521005057901.png",
        "http://twst.isart.me/avatar/20181228525410147498.png",
        "http://twst.isart.me/avatar/20181228504910216417.png",
        "http://twst.isart.me/avatar/20181228495049490891.png",
        "http://twst.isart.me/avatar/20181228505350504017.png",
        "http://twst.isart.me/avatar/20181228102575255922.png",
        "http://twst.isart.me/avatar/20181228579949547327.png",
        "http://twst.isart.me/avatar/20181228524910090259.png",
        "http://twst.isart.me/avatar/20181228489855977351.png",
        "http://twst.isart.me/avatar/20181228999850562783.png",
        "http://twst.isart.me/avatar/20181228975299572861.png",
        "http://twst.isart.me/avatar/20181228985598106437.png",
        "http://twst.isart.me/avatar/20181228505554102084.png",
        "http://twst.isart.me/avatar/20181228100505059557.png",
    ];

    /*
     * 判断一个对象是不是空
     *
     * By TerryQi
     *
     * 2017-12-23
     *
     */
    public static function isObjNull($obj)
    {
        if ($obj === null || $obj === "") {
            return true;
        }
        return false;
    }

    /*
     * 生成订单号-增加4个随机数位置
     *
     * By TerryQi
     *
     * 2017-01-12
     *
     */
    public static function generateTradeNo()
    {
        $trade_no = date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        return $trade_no . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
    }

    /*
     * 获取随机数-一般用于验证码
     *
     * By TerryQi
     *
     * 2018-08-19
     */
    public static function getRandNum($length)
    {
        $rand_str = "";
        for ($i = 0; $i < $length; $i++) {
            $rand_str = $rand_str . rand(0, 9);
        }
        return $rand_str;
    }


    /*
     * 获取范围内的随机数
     *
     * By TerryQi
     *
     * 2018-08-25
     */
    public static function getRandInRang($start, $end)
    {
        return rand($start, $end);
    }


    /**
     * @param $url 请求网址
     * @param bool $params 请求参数
     * @param int $ispost 请求方式
     * @param int $https https协议
     * @return bool|mixed
     */
    public static function curl($url, $params = false, $ispost = 0, $https = 0)
    {
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
        }
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                if (is_array($params)) {
                    $params = http_build_query($params);
                }
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }

        $response = curl_exec($ch);

        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }


    /*
     * 返回圆角图片
     *
     * By TerryQi
     *
     * 2018-10-11
     *
     * img_path：图片路径，可以是网络图片的路径
     */
    public static function getCirclePic($img_path)
    {
        $src_img = imagecreatefromjpeg($img_path);
        list($width, $height) = getimagesize($img_path);
        $width = min($width, $height);
        $height = $width;
        $img = imagecreatetruecolor($width, $height);
        imagesavealpha($img, true);
        //拾取一个完全透明的颜色,最后一个参数127为全透明
        $bg = imagecolorallocatealpha($img, 255, 255, 255, 0);
        imagefill($img, 0, 0, $bg);
        $r = $width / 2; //圆半径
        $y_x = $r; //圆心X坐标
        $y_y = $r; //圆心Y坐标
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgbColor = imagecolorat($src_img, $x, $y);
                if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
            }
        }
        imagedestroy($src_img);
        return $img;
    }

    //缩放图片
    /*
     * im 需要缩放的图片 maxwith最大宽度 maxheight最大高度
     *
     *
     */
    public static function resizeImage($im, $maxwidth, $maxheight)
    {
        $pic_width = imagesx($im);
        $pic_height = imagesy($im);

        if (($maxwidth && $pic_width > $maxwidth) || ($maxheight && $pic_height > $maxheight)) {
            if ($maxwidth && $pic_width > $maxwidth) {
                $widthratio = $maxwidth / $pic_width;
                $resizewidth_tag = true;
            }

            if ($maxheight && $pic_height > $maxheight) {
                $heightratio = $maxheight / $pic_height;
                $resizeheight_tag = true;
            }

            if ($resizewidth_tag && $resizeheight_tag) {
                if ($widthratio < $heightratio)
                    $ratio = $widthratio;
                else
                    $ratio = $heightratio;
            }

            if ($resizewidth_tag && !$resizeheight_tag)
                $ratio = $widthratio;
            if ($resizeheight_tag && !$resizewidth_tag)
                $ratio = $heightratio;

            $newwidth = $pic_width * $ratio;
            $newheight = $pic_height * $ratio;

            if (function_exists("imagecopyresampled")) {
                $newim = imagecreatetruecolor($newwidth, $newheight);
                imagecopyresampled($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
            } else {
                $newim = imagecreate($newwidth, $newheight);
                imagecopyresized($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
            }
            return $newim;
        } else {
            return $im;
        }
    }


    /*
     * 下载图片方法
     *
     * By TerryQi
     *
     * 2018-10-11
     *
     * url：下载链接  path：下载路径  filename：文件名
     *
     */
    public static function downloadFile($url, $path, $filename)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $file = curl_exec($ch);
        curl_close($ch);

        $filename = $filename;
        $resource = fopen($path . '/' . $filename, 'a');
        fwrite($resource, $file);
        fclose($resource);

        return $filename;
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///
    /// log相关
    ///
    ///
    /**
     * 请求接口LOG
     * @param string $logPath 请求接口
     * @param string $logIp IP地址
     * @param array $logData 请求参数
     */
    public static function requestLog($logPath = "", $logIp = "", $logData = [])
    {
        $LOGO_NO = 'LOG' . date('Ymdhis', time()) . rand(1000000, 10000000);
        Session::put('LOGO_NO', $LOGO_NO);
        Log::info('[Request]  ' . $LOGO_NO . '  ' . $logPath . "(" . $logIp . ")   " . json_encode($logData));
    }

    /**
     * 过程中接口LOG
     * @param string $logModular 模块
     * @param string $logData 数据
     * @param string $logContent 备注
     */
    public static function processLog($logModular = "", $logContent = "", $logData = "")
    {
        $LOGO_NO = Session::get("LOGO_NO");
        if (is_array($logData)) {
            $logData = json_encode($logData, true);
        }
        if ($logContent) {
            Log::info('[Process]  ' . $LOGO_NO . '  ' . $logContent . '  ' . $logModular . '  ' . $logData);
        } else {
            Log::info('[Process]  ' . $LOGO_NO . '  ' . $logModular . '  ' . $logData);
        }
    }

    /**
     * 过程报错接口LOG
     * @param string $logData 数据
     */
    public static function errorLog($logData = "")
    {
        $LOGO_NO = Session::get("LOGO_NO");
        if (!$LOGO_NO) {
            $LOGO_NO = 'LOG' . date('Ymdhis', time()) . rand(1000000, 10000000);
            Session::put('LOGO_NO', $LOGO_NO);
        }
        if (is_array($logData)) {
            $logData = json_encode($logData, true);
        }
        Log::info('[Error]  ' . $LOGO_NO . '  ' . $logData);
        Session::remove("LOGO_NO");
    }

    /**
     * 返回接口LOG
     * @param string $logModular 模块
     * @param array $logData 数据
     */
    public static function backLog($logModular = "", $logData = [])
    {
        $LOGO_NO = Session::get("LOGO_NO");
        $log = array(
            'code' => $logData['code'],
            'result' => $logData['result'],
            'message' => $logData['message'],
        );
        if (array_key_exists('ret', $logData)) {
            $log['ret'] = $logData['ret'];
        }
        Log::info('[Back]  ' . $LOGO_NO . '  ' . $logModular . '  ' . json_encode($log, true));
        Session::remove("LOGO_NO");
    }

    /**
     * 自定义LOG
     * @param string $label log标签
     * @param string $logContent 备注
     * @param string/array $logData 数据
     */
    public static function customLog($label = "DEBUG", $logContent = "", $logData = "")
    {
        $LOGO_NO = Session::get("LOGO_NO");
        if (!$LOGO_NO) {
            $LOGO_NO = 'LOG' . date('Ymdhis', time()) . rand(1000000, 10000000);
            Session::put('LOGO_NO', $LOGO_NO);
        }
        if (is_array($logData)) {
            // 将数组转为字符串
            $logDataArray = $logData;
            $logData = '';
            foreach ($logDataArray as $key => $logDataRow) {
                if (is_array($logDataRow)) {
                    $logDataRow = json_encode($logDataRow, true);
                }
                $str = $key . "：" . $logDataRow;
                $logData .= $str . '  ';
            }
        }
        if ($logContent) {
            Log::info('[' . $label . ']  ' . $LOGO_NO . '  ' . $logContent . '  ' . $logData);
        } else {
            Log::info('[' . $label . ']  ' . $LOGO_NO . '  ' . $logData);
        }
        Session::remove("LOGO_NO");
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
     * 统计数据补领工具
     *
     * By TerryQi
     *
     * 要求数组的元素格式为 ["data"=>$data,'value'=>$value]，data代表日期、value代表具体的值
     *
     * 入参要求
     *
     * @arr 数组样式：必须参考元素格式，即data和value的格式
     * @start_at 开始日期，必须为2018-11-28的格式
     * @end_at 结束日期，必须为2018-12-01的格式
     *
     * 输出数组，将日期空缺补零
     *
     */
    public static function replZero($arr, $start_at, $end_at)
    {
        $nums = DateTool::dateDiff('D', $start_at, $end_at);
        //进行补零动作
        $replZero_arr = [];        //处理后的数组
        for ($i = 0; $i < $nums; $i++) {
            $date_at = DateTool::dateAdd('D', $i, $start_at, 'Y-m-d');
            //代表有值
            $index = self::isDateInStatisArr($date_at, $arr);
            $item = null;
            if ($index != -1) {
                $item = array(
                    'date' => $arr[$index]['date'],
                    'value' => $arr[$index]['value']
                );
            } else {
                $item = array(
                    'date' => $date_at,
                    'value' => 0
                );
            }
            array_push($replZero_arr, $item);
        }
        return $replZero_arr;
    }


    //配合replZero使用，判断arr中有没有data_at的数据
    //如果有返回i，即具体位置，否则返回-1
    //该方法能够仅限于支撑replZero方法
    /*
     * By TerryQi
     *
     * @date_at 为arr中date的具体指，arr为数组，格式为[["data"=>$data,'value'=>$value],.....]
     *
     * @return 如果有值返回 index，如果没有值返回-1
     */
    public static function isDateInStatisArr($date_at, $arr)
    {
        //循环数组
        for ($i = 0; $i < count($arr); $i++) {
            if ($arr[$i]['date'] == $date_at) {
                return $i;
            }
        }
        return -1;
    }

    /*
     * 字符串截取
     *
     * By TerryQi
     *
     * 2018-12-05
     *
     * @str：需要截取的字符串 start：开始位置 length：长度 chartset：字符集 suffix 结束符
     */

    public static function substr_text($str, $start = 0, $length, $charset = "utf-8", $suffix = "")
    {
        if (function_exists("mb_substr")) {
            return mb_substr($str, $start, $length, $charset) . $suffix;
        } elseif (function_exists('iconv_substr')) {
            return iconv_substr($str, $start, $length, $charset) . $suffix;
        }
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
        return $slice . $suffix;
    }

    /*
     * 配置文字自动换行
     *
     * By TerryQi
     *
     * 2018-12-05
     *
     * 字体大小:fontsize, 角度:angle, 字体名称:fontface, 字符串:string, 预设宽度:width
     *
     */
    public static function autowrap($fontsize, $angle, $fontface, $string, $width)
    {
        $content = "";

        // 将字符串拆分成一个个单字 保存到数组 letter 中
        for ($i = 0; $i < mb_strlen($string); $i++) {
            $letter[] = mb_substr($string, $i, 1);
        }

        foreach ($letter as $l) {
            $teststr = $content . " " . $l;
            $testbox = imagettfbbox($fontsize, $angle, $fontface, $teststr);
            // 判断拼接后的字符串是否超过预设的宽度
            if (($testbox[2] > $width) && ($content !== "")) {
                $content .= "\n";
            }
            $content .= $l;
        }

        return $content;
    }


}