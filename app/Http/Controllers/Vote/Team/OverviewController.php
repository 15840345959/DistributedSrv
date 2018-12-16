<?php
/**
 * Created by PhpStorm.
 * User: leek
 * Date: 2018/2/4
 * Time: 下午2:42
 */

namespace App\Http\Controllers\Vote\Team;

use App\Components\DateTool;
use App\Components\RequestValidator;
use App\Components\UserManager;
use App\Components\Utils;
use App\Components\Vote\VoteActivityManager;
use App\Components\Vote\VoteOrderManager;
use App\Components\Vote\VoteUserManager;
use App\Http\Controllers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Vote\VoteActivity;
use App\Models\Vote\VoteOrder;
use App\Models\Vote\VoteRecord;
use App\Models\Vote\VoteUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\DocBlock;

class OverviewController extends Controller
{

    /*
     * 业务概览信息
     *
     * By TerryQi
     *
     * 2018-12-07
     *
     */
    public function index(Request $request)
    {
        $team = $request->session()->get('team');

        //获取team_id信息
        $vote_team_id = $team->id;
        $date_at = DateTool::getToday();

        //归属团队的活动数组
        $voteActivity_id_arr = VoteActivityManager::getListByCon(['vote_team_id' => $vote_team_id], false)->pluck('id');

        //今日收入
        $today_total_fee = VoteOrder::wherein('activity_id', $voteActivity_id_arr)->where('pay_status', '=', '1')
            ->whereDate('created_at', $date_at)->sum('total_fee');
        //待审核选手数
        $authstr_num = VoteUser::wherein('activity_id', $voteActivity_id_arr)->where('audit_status', '=', '0')->count();
        //今日新增活动数
        $today_new_activity_num = VoteActivity::where('vote_team_id', '=', $vote_team_id)->whereDate('created_at', $date_at)->count();
        //今日结束活动数
        $today_end_activity_num = VoteActivity::where('vote_team_id', '=', $vote_team_id)->whereDate('vote_end_time', $date_at)->count();

        $info = [
            'today_total_fee' => $today_total_fee,
            'authstr_num' => $authstr_num,
            'today_new_activity_num' => $today_new_activity_num,
            'today_end_activity_num' => $today_end_activity_num
        ];

        return view('vote.team.overview.index', $info);
    }


    /*
     * 订单统计数据-获取成功/失败订单统计
     *
     * By TerryQi
     *
     * 2018-12-07
     *
     */
    public function order(Request $request)
    {
        $data = $request->all();
        $team = $request->session()->get('team');
        //归属团队的活动数组
        $vote_team_id = $team->id;
        $voteActivity_id_arr = VoteActivityManager::getListByCon(['vote_team_id' => $vote_team_id], false)->pluck('id');

        $days_num = 7;     //默认是按周的活动
        $start_at = null;
        $end_at = Carbon::now()->addDay(1)->toDateString(); //获取到下一天，因为end_at时间为2018-11-28 00:00:00这种形式
        //如果存在days_num，代表传入了日期间隔
        if (array_key_exists('days_num', $data) && !Utils::isObjNull($data['days_num']) && is_numeric($data['days_num'])) {
            $days_num = intval($data['days_num']);
        }
        $start_at = Carbon::now()->subDay($days_num)->toDateString();

        //总订单数统计
        $order_total_num = VoteOrder::wherein('activity_id', $voteActivity_id_arr)->whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)->count();

        $voteOrders = VoteOrder::wherein('activity_id', $voteActivity_id_arr)
            ->whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)
            ->groupBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as value')
            ])
            ->toArray();

        $voteOrders = Utils::replZero($voteOrders, $start_at, $end_at);

        //支付成功订单数
        $pay_order_total_num = VoteOrder::wherein('activity_id', $voteActivity_id_arr)->where('pay_status', '=', '1')
            ->whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)->count();

        $pay_voteOrders = VoteOrder::wherein('activity_id', $voteActivity_id_arr)->where('pay_status', '=', '1')
            ->whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)
            ->groupBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as value')
            ])
            ->toArray();

        $pay_voteOrders = Utils::replZero($pay_voteOrders, $start_at, $end_at);

        $ret = [
            'order_arr' => $voteOrders,
            'order_total_num' => $order_total_num,
            'pay_order_arr' => $pay_voteOrders,
            'pay_order_total_num' => $pay_order_total_num,
        ];

        return ApiResponse::makeResponse(true, $ret, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 投票与收入数
     *
     * By TerryQi
     *
     * 2018-12-07
     */
    public function vote_money(Request $request)
    {
        $data = $request->all();
        $team = $request->session()->get('team');
        //归属团队的活动数组
        $vote_team_id = $team->id;
        $voteActivity_id_arr = VoteActivityManager::getListByCon(['vote_team_id' => $vote_team_id], false)->pluck('id');

        $days_num = 7;     //默认是按周的活动
        $start_at = null;
        $end_at = Carbon::now()->addDay(1)->toDateString(); //获取到下一天，因为end_at时间为2018-11-28 00:00:00这种形式
        //如果存在days_num，代表传入了日期间隔
        if (array_key_exists('days_num', $data) && !Utils::isObjNull($data['days_num']) && is_numeric($data['days_num'])) {
            $days_num = intval($data['days_num']);
        }
        $start_at = Carbon::now()->subDay($days_num)->toDateString();

        //总投票数
        $vote_total_num = VoteRecord::wherein('activity_id', $voteActivity_id_arr)
            ->whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)->count();

        $voteRecords = VoteRecord::wherein('activity_id', $voteActivity_id_arr)
            ->whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)
            ->groupBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as value')
            ]);

        $voteRecords = Utils::replZero($voteRecords, $start_at, $end_at);

        //总订单金额
        $order_total_money = VoteOrder::wherein('activity_id', $voteActivity_id_arr)->where('pay_status', '=', '1')
            ->whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)->sum('total_fee');

        $pay_voteOrders = VoteOrder::wherein('activity_id', $voteActivity_id_arr)->where('pay_status', '=', '1')
            ->whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)
            ->groupBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('sum(total_fee) as value')
            ])
            ->toArray();

        $pay_voteOrders = Utils::replZero($pay_voteOrders, $start_at, $end_at);

        $ret = [
            'vote_arr' => $voteRecords,
            'vote_total_num' => $vote_total_num,
            'order_arr' => $pay_voteOrders,
            'order_total_money' => $order_total_money,
        ];

        return ApiResponse::makeResponse(true, $ret, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 场次变化趋势
     *
     * By TerryQi
     *
     * 2018-12-7
     */
    public function activity_trend(Request $request)
    {
        $data = $request->all();
        $team = $request->session()->get('team');
        //归属团队的活动数组
        $vote_team_id = $team->id;
        $voteActivity_id_arr = VoteActivityManager::getListByCon(['vote_team_id' => $vote_team_id], false)->pluck('id');

        $days_num = 7;     //默认是按周的活动
        $start_at = null;
        $end_at = Carbon::now()->addDay(1)->toDateString(); //获取到下一天，因为end_at时间为2018-11-28 00:00:00这种形式
        //如果存在days_num，代表传入了日期间隔
        if (array_key_exists('days_num', $data) && !Utils::isObjNull($data['days_num']) && is_numeric($data['days_num'])) {
            $days_num = intval($data['days_num']);
        }
        $start_at = Carbon::now()->subDay($days_num)->toDateString();

        //新增场次信息
        $new_activity_num = VoteActivity::where('vote_team_id', '=', $team->id)
            ->whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)->count();

        $new_activities = VoteActivity::where('vote_team_id', '=', $team->id)
            ->whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)
            ->groupBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as value')
            ]);

        $new_activities = Utils::replZero($new_activities, $start_at, $end_at);

        //结束场次信息
        $end_activity_num = VoteActivity::where('vote_team_id', '=', $team->id)
            ->whereDate('vote_end_time', '>=', $start_at)
            ->whereDate('vote_end_time', '<=', $end_at)->count();

        $end_activities = VoteActivity::where('vote_team_id', '=', $team->id)
            ->whereDate('vote_end_time', '>=', $start_at)
            ->whereDate('vote_end_time', '<=', $end_at)
            ->groupBy('date')
            ->get([
                DB::raw('DATE(vote_end_time) as date'),
                DB::raw('count(*) as value')
            ]);

        $end_activities = Utils::replZero($end_activities, $start_at, $end_at);

        $ret = [
            'new_activity_num' => $new_activity_num,
            'new_activity_arr' => $new_activities,
            'end_activity_num' => $end_activity_num,
            'end_activity_arr' => $end_activities,
        ];

        return ApiResponse::makeResponse(true, $ret, ApiResponse::SUCCESS_CODE);

    }

}