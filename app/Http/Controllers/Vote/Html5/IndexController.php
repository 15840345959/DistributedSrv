<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2017/10/3
 * Time: 0:38
 */

namespace App\Http\Controllers\Vote\Html5;

use App\Components\ADManager;
use App\Components\AdminManager;
use App\Components\BusiWordManager;
use App\Components\QNManager;
use App\Components\UserManager;
use App\Components\Vote\VoteActivityManager;
use App\Components\Vote\VoteADManager;
use App\Components\Vote\VoteGuanZhuManager;
use App\Components\Vote\VoteOrderManager;
use App\Components\Vote\VoteRecordManager;
use App\Components\Vote\VoteUserManager;
use App\Http\Controllers\Admin\Vote\VoteOrderController;
use App\Libs\CommonUtils;
use App\Models\Vote\VoteActivity;
use App\Models\Vote\VoteOrder;
use App\Models\Vote\VoteTeam;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Components\RequestValidator;
use App\Models\Admin;
use EasyWeChat\Kernel\Messages\Image;
use Illuminate\Support\Facades\Log;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;

class IndexController
{

    //相关配置
    const BUSI_NAME = "isart";      //业务名称

    /*
     * 投票大赛首页
     *
     * By TerryQi
     *
     * 2018-07-18
     */
    public function index(Request $request)
    {
        $data = $request->all();
        //是否为本地调测，如果本地调测，则默认用户信息
        if (env('FWH_LOCAL_DEBUG')) {
            $user = UserManager::getById('11');
        } else {
            $session_val = session('wechat.oauth_user'); // 拿到授权用户资料
            $user_data = UserManager::convertSessionValToUserData($session_val, self::BUSI_NAME);
            //进行用户登录
            $user = UserManager::login(Utils::ACCOUNT_TYPE_FWH, $user_data);
            if ($user == null) {
                return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '用户信息错误，请10分钟后再试']);
            }
        }
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'activity_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '缺少活动编码']);
        }
        //活动基本信息
        $activity = VoteActivityManager::getById($data['activity_id']);
        if (!$activity) {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '没有找到活动']);
        }
        //活动被冻结
        if ($activity->status == '0') {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '该活动被冻结']);
        }
        $activity = VoteActivityManager::getInfoByLevel($activity, '0');
        //活动真实参与用户数（审核通过）
        $activity->real_join_num = VoteUserManager::getListByCon(['activity_id' => $activity->id, 'audit_status' => '1', 'status' => '1'], false)->count();
        //可能的搜索项目
        $search_word = null;
        //选手排序-默认按照展示数-顺序排序
        //2018.11.21 阿伟提出默认按照时间倒序
        $vote_user_order_by = array(
            'created_at' => 'desc'
        );
        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }
        if (array_key_exists('vote_user_order_by', $data) && !Utils::isObjNull($data['vote_user_order_by'])) {
            //按照时间排序
            if ($data['vote_user_order_by'] == "created_at") {
                $vote_user_order_by = array(
                    'created_at' => 'desc'
                );
            }
            //按照投票排序
            if ($data['vote_user_order_by'] == "vote_num") {
                $vote_user_order_by = array(
                    'vote_num' => 'desc'
                );
            }
            //按照展示次数排序
            if ($data['vote_user_order_by'] == "show_num") {
                $vote_user_order_by = array(
                    'show_num' => 'asc'
                );
            }

        }

        //审核通过的参赛选手信息
        $con_arr = array(
            'activity_id' => $activity->id,
            'audit_status' => '1',
            'status' => '1',
            'search_word_by_code_or_name' => $search_word,
            'orderby' => $vote_user_order_by
        );
        $vote_users = VoteUserManager::getListByCon($con_arr, false);
        $i = 0;
        foreach ($vote_users as $vote_user) {
            $vote_user = VoteUserManager::getInfoByLevel($vote_user, '0');
            //大于6个就不展示了
            if (($i++) >= 6) {
                $vote_user->show_flag = false;
            } else {
                $vote_user->show_flag = true;
            }
        }
        //首页广告
        $index_ads = VoteActivityManager::getIndexAdArr($activity->id);

        //进行数据统计
        VoteActivityManager::addStatistics($activity->id, 'show_num', 1);

        //生成微信JS-SDK相关
        $wxConfig = null;
        if (!env('FWH_LOCAL_DEBUG')) {
            $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL[self::BUSI_NAME]);
            $wxConfig = $app->jssdk->buildConfig(array('onMenuShareTimeline', 'onMenuShareAppMessage'), false);
            Utils::processLog(__METHOD__, '',  "wxConfig:" . json_encode($wxConfig));
        }
//        dd($con_arr);

        return view('vote.html5.index', ['user' => $user, 'index_ads' => $index_ads
            , 'activity' => $activity, 'vote_users' => $vote_users, 'wxConfig' => $wxConfig, 'con_arr' => $con_arr]);
    }


    /*
     * 错误页面
     *
     * By TerryQi
     *
     * 2018-07-18
     */
    public function error(Request $request)
    {
        $data = $request->all();
        $msg = null;
        if (array_key_exists('msg', $data)) {
            $msg = $data['msg'];
        }

        return view('vote.html5.error.error', ['msg' => $msg]);
    }


    /*
     * 投票大赛区域页面
     *
     * By TerryQi
     *
     * 2018-07-18
     */
    public function zone(Request $request)
    {
        $data = $request->all();
        //是否为本地调测，如果本地调测，则默认用户信息
        if (env('FWH_LOCAL_DEBUG')) {
            $user = UserManager::getById('11');
        } else {
            $session_val = session('wechat.oauth_user'); // 拿到授权用户资料
            $user_data = UserManager::convertSessionValToUserData($session_val, self::BUSI_NAME);
            //进行用户登录
            $user = UserManager::login(Utils::ACCOUNT_TYPE_FWH, $user_data);
            if ($user == null) {
                return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '用户信息错误，请10分钟后再试']);
            }
        }
        //获取全部活动信息
        $con_arr = array(
            'status' => '1'
        );
        $activitys = VoteActivityManager::getListByCon($con_arr, false);

        //生成微信JS-SDK相关
        $wxConfig = null;
        if (!env('FWH_LOCAL_DEBUG')) {
            $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL[self::BUSI_NAME]);
            $wxConfig = $app->jssdk->buildConfig(array('onMenuShareTimeline', 'onMenuShareAppMessage'), false);
            Utils::processLog(__METHOD__, '',  "wxConfig:" . json_encode($wxConfig));
        }

        return view('vote.html5.zone', ['user' => $user, 'activities' => $activitys, 'wxConfig' => $wxConfig]);
    }

    /*
     * 大赛个人页面
     *
     * By TerryQi
     *
     * 2018-07-19
     *
     */
    public function person(Request $request)
    {
        $data = $request->all();
        //是否为本地调测，如果本地调测，则默认用户信息
        if (env('FWH_LOCAL_DEBUG')) {
            $user = UserManager::getById('11');
        } else {
            $session_val = session('wechat.oauth_user'); // 拿到授权用户资料
            $user_data = UserManager::convertSessionValToUserData($session_val, self::BUSI_NAME);
            //进行用户登录
            $user = UserManager::login(Utils::ACCOUNT_TYPE_FWH, $user_data);
            if ($user == null) {
                return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '用户信息错误，请10分钟后再试']);
            }
        }
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'vote_user_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '缺少选手编码']);
        }
        //选手基本信息
        $vote_user = VoteUserManager::getById($data['vote_user_id']);
        if (!$vote_user) {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '没有找到选手信息']);
        }
        //如果选手被驳回
        /*
         * 此处调整逻辑，即用户只有被驳回才跳转到异常页面，如果用户刚刚报名，也可以跳转至个人页面
         *
         * By TerryQi
         *
         * 2018-11-23
         */
        if ($vote_user->audit_status == '2') {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '选手信息未审核通过']);
        }
        //如果选手被冻结
        if ($vote_user->status != '1') {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '选手被冻结']);
        }
        $vote_user = VoteUserManager::getInfoByLevel($vote_user, '02');
        //设置关注标识
        $vote_user->guanzhu_flag = VoteGuanZhuManager::isAGuanZhuB($user->id, $vote_user->id);
        //获取相差票数
        $con_arr = array(
            'activity_id' => $vote_user->activity_id,
            'bigger_than_vote_num' => $vote_user->vote_num,
            'orderby' => [
                'vote_num' => 'asc'
            ]
        );
//        dd($con_arr);
        $pre_vote_user = VoteUserManager::getListByCon($con_arr, false)->first();
//        dd($pre_vote_user);
        if (!$pre_vote_user) {
            $vote_user->less_vote_num = 0;
        } else {
            $vote_user->less_vote_num = $pre_vote_user->vote_num - $vote_user->vote_num;
        }
        //设置是否需要开启验证码
        $con_arr = array(
            'vote_user_id' => $vote_user->id,
            'at_date' => date("Y-m-d"),
        );
        $today_vote_num = VoteRecordManager::getListByCon($con_arr, false)->count();
        if ($today_vote_num >= Utils::DAILY_START_VOTE_VERTIFY_NUM) {
            $vote_user->need_vote_valid_flag = true;
        } else {
            $vote_user->need_vote_valid_flag = false;
        }
        //测试用
//        $vote_user->need_vote_valid_flag = true;

//        dd($vote_user);
        //活动基本信息
        $activity = VoteActivityManager::getById($vote_user->activity_id);
        //活动被冻结
        if ($activity->status == '0') {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '该活动被冻结']);
        }
        $activity = VoteActivityManager::getInfoByLevel($activity, '0');
//        dd($activity);
        //首页广告
        $index_ads = VoteActivityManager::getIndexAdArr($activity->id);
        //投票成功广告
        $tp_ads = VoteActivityManager::getTPAdArr($activity->id);
        $tp_ad = null;
        //随机获取一条投票成功提示数据
        if ($tp_ads) {
            $rand_index = array_rand($tp_ads, 1);
            $tp_ad = $tp_ads[$rand_index];
        }
//        dd($tp_ad);
        //生成微信JS-SDK相关
        $wxConfig = null;
        if (!env('FWH_LOCAL_DEBUG')) {
            $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL[self::BUSI_NAME]);
            $wxConfig = $app->jssdk->buildConfig(array('onMenuShareTimeline', 'onMenuShareAppMessage', 'chooseWXPay'), false);
            Utils::processLog(__METHOD__, '', "wxConfig:" . json_encode($wxConfig));
        }

        //排名前三的付费用户
        $group_by_user_orders = VoteOrderManager::groupByUserList($vote_user->id);
        $i = 0;
        foreach ($group_by_user_orders as $group_by_user_order) {
            $group_by_user_order = VoteOrderManager::getInfoByLevel($group_by_user_order, '2');
            $group_by_user_order->pm = (++$i);
        }
//        dd($group_by_user_orders);
        //订单明细
        $orders = VoteOrderManager::getListByCon(['vote_user_id' => $vote_user->id, 'pay_status' => '1'], false);
        $i = 0;
        foreach ($orders as $order) {
            $order = VoteOrderManager::getInfoByLevel($order, '23');
            //大于6个就不展示了
            if (($i++) >= 6) {
                $order->show_flag = false;
            } else {
                $order->show_flag = true;
            }
        }
//        dd($orders);
        //统计数据
        VoteUserManager::addStatistics($vote_user->id, 'show_num', 1);
        VoteActivityManager::addStatistics($activity->id, 'show_num', 1);

        return view('vote.html5.person', ['vote_user' => $vote_user, 'user' => $user, 'activity' => $activity
            , 'index_ads' => $index_ads, 'wxConfig' => $wxConfig, 'tp_ad' => $tp_ad, 'group_by_user_orders' => $group_by_user_orders, 'orders' => $orders]);
    }


    /*
     * 大赛个人用分享页面
     *
     * By TerryQi
     *
     * 2018-07-27
     */
    public function personShare(Request $request)
    {
        return view('vote.html5.personShare', []);
    }


    /*
     * 大赛关注成功页面
     *
     * By TerryQi
     *
     * 2018-07-19
     *
     */
    public function guanzhu(Request $request)
    {
        $data = $request->all();
        //是否为本地调测，如果本地调测，则默认用户信息
        if (env('FWH_LOCAL_DEBUG')) {
            $user = UserManager::getById('11');
        } else {
            $session_val = session('wechat.oauth_user'); // 拿到授权用户资料
            $user_data = UserManager::convertSessionValToUserData($session_val, self::BUSI_NAME);
            //进行用户登录
            $user = UserManager::login(Utils::ACCOUNT_TYPE_FWH, $user_data);
            if ($user == null) {
                return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '用户信息错误，请10分钟后再试']);
            }
        }
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'vote_user_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '缺少选手编码']);
        }
        //选手基本信息
        $vote_user = VoteUserManager::getById($data['vote_user_id']);
        if (!$vote_user) {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '没有找到选手信息']);
        }
        $vote_user = VoteUserManager::getInfoByLevel($vote_user, '02');
        //活动信息
        $activity = VoteActivityManager::getById($vote_user->activity_id);
        //活动被冻结
        if ($activity->status == '0') {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '该活动被冻结']);
        }
        $activity = VoteActivityManager::getInfoByLevel($activity, '');

//        dd($tp_ad);
        //生成微信JS-SDK相关
        $wxConfig = null;
        if (!env('FWH_LOCAL_DEBUG')) {
            $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL[self::BUSI_NAME]);
            $wxConfig = $app->jssdk->buildConfig(array('onMenuShareTimeline', 'onMenuShareAppMessage'), false);
            Utils::processLog(__METHOD__, '', "wxConfig:" . json_encode($wxConfig));
        }

        //统计数据
        VoteUserManager::addStatistics($vote_user->id, 'show_num', 1);

        return view('vote.html5.guanzhu', ['vote_user' => $vote_user, 'user' => $user
            , 'activity' => $activity, 'wxConfig' => $wxConfig]);
    }

    /*
     * 礼品页面
     *
     * By TerryQi
     *
     * 2018-07-19
     */
    public function present(Request $request)
    {
        $data = $request->all();
        //是否为本地调测，如果本地调测，则默认用户信息
        if (env('FWH_LOCAL_DEBUG')) {
            $user = UserManager::getById('11');
        } else {
            $session_val = session('wechat.oauth_user'); // 拿到授权用户资料
            $user_data = UserManager::convertSessionValToUserData($session_val, self::BUSI_NAME);
            //进行用户登录
            $user = UserManager::login(Utils::ACCOUNT_TYPE_FWH, $user_data);
            if ($user == null) {
                return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '用户信息错误，请10分钟后再试']);
            }
        }
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'activity_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '缺少活动编码']);
        }
        //活动基本信息
        $activity = VoteActivityManager::getById($data['activity_id']);
        if (!$activity) {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '没有找到活动']);
        }
        //活动被冻结
        if ($activity->status == '0') {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '该活动被冻结']);
        }
        $activity = VoteActivityManager::getInfoByLevel($activity, '');
        //进行数据统计
        VoteActivityManager::addStatistics($activity->id, 'show_num', 1);

        //生成微信JS-SDK相关
        $wxConfig = null;
        if (!env('FWH_LOCAL_DEBUG')) {
            $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL[self::BUSI_NAME]);
            $wxConfig = $app->jssdk->buildConfig(array('onMenuShareTimeline', 'onMenuShareAppMessage'), false);
            Utils::processLog(__METHOD__, '', "wxConfig:" . json_encode($wxConfig));
        }

        return view('vote.html5.present', ['user' => $user, 'activity' => $activity, 'wxConfig' => $wxConfig]);
    }


    /*
    * 活动说明页面
    *
    * By TerryQi
    *
    * 2018-07-19
    */
    public function intro(Request $request)
    {
        $data = $request->all();
        //是否为本地调测，如果本地调测，则默认用户信息
        if (env('FWH_LOCAL_DEBUG')) {
            $user = UserManager::getById('11');
        } else {
            $session_val = session('wechat.oauth_user'); // 拿到授权用户资料
            $user_data = UserManager::convertSessionValToUserData($session_val, self::BUSI_NAME);
            //进行用户登录
            $user = UserManager::login(Utils::ACCOUNT_TYPE_FWH, $user_data);
            if ($user == null) {
                return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '用户信息错误，请10分钟后再试']);
            }
        }
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'activity_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '缺少活动编码']);
        }
        //活动基本信息
        $activity = VoteActivityManager::getById($data['activity_id']);
        if (!$activity) {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '没有找到活动']);
        }
        //活动被冻结
        if ($activity->status == '0') {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '该活动被冻结']);
        }
        $activity = VoteActivityManager::getInfoByLevel($activity, '');
        //进行数据统计
        VoteActivityManager::addStatistics($activity->id, 'show_num', 1);

        //生成微信JS-SDK相关
        $wxConfig = null;
        if (!env('FWH_LOCAL_DEBUG')) {
            $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL[self::BUSI_NAME]);
            $wxConfig = $app->jssdk->buildConfig(array('onMenuShareTimeline', 'onMenuShareAppMessage'), false);
            Utils::processLog(__METHOD__, '', "wxConfig:" . json_encode($wxConfig));
        }

        return view('vote.html5.intro', ['user' => $user, 'activity' => $activity, 'wxConfig' => $wxConfig]);
    }


    /*
     * 列表页面
     *
     * By TerryQi
     *
     * 2018-07-19
     */
    public function list(Request $request)
    {
        $data = $request->all();
        //是否为本地调测，如果本地调测，则默认用户信息
        if (env('FWH_LOCAL_DEBUG')) {
            $user = UserManager::getById('11');
        } else {
            $session_val = session('wechat.oauth_user'); // 拿到授权用户资料
            $user_data = UserManager::convertSessionValToUserData($session_val, self::BUSI_NAME);
            //进行用户登录
            $user = UserManager::login(Utils::ACCOUNT_TYPE_FWH, $user_data);
            if ($user == null) {
                return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '用户信息错误，请10分钟后再试']);
            }
        }

        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'activity_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '缺少活动编码']);
        }
        //活动基本信息
        $activity = VoteActivityManager::getById($data['activity_id']);
        if (!$activity) {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '没有找到活动']);
        }
        //活动被冻结
        if ($activity->status == '0') {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '该活动被冻结']);
        }
        //首页广告
        $pm_ads = VoteActivityManager::getPMAdArr($activity->id);
//        dd($pm_ads);
        //获取选手列表
        $con_arr = array(
            'activity_id' => $activity->id,
            'audit_status' => '1',
            'status' => '1',
            'orderby' => [
                'vote_num' => 'desc'
            ]
        );
        $vote_users = VoteUserManager::getListByCon($con_arr, false);
        $i = 0;
        foreach ($vote_users as $vote_user) {
            $vote_user = VoteUserManager::getInfoByLevel($vote_user, '0');
            $curr_pm = (++$i);
            $vote_user->pm_bd = $vote_user->yes_pm - $curr_pm;        //设置排名变动信息
            $vote_user->curr_pm = $curr_pm;                               //设置当前排名
            //设置文字排名-活动已经结束才需要设置
            if ($activity->vote_status == '2') {

                $first_prize_num = $activity->first_prize_num;
                $second_prize_num = $activity->second_prize_num;
                $third_prize_num = $activity->third_prize_num;
                $honor_prize_num = $activity->honor_prize_num;


                $prize = VoteUserManager::getPrize($vote_user->id);

                $vote_user->pm_str = $prize;
            }
        }
//        dd($vote_users);
        //进行数据统计
        VoteActivityManager::addStatistics($activity->id, 'show_num', 1);

        //生成微信JS-SDK相关
        $wxConfig = null;
        if (!env('FWH_LOCAL_DEBUG')) {
            $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL[self::BUSI_NAME]);
            $wxConfig = $app->jssdk->buildConfig(array('onMenuShareTimeline', 'onMenuShareAppMessage'), false);
            Utils::processLog(__METHOD__, '', "wxConfig:" . json_encode($wxConfig));
        }

        return view('vote.html5.list', ['user' => $user, 'activity' => $activity, 'pm_ads' => $pm_ads
            , 'vote_users' => $vote_users, 'wxConfig' => $wxConfig]);
    }


    /*
     * 投诉页面
     *
     * By TerryQi
     *
     * 2018-07-19
     */
    public function complain(Request $request)
    {
        $data = $request->all();
        //是否为本地调测，如果本地调测，则默认用户信息
        if (env('FWH_LOCAL_DEBUG')) {
            $user = UserManager::getById('11');
        } else {
            $session_val = session('wechat.oauth_user'); // 拿到授权用户资料
            $user_data = UserManager::convertSessionValToUserData($session_val, self::BUSI_NAME);
            //进行用户登录
            $user = UserManager::login(Utils::ACCOUNT_TYPE_FWH, $user_data);
            if ($user == null) {
                return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '用户信息错误，请10分钟后再试']);
            }
        }
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'activity_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '缺少活动编码']);
        }
        //封装参数
        $vote_user_id = null;
        $activity_id = $data['activity_id'];
        $activity = VoteActivityManager::getById($activity_id);
        if (array_key_exists('vote_user_id', $data) && !Utils::isObjNull($data['vote_user_id'])) {
            $vote_user_id = $data['vote_user_id'];
        }
        $con_arr = array(
            'activity_id' => $activity_id,
            'vote_user_id' => $vote_user_id,
        );

        //生成微信JS-SDK相关
        $wxConfig = null;
        if (!env('FWH_LOCAL_DEBUG')) {
            $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL[self::BUSI_NAME]);
            $wxConfig = $app->jssdk->buildConfig(array('onMenuShareTimeline', 'onMenuShareAppMessage'), false);
            Utils::processLog(__METHOD__, '', "wxConfig:" . json_encode($wxConfig));
        }

        return view('vote.html5.complain', ['user' => $user, 'con_arr' => $con_arr, 'activity' => $activity, 'wxConfig' => $wxConfig]);
    }

    /*
     * 报名页面
     *
     * By TerryQi
     *
     * 2018-07-21
     *
     */
    public function apply(Request $request)
    {
        $data = $request->all();
        //是否为本地调测，如果本地调测，则默认用户信息
        if (env('FWH_LOCAL_DEBUG')) {
            $user = UserManager::getById('11');
        } else {
            $session_val = session('wechat.oauth_user'); // 拿到授权用户资料
            $user_data = UserManager::convertSessionValToUserData($session_val, self::BUSI_NAME);
            //进行用户登录
            $user = UserManager::login(Utils::ACCOUNT_TYPE_FWH, $user_data);
            if ($user == null) {
                return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '用户信息错误，请10分钟后再试']);
            }
        }
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'activity_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '缺少活动编码']);
        }
        //活动基本信息
        $activity = VoteActivityManager::getById($data['activity_id']);
        if (!$activity) {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '没有找到活动']);
        }
        //活动被冻结
        if ($activity->status == '0') {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '该活动被冻结']);
        }
        //生成七牛token
        $upload_token = QNManager::uploadToken();

        //生成微信JS-SDK相关
        $wxConfig = null;
        if (!env('FWH_LOCAL_DEBUG')) {
            $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL[self::BUSI_NAME]);
            $wxConfig = $app->jssdk->buildConfig(array('onMenuShareTimeline', 'onMenuShareAppMessage'), false);
            Utils::processLog(__METHOD__, '', "wxConfig:" . json_encode($wxConfig));
        }

        //是否为机构报名
        //通过jg_flag来判断是否为机构报名 0:非机构报名-个人报名 1：机构报名
        if (array_key_exists('jg_flag', $data) && $data['jg_flag'] == "1") {
            $view = 'vote.html5.jg_apply';
        } else {
            $view = 'vote.html5.apply';
        }

        return view($view, ['user' => $user, 'activity' => $activity,
            'upload_token' => $upload_token, 'wxConfig' => $wxConfig]);
    }


    /*
     * 团队查看结算信息页面
     *
     * By 智强
     *
     * 2018-09-13
     *
     */
    public function team(Request $request)
    {
        $data = $request->all();

        //是否为本地调测，如果本地调测，则默认用户信息
        if (env('FWH_LOCAL_DEBUG')) {
            $user = UserManager::getById('11');
        } else {
            $session_val = session('wechat.oauth_user'); // 拿到授权用户资料
            $user_data = UserManager::convertSessionValToUserData($session_val, self::BUSI_NAME);
            //进行用户登录
            $user = UserManager::login(Utils::ACCOUNT_TYPE_FWH, $user_data);
            if ($user == null) {
                return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '用户信息错误，请10分钟后再试']);
            }
        }
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'team_id' => 'required'
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '缺少团队编码']);
        }

        $team = VoteTeam::find(array_get($data, 'team_id'));

        if (!$team) {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '没有找到团队']);
        }

        if ($team->status === '0') {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '该团队已被禁用']);
        }

        $ongoing_activity = VoteActivity::withCount('vote_user')
            ->where('gift_money', '<', $team->amount ? $team->amount : 500)
            ->whereNotNull('apply_start_time')
            ->whereNotNull('vote_end_time')
            ->where('vote_team_id', $team->id)
            ->where('status', 1);

        $not_settle_activity = VoteActivity::withCount('vote_user')
            ->whereNotNull('apply_start_time')
            ->whereNotNull('vote_end_time')
            ->where('vote_team_id', $team->id)
            ->where('gift_money', '>=', $team->amount ? $team->amount : 500)
            ->where('is_settle', 0)
            ->where('status', 1);

        $settle_activity = VoteActivity::withCount('vote_user')
            ->whereNotNull('apply_start_time')
            ->whereNotNull('vote_end_time')
            ->where('vote_team_id', $team->id)
            ->where('is_settle', 1)
            ->where('status', 1);

        $activity_count = [
            'ongoing' => $ongoing_activity->count(),
            'not_settle_activity' => $not_settle_activity->count(),
            'settle_activity' => $settle_activity->count()
        ];

        $activity = [
            'ongoing' => $ongoing_activity->get(),
            'not_settle_activity' => $not_settle_activity->get(),
            'settle_activity' => $settle_activity->get()
        ];

        $wxConfig = null;
        if (!env('FWH_LOCAL_DEBUG')) {
            $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL[self::BUSI_NAME]);
            $wxConfig = $app->jssdk->buildConfig(array('onMenuShareTimeline', 'onMenuShareAppMessage'), false);
            Utils::processLog(__METHOD__, '', "wxConfig:" . json_encode($wxConfig));
        }

        return view('vote.html5.team', ['team' => $team, 'activity' => $activity, 'activity_count' => $activity_count, 'team_id' => array_get($data, 'team_id'), 'wxConfig' => $wxConfig]);
    }


    /*
     * 下载证书
     *
     * By TerryQi
     *
     * 2018-09-18
     *
     */
    public function sendCert(Request $request)
    {
        $data = $request->all();

        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'activity_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '缺少活动编码']);
        }
        //活动基本信息
        $activity = VoteActivityManager::getById($data['activity_id']);
        if (!$activity) {
            return redirect()->action('\App\Http\Controllers\Vote\Html5\IndexController@error', ['msg' => '没有找到活动']);
        }

        //如果传入vote_user_id
        $vote_user_id = null;
        //如果vote_user_id存在
        if (array_key_exists('vote_user_id', $data) && !Utils::isObjNull($data['vote_user_id'])) {
            $vote_user_id = $data['vote_user_id'];
        }
        //如果传入vote_user_id
        $vote_user = null;
        if ($vote_user_id) {
            $vote_user = VoteUserManager::getById($vote_user_id);
            $vote_activity = VoteActivityManager::getById($vote_user->activity_id);

            $vote_user->code = $vote_activity->code . "-" . $vote_user->code;       //获取选手编码
            $vote_user->name = VoteUserManager::getVoteUserName($vote_user->name);      //获取选手姓名
        }

        //生成微信JS-SDK相关
        $wxConfig = null;
        if (!env('FWH_LOCAL_DEBUG')) {
            $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL[self::BUSI_NAME]);
            $wxConfig = $app->jssdk->buildConfig(array('onMenuShareTimeline', 'onMenuShareAppMessage'), false);
            Utils::processLog(__METHOD__, '', "wxConfig:" . json_encode($wxConfig));
        }

        return view('vote.html5.sendCert', ['vote_user' => $vote_user, 'wxConfig' => $wxConfig, 'activity' => $activity,]);
    }


}
