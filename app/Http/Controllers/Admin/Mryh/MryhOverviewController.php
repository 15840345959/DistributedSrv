<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\Mryh;

use App\Components\AdminManager;
use App\Components\DateTool;
use App\Components\LoginManager;
use App\Components\Mryh\MryhComputePrizeManager;
use App\Components\Mryh\MryhJoinManager;
use App\Components\Mryh\MryhJoinOrderManager;
use App\Components\QNManager;
use App\Components\Mryh\MryhADManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\Mryh\MryhAD;
use App\Models\Mryh\MryhJoin;
use App\Models\Mryh\MryhJoinArticle;
use App\Models\Mryh\MryhJoinOrder;
use App\Models\Mryh\MryhUser;
use App\Models\Mryh\MryhWithdrawCash;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class MryhOverviewController
{

    /*
     * 综合统计页面
     *
     * By TerryQi
     *
     * 2018-11-27
     *
     */
    public function index(Request $request)
    {
        //今日新增用户数
        $new_user_num = LoginManager::getListByCon(['busi_name' => Utils::BUSI_NAME_MRYH, 'date_at' => DateTool::getToday()], false)->count();
        //用户总数
        $total_user_num = LoginManager::getListByCon(['busi_name' => Utils::BUSI_NAME_MRYH], false)->count();
        //今日流水
        $today_trans_money = MryhJoinManager::getListByCon(['date_at' => DateTool::getToday()], false)->sum('total_fee');
        //平台总流水
        $total_trans_moeny = MryhJoinManager::getListByCon([], false)->sum('total_fee');
        //已提现金额
        $already_withdraw_money = MryhJoinManager::getListByCon(['jiesuan_status' => '1'], false)->sum('jiesuan_price');
        //待提现金额
        $waiting_withdraw_money = MryhJoinManager::getListByCon(['jiesuan_status' => '0'], false)->sum('jiesuan_price');

        //近3日清分任务数
        $compute_task_num = MryhComputePrizeManager::getListByCon(['created_start_at' => DateTool::dateAdd('D', -3, DateTool::getToday())], false)->count();

        return view('admin.mryh.mryhOverview.index', [
            'new_user_num' => $new_user_num,
            'total_user_num' => $total_user_num,
            'today_trans_money' => $today_trans_money,
            'total_trans_moeny' => $total_trans_moeny,
            'waiting_withdraw_money' => $waiting_withdraw_money,
            'already_withdraw_money' => $already_withdraw_money,
            'compute_task_num' => $compute_task_num
        ]);

    }

    //用户发展情况及参与数据情况
    /*
     *  By TerryQi
     *
     * 2018-11-27
     *
     * 要求request里面传入days_num，days_num代表了时间间隔
     *
     */
    public function user(Request $request)
    {
        $data = $request->all();
        $days_num = 7;     //默认是按周的活动
        $start_at = null;
        $end_at = Carbon::now()->addDay(1)->toDateString(); //获取到下一天，因为end_at时间为2018-11-28 00:00:00这种形式
        //如果存在days_num，代表传入了日期间隔
        if (array_key_exists('days_num', $data) && !Utils::isObjNull($data['days_num']) && is_numeric($data['days_num'])) {
            $days_num = intval($data['days_num']);
        }
        $start_at = Carbon::now()->subDay($days_num)->toDateString();
        //每天一画新增用户数据
        $mryhUsers = MryhUser::whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)
            ->groupBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as value')
            ])
            ->toArray();
        $mryhUsers = Utils::replZero($mryhUsers, $start_at, $end_at);

        $mryhUsers_total_num = MryhUser::whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)->count();

        $ret = [
            'user_arr' => $mryhUsers,
            'user_total_num' => $mryhUsers_total_num
        ];

        return ApiResponse::makeResponse(true, $ret, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 参赛趋势和作品趋势
     *
     * By TerryQi
     *
     * 2018-11-28
     *
     *  要求request里面传入days_num，days_num代表了时间间隔
     */
    public function join_article(Request $request)
    {
        $data = $request->all();
        $days_num = 7;     //默认是按周的活动
        $start_at = null;
        $end_at = Carbon::now()->addDay(1)->toDateString(); //获取到下一天，因为end_at时间为2018-11-28 00:00:00这种形式
        //如果存在days_num，代表传入了日期间隔
        if (array_key_exists('days_num', $data) && !Utils::isObjNull($data['days_num']) && is_numeric($data['days_num'])) {
            $days_num = intval($data['days_num']);
        }
        $start_at = Carbon::now()->subDay($days_num)->toDateString();

        //参赛趋势
        $mryhJoins = MryhJoin::whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)
            ->groupBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as value')
            ])
            ->toArray();
        $mryhJoins = Utils::replZero($mryhJoins, $start_at, $end_at);

        $mryhJoins_total_num = MryhUser::whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)->count();

        //作品趋势
        $mryhJoinArticles = MryhJoinArticle::whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)
            ->groupBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as value')
            ])
            ->toArray();
        $mryhJoinArticles = Utils::replZero($mryhJoinArticles, $start_at, $end_at);

        $mryhJoinArticles_total_num = MryhJoinArticle::whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)->count();

        $ret = [
            'join_arr' => $mryhJoins,
            'join_total_num' => $mryhJoins_total_num,
            'article_arr' => $mryhJoinArticles,
            'article_total_num' => $mryhJoinArticles_total_num,
        ];

        return ApiResponse::makeResponse(true, $ret, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 提现趋势图
     *
     * By TerryQi
     *
     * 2018-11-28
     *
     *  要求request里面传入days_num，days_num代表了时间间隔
     *
     */
    public function withdraw_failed(Request $request)
    {
        $data = $request->all();
        $days_num = 7;     //默认是按周的活动
        $start_at = null;
        $end_at = Carbon::now()->addDay(1)->toDateString(); //获取到下一天，因为end_at时间为2018-11-28 00:00:00这种形式
        //如果存在days_num，代表传入了日期间隔
        if (array_key_exists('days_num', $data) && !Utils::isObjNull($data['days_num']) && is_numeric($data['days_num'])) {
            $days_num = intval($data['days_num']);
        }
        $start_at = Carbon::now()->subDay($days_num)->toDateString();
        //每日提现金额
        $mryhWithdrawCashs = MryhWithdrawCash::whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)
            ->where('withdraw_status', '=', '1')
            ->groupBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as value')
            ])
            ->toArray();
        $mryhWithdrawCashs = Utils::replZero($mryhWithdrawCashs, $start_at, $end_at);

        $mryhWithdrawCashs_total_num = MryhWithdrawCash::whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)->where('withdraw_status', '=', '1')->count();

        //每日提现失败数
        $mryhWithdrawFaileds = MryhWithdrawCash::whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)
            ->where('withdraw_status', '=', '2')
            ->groupBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as value')
            ])
            ->toArray();

        $mryhWithdrawFaileds = Utils::replZero($mryhWithdrawFaileds, $start_at, $end_at);

        $mryhWithdrawFaileds_total_num = MryhWithdrawCash::whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)->where('withdraw_status', '=', '2')->count();

        $ret = [
            'withdrawCash_arr' => $mryhWithdrawCashs,
            'withdrawCash_total_num' => $mryhWithdrawCashs_total_num,
            'withdrawFailed_arr' => $mryhWithdrawFaileds,
            'withdrawFailed_total_num' => $mryhWithdrawFaileds_total_num
        ];

        return ApiResponse::makeResponse(true, $ret, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 业务订单趋势-新增+退款
     *
     * By TerryQi
     *
     * 2018-11-28
     *
     *  要求request里面传入days_num，days_num代表了时间间隔
     *
     */
    public function new_refund_joinOrder(Request $request)
    {
        $data = $request->all();
        $days_num = 7;     //默认是按周的活动
        $start_at = null;
        $end_at = Carbon::now()->addDay(1)->toDateString(); //获取到下一天，因为end_at时间为2018-11-28 00:00:00这种形式
        //如果存在days_num，代表传入了日期间隔
        if (array_key_exists('days_num', $data) && !Utils::isObjNull($data['days_num']) && is_numeric($data['days_num'])) {
            $days_num = intval($data['days_num']);
        }
        $start_at = Carbon::now()->subDay($days_num)->toDateString();

        //每日新增订单金额
        $new_mryhJoinOrders = MryhJoinOrder::whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)
            ->where('pay_status', '=', '1')
            ->groupBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_fee) as value')
            ])
            ->toArray();

        $new_mryhJoinOrders = Utils::replZero($new_mryhJoinOrders, $start_at, $end_at);

        $new_mryhJoinOrders_total_num = MryhJoinOrder::whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)->count();

        //每日达标退款金额
        $refund_mryhJoinOrders = MryhJoinOrder::whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)
            ->where('pay_status', '=', '4')
            ->groupBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_fee) as value')
            ])
            ->toArray();

        $refund_mryhJoinOrders = Utils::replZero($refund_mryhJoinOrders, $start_at, $end_at);

        $refund_mryhJoinOrders_total_num = MryhJoinOrder::whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)->where('pay_status', '=', '4')->count();

        $ret = [
            'new_joinOrders_arr' => $new_mryhJoinOrders,
            'new_joinOrders_total_num' => $new_mryhJoinOrders_total_num,
            'refund_joinOrders_arr' => $refund_mryhJoinOrders,
            'refund_joinOrders_total_num' => $refund_mryhJoinOrders_total_num,
        ];

        return ApiResponse::makeResponse(true, $ret, ApiResponse::SUCCESS_CODE);

    }

}