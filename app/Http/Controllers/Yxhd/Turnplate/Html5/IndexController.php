<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2017/10/3
 * Time: 0:38
 */

namespace App\Http\Controllers\Yxhd\Turnplate\Html5;

use App\Components\AdminManager;
use App\Components\BusiWordManager;
use App\Components\QNManager;
use App\Components\UserManager;
use App\Components\Vote\VoteActivityManager;
use App\Components\Vote\VoteUserManager;
use App\Components\Yxhd\YxhdActivityManager;
use App\Components\Yxhd\YxhdADManager;
use App\Components\Yxhd\YxhdGuanZhuManager;
use App\Components\Yxhd\YxhdOrderManager;
use App\Components\Yxhd\YxhdPrizeManager;
use App\Components\Yxhd\YxhdPrizeSettingManager;
use App\Components\Yxhd\YxhdRecordManager;
use App\Components\Yxhd\YxhdUserManager;
use App\Http\Controllers\Admin\Yxhd\YxhdOrderController;
use App\Libs\CommonUtils;
use App\Models\Yxhd\YxhdActivity;
use App\Models\Yxhd\YxhdOrder;
use App\Models\Yxhd\YxhdTeam;
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
     * 首页
     *
     * By TerryQi
     *
     * 2018-07-18
     *
     * 该页面传入参数为：
     *
     * @actiivty_id：大转盘活动id
     * @from_page：从哪个页面传入的，现在只有vote_person，即大转盘的活动页面
     * @param1：为页面传入时带入的参数，当from_page==vote_person时，param1为vote_user_id
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
                return redirect()->action('\App\Http\Controllers\Yxhd\Turnplate\Html5\IndexController@error', ['msg' => '用户信息错误，请10分钟后再试']);
            }
        }

        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'activity_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Yxhd\Turnplate\Html5\IndexController@error', ['msg' => '缺少活动编码']);
        }
        //活动基本信息
        $activity = YxhdActivityManager::getById($data['activity_id']);
        if (!$activity) {
            return redirect()->action('\App\Http\Controllers\Yxhd\Turnplate\Html5\IndexController@error', ['msg' => '没有找到活动']);
        }
        //活动被冻结
        if ($activity->status == '0') {
            return redirect()->action('\App\Http\Controllers\Yxhd\Turnplate\Html5\IndexController@error', ['msg' => '该活动被冻结']);
        }
        $activity = YxhdActivityManager::getInfoByLevel($activity, '');
        //计算剩余次数
        $left_turnplate_num = (int)($user->score / $activity->join_score);
        //获取奖品列表
        $con_arr = array(
            'activity_id' => $activity->id
        );
        $prizeSettings = YxhdPrizeSettingManager::getListByCon($con_arr, false);
        //一定要达到10个奖品，如果奖品不足10个，则计划性的补充奖品
        $prizeName_arr = [];            //奖品名称数组
        $prizeId_arr = [];            //奖品id数组
        foreach ($prizeSettings as $prizeSetting) {
            $prize = YxhdPrizeManager::getById($prizeSetting->prize_id);
            //存在奖品，进行存入
            if ($prize) {
                array_push($prizeName_arr, $prize->name);
                array_push($prizeId_arr, $prize->id);
            }
        }
        //小于9个奖品，则进行奖品整理
        $ori_arr_count = count($prizeId_arr);
        if ($ori_arr_count < 9) {
            for ($i = 0; $i < (9 - $ori_arr_count); $i++) {
                //顺序补充数据，此处用到的思路是余数法，即如果初始数组为[1,2,3]，则ori_arr_count=3，需要补充6个数据，从Ri=0开始余原始数组长度，获得循环的数据
                array_push($prizeId_arr, $prizeId_arr[$i % $ori_arr_count]);
                array_push($prizeName_arr, $prizeName_arr[$i % $ori_arr_count]);
            }
        }
        //推入谢谢参与奖品
        array_push($prizeId_arr, -1);         //谢谢参与为id==-1的奖品
        array_push($prizeName_arr, '谢谢参与');

        $prizeConfig = array(
            'name_arr' => implode(",", $prizeName_arr),
            'id_arr' => implode(",", $prizeId_arr)
        );

        //我的中奖记录
        $con_arr = array(
            'user_id' => $user->id,
            'activity_id' => $activity->id,
            'pay_status' => '1',
            'winning_status' => '1'
        );
        $yxhdOrders = YxhdOrderManager::getListByCon($con_arr, false);
        foreach ($yxhdOrders as $yxhdOrder) {
            $yxhdOrder = YxhdOrderManager::getInfoByLevel($yxhdOrder, '2');
        }

        //增加统计数据
        YxhdActivityManager::addStatistics($activity->id, 'show_num', 1);

        //生成微信JS-SDK相关
        $wxConfig = null;
        if (!env('FWH_LOCAL_DEBUG')) {
            $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL[self::BUSI_NAME]);
            $wxConfig = $app->jssdk->buildConfig(array('onMenuShareTimeline', 'onMenuShareAppMessage'), false);
            Utils::processLog(__METHOD__, '', "wxConfig:" . json_encode($wxConfig));
        }


        //2018-12-24新增大转盘分享逻辑，分享页面为前端页面带入
        if (array_key_exists('from_page', $data) && !Utils::isObjNull($data['from_page'])) {
            Utils::processLog(__METHOD__, '', "配置分享信息，data:" . json_encode($data));
            $from_page = $data['from_page'];
            $params = array(
                'param1' => array_key_exists('param1', $data) ? $data['param1'] : '0',
            );
            $activity = self::getShareInfo($from_page, $params, $activity);
            Utils::processLog(__METHOD__, '', "activity:" . json_encode($activity));
        }

        return view('yxhd.turnplate.html5.index', ['user' => $user, 'activity' => $activity
            , 'yxhdOrders' => $yxhdOrders, 'prizeConfig' => $prizeConfig, 'left_turnplate_num' => $left_turnplate_num, 'wxConfig' => $wxConfig,]);
    }

    /*
     * 获取分享信息，该方法的主要作用是处理大转盘的分享信息
     *
     * By TerryQi
     *
     * 2018-12-24
     *
     * @from_page为传入的页面 params为参数数组，yxhdActivity是返回给前端的营销活动内容
     *
     */
    public function getShareInfo($from_page, $params, $yxhdActivity)
    {
        Utils::processLog(__METHOD__, '', "getShareInfo，from_page:" . json_encode($from_page) . " params:" . json_encode($params));
        //如果是从vote_person页面传入的
        if ($from_page == "vote_person") {
            $vote_user_id = $params['param1'];
            $voteUser = VoteUserManager::getById($vote_user_id);
            //如果没有获取选手信息
            if (!$voteUser) {
                return $yxhdActivity;
            }
            $voteUser = VoteUserManager::getInfoByLevel($voteUser, '');
            $voteActivity = VoteActivityManager::getById($voteUser->activity_id);
            //否则设置信息
            $yxhdActivity->share_title = '我是' . $voteUser->name . '，正在参加' . $voteActivity->name . '，点击查看我的作品'; // 分享标题
            $yxhdActivity->share_desc = $voteActivity->share_desc;
            $yxhdActivity->share_url = env('SYATC_CN_URL', "") . 'vote/person?vote_user_id=' . $voteUser->id;
            $yxhdActivity->share_img = $voteUser->img_arr[0];

            Utils::processLog(__METHOD__, '', "yxhdActivity:" . json_encode($yxhdActivity));

            return $yxhdActivity;
        }

        return $yxhdActivity;
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

        return view('yxhd.turnplate.html5.error.error', ['msg' => $msg]);
    }


    /*
     * 奖品页面
     *
     * By TerryQi
     *
     * 2018-12-20
     */
    public function prize(Request $request)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'prize_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Yxhd\Turnplate\Html5\IndexController@error', ['msg' => '缺少活动编码']);
        }
        $prize = YxhdPrizeManager::getById($data['prize_id']);

        return view('yxhd.turnplate.html5.prize', ['prize' => $prize]);
    }


}
