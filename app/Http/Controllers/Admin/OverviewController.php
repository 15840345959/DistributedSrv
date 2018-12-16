<?php
/**
 * Created by PhpStorm.
 * User: leek
 * Date: 2018/2/4
 * Time: 下午2:42
 */

namespace App\Http\Controllers\Admin;

use App\Components\UserManager;
use App\Components\Vote\VoteActivityManager;
use App\Components\Vote\VoteOrderManager;
use App\Components\Vote\VoteUserManager;
use App\Http\Controllers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Vote\VoteActivity;
use App\Models\Vote\VoteOrder;
use App\Models\Vote\VoteRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\DocBlock;

class OverviewController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->session()->get('user');

        // 今日收入
        $today_order_info = VoteOrder::whereDate('pay_at', Carbon::now()->toDateString())->get()->sum('total_fee');

        // 今日新增场次
        // TODO 此处只用了 activity_start_time 不知道对不对
        $today_activity_info = VoteActivity::whereDate('created_at', Carbon::now()->toDateString())->get();

        // 等待审核人数
        // TODO 此处只用了 audit_status 不知道对不对
        $wait_audit = VoteUserManager::getListByCon(['audit_status' => 0], false);

        // 场次状态
        $activity = VoteActivity::withCount('vote_user')->where('status', '1')->wherein('vote_status', ['0', '1'])->get();

//        dd($activity);

        return view('admin.overview.index', [
            'today_total_fee' => $today_order_info,
            'today_activity_count' => $today_activity_info->count(),
            'wait_audit' => $wait_audit->count(),
            'activity' => $activity,
        ]);
    }

    // 场次状态
    public function activityStatus(Request $request)
    {
        // 待上传
        $activity_wait_upload_count = VoteActivity::where('status', 1)->has('vote_user', '<', 30)->get()->count();
        // 待激活
        $activity_wait_activate_count = VoteActivity::where('valid_status', '0')->where('status', 1)->get()->count();
        // 已结束
        $activity_end_count = VoteActivity::where('vote_status', '2')->where('status', 1)->get()->count();
        // 正常
        $activity_normal_count = VoteActivity::where('valid_status', '1')->where('status', 1)->get()->count();

        $activity_status = [
            collect(['name' => '待上传', 'value' => $activity_wait_upload_count]),
            collect(['name' => '待激活', 'value' => $activity_wait_activate_count]),
            collect(['name' => '已结束', 'value' => $activity_end_count]),
            collect(['name' => '正常', 'value' => $activity_normal_count]),
        ];

        return ApiResponse::makeResponse(true, $activity_status, 200);
    }

    // 收入
    public function income(Request $request)
    {
        $data = $request->all();

        // 开始时间
        $start_at = null;
        // 结束时间为今天
        $end_at = Carbon::now()->toDateString();

        // 根据传来type推算开始日期
        switch (array_get($data, 'type')) {
            case 'week':
                $start_at = Carbon::now()->subDay(6)->toDateString();
                break;
            case 'half_month':
                $start_at = Carbon::now()->subDay(13)->toDateString();
                break;
            case 'month':
                $start_at = Carbon::now()->subDay(29)->toDateString();
                break;
        }

        // 根据日期分组查询当日支付成功金额
        $income = VoteOrder::whereDate('pay_at', '>=', $start_at)
            ->whereDate('pay_at', '<=', $end_at)
            ->groupBy('date')
            ->get([
                DB::raw('DATE(pay_at) as date'),
                DB::raw('SUM(total_fee) as count')
            ])
            ->toArray();

        // 计算时间范围内总收入
        $total = VoteOrder::whereDate('pay_at', '>=', $start_at)
            ->whereDate('pay_at', '<=', $end_at)
            ->get()
            ->sum('total_fee');

        // 因为下面要进行日期补全操作 $income 中需要开始日期和结束日期两天数据
        // so 现在需要根据分组情况分别判断

        // 分组总数为0 代表一条数据也没有 手动添加开始日期和结束日期
        if (count($income) === 0) {
            array_unshift($income, [
                'date' => $start_at,
                'count' => 0
            ]);

            array_push($income, [
                'date' => $end_at,
                'count' => 0
            ]);
        }

        // 分组总数为1 代表只有一条数据
        if (count($income) === 1) {
            // 根据数据的日期判断
            switch ($income[0]['date']) {
                // 如果是开始日期就手动添加一个结束日期
                case $start_at:
                    array_push($income, [
                        'date' => $end_at,
                        'count' => 0
                    ]);
                    break;
                // 如果是结束日期就手动添加一个结束日期
                case $end_at:
                    array_unshift($income, [
                        'date' => $start_at,
                        'count' => 0
                    ]);
                    break;
                // 都不是的话就手动添加一个开始日期一个结束日期
                default:
                    array_push($income, [
                        'date' => $end_at,
                        'count' => 0
                    ]);
                    array_unshift($income, [
                        'date' => $start_at,
                        'count' => 0
                    ]);
                    break;
            }
        }

        if ($income[0]['date'] !== $start_at) {
            array_unshift($income, [
                'date' => $start_at,
                'count' => 0
            ]);
        }

        if ($income[count($income) - 1]['date'] !== $end_at) {
            array_push($income, [
                'date' => $end_at,
                'count' => 0
            ]);
        }

        $new_income = [];

        $one_day_second = 86400;

        for ($i = 0; $i < count($income); $i++) {
            if ($i == count($income) - 1) {
                array_push($new_income, $income[$i]);
            } else {
                $curr_time = Carbon::parse($income[$i]['date']);
                $next_time = Carbon::parse($income[$i + 1]['date']);

                $curr_timestamp = $curr_time->timestamp;
                $next_timestamp = $next_time->timestamp;

                array_push($new_income, $income[$i]);

                $diff = $next_timestamp - $curr_timestamp;

                if ($diff > $one_day_second) {
                    for ($m = $curr_timestamp + $one_day_second; $m < $next_timestamp; $m += $one_day_second) {
                        array_push($new_income, [
                            'date' => Carbon::createFromTimestamp($m)->toDateString(),
                            'count' => 0
                        ]);
                    }
                }
            }
        }


        $vote = self::vote($data);

        return ApiResponse::makeResponse(true, ['income' => $new_income, 'income_total' => $total, 'vote' => $vote['vote'], 'vote_total' => $vote['total']], 200);
    }

    public function is_in_date_array($array, $date)
    {
        for ($i = 0; $i < count($array); $i++) {
            if ($array[$i]['date'] === $date) {
                return true;
            }
        }
        return false;
    }


    // 新增场次
    public function newActivity(Request $request)
    {
        $data = $request->all();

        $start_at = null;
        $end_at = Carbon::now()->toDateString();

        switch (array_get($data, 'type')) {
            case 'week':
                $start_at = Carbon::now()->subDay(6)->toDateString();
                break;
            case 'half_month':
                $start_at = Carbon::now()->subDay(13)->toDateString();
                break;
            case 'month':
                $start_at = Carbon::now()->subDay(29)->toDateString();
                break;
        }

        $activity = VoteActivity::whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)
            ->groupBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            ])
            ->toArray();

        $total = VoteActivity::whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)
            ->get()
            ->count();

        if (count($activity) === 0) {
            array_unshift($activity, [
                'date' => $start_at,
                'count' => 0
            ]);

            array_push($activity, [
                'date' => $end_at,
                'count' => 0
            ]);
        }

        if (count($activity) === 1) {
            switch ($activity[0]['date']) {
                case $start_at:
                    array_push($activity, [
                        'date' => $end_at,
                        'count' => 0
                    ]);
                    break;
                case $end_at:
                    array_unshift($activity, [
                        'date' => $start_at,
                        'count' => 0
                    ]);
                    break;
                default:
                    array_push($activity, [
                        'date' => $end_at,
                        'count' => 0
                    ]);
                    array_unshift($activity, [
                        'date' => $start_at,
                        'count' => 0
                    ]);
                    break;
            }
        }

        if ($activity[0]['date'] !== $start_at) {
            array_unshift($activity, [
                'date' => $start_at,
                'count' => 0
            ]);
        }

        if ($activity[count($activity) - 1]['date'] !== $end_at) {
            array_push($activity, [
                'date' => $end_at,
                'count' => 0
            ]);
        }

        $new_activity = [];

        $one_day_second = 86400;

        for ($i = 0; $i < count($activity); $i++) {
            if ($i == count($activity) - 1) {
                array_push($new_activity, $activity[$i]);
            } else {
                $curr_time = Carbon::parse($activity[$i]['date']);
                $next_time = Carbon::parse($activity[$i + 1]['date']);

                $curr_timestamp = $curr_time->timestamp;
                $next_timestamp = $next_time->timestamp;

                array_push($new_activity, $activity[$i]);

                $diff = $next_timestamp - $curr_timestamp;

                if ($diff > $one_day_second) {
                    for ($m = $curr_timestamp + $one_day_second; $m < $next_timestamp; $m += $one_day_second) {
                        array_push($new_activity, [
                            'date' => Carbon::createFromTimestamp($m)->toDateString(),
                            'count' => 0
                        ]);
                    }
                }
            }
        }

        return ApiResponse::makeResponse(true, ['activity' => $new_activity, 'total' => $total], 200);
    }

    // 订单数
    public function order(Request $request)
    {
        $data = $request->all();

        $start_at = null;
        $end_at = Carbon::now()->toDateString();

        switch (array_get($data, 'type')) {
            case 'week':
                $start_at = Carbon::now()->subDay(6)->toDateString();
                break;
            case 'half_month':
                $start_at = Carbon::now()->subDay(13)->toDateString();
                break;
            case 'month':
                $start_at = Carbon::now()->subDay(29)->toDateString();
                break;
        }

        $order = VoteOrder::select([
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as all_order'),
            DB::raw('count(CASE WHEN pay_at is not null THEN 0 END) AS pay_order')
        ])
            ->whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)
            ->groupBy('date')
            ->get([
                'date',
                'all_order'
            ])
            ->toArray();

        $total = VoteOrder::select([
            DB::raw('count(*) as all_count'),
            DB::raw('count(CASE WHEN pay_at is not null THEN 0 END) AS pay_count')
        ])
            ->whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)
            ->get();

        if (count($order) === 0) {
            array_unshift($order, [
                'date' => $start_at,
                'all_order' => 0,
                'pay_order' => 0
            ]);

            array_push($order, [
                'date' => $end_at,
                'all_order' => 0,
                'pay_order' => 0
            ]);
        }

        if (count($order) === 1) {
            switch ($order[0]['date']) {
                case $start_at:
                    array_push($order, [
                        'date' => $end_at,
                        'all_order' => 0,
                        'pay_order' => 0
                    ]);
                    break;
                case $end_at:
                    array_unshift($order, [
                        'date' => $start_at,
                        'all_order' => 0,
                        'pay_order' => 0
                    ]);
                    break;
                default:
                    array_push($order, [
                        'date' => $end_at,
                        'all_order' => 0,
                        'pay_order' => 0
                    ]);
                    array_unshift($order, [
                        'date' => $start_at,
                        'all_order' => 0,
                        'pay_order' => 0
                    ]);
                    break;
            }
        }

        if ($order[0]['date'] !== $start_at) {
            array_unshift($order, [
                'date' => $start_at,
                'all_order' => 0,
                'pay_order' => 0
            ]);
        }

        if ($order[count($order) - 1]['date'] !== $end_at) {
            array_push($order, [
                'date' => $end_at,
                'all_order' => 0,
                'pay_order' => 0
            ]);
        }

        $new_order = [];

        $one_day_second = 86400;

        for ($i = 0; $i < count($order); $i++) {
            if ($i == count($order) - 1) {
                array_push($new_order, $order[$i]);
            } else {
                $curr_time = Carbon::parse($order[$i]['date']);
                $next_time = Carbon::parse($order[$i + 1]['date']);

                $curr_timestamp = $curr_time->timestamp;
                $next_timestamp = $next_time->timestamp;

                array_push($new_order, $order[$i]);

                $diff = $next_timestamp - $curr_timestamp;

                if ($diff > $one_day_second) {
                    for ($m = $curr_timestamp + $one_day_second; $m < $next_timestamp; $m += $one_day_second) {
                        array_push($new_order, [
                            'date' => Carbon::createFromTimestamp($m)->toDateString(),
                            'count' => 0
                        ]);
                    }
                }
            }
        }

        return ApiResponse::makeResponse(true, ['order' => $new_order, 'total' => $total], 200);
    }

    // 投票数
    public static function vote($data)
    {
//        $data = $request->all();

        $start_at = null;
        $end_at = Carbon::now()->toDateString();

        switch (array_get($data, 'type')) {
            case 'week':
                $start_at = Carbon::now()->subDay(6)->toDateString();
                break;
            case 'half_month':
                $start_at = Carbon::now()->subDay(13)->toDateString();
                break;
            case 'month':
                $start_at = Carbon::now()->subDay(29)->toDateString();
                break;
        }

//        $start_at = '2018-10-20';

        $vote = VoteRecord::select([
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(vote_num) as count')
        ])
            ->whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)
            ->groupBy('date')
            ->get([
                'date',
                'count'
            ])
            ->toArray();

        $total = VoteRecord::select([
            DB::raw('SUM(vote_num) as vote_num'),
        ])
            ->whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)
            ->get();
//            ->sum('vote_num');

//        dd($total->first()->vote_num);

        if (count($vote) === 0) {
            array_unshift($vote, [
                'date' => $start_at,
                'all_order' => 0,
                'pay_order' => 0
            ]);

            array_push($vote, [
                'date' => $end_at,
                'all_order' => 0,
                'pay_order' => 0
            ]);
        }

        if (count($vote) === 1) {
            switch ($vote[0]['date']) {
                case $start_at:
                    array_push($vote, [
                        'date' => $end_at,
                        'all_order' => 0,
                        'pay_order' => 0
                    ]);
                    break;
                case $end_at:
                    array_unshift($vote, [
                        'date' => $start_at,
                        'all_order' => 0,
                        'pay_order' => 0
                    ]);
                    break;
                default:
                    array_push($vote, [
                        'date' => $end_at,
                        'all_order' => 0,
                        'pay_order' => 0
                    ]);
                    array_unshift($vote, [
                        'date' => $start_at,
                        'all_order' => 0,
                        'pay_order' => 0
                    ]);
                    break;
            }
        }

        if ($vote[0]['date'] !== $start_at) {
            array_unshift($vote, [
                'date' => $start_at,
                'all_order' => 0,
                'pay_order' => 0
            ]);
        }

        if ($vote[count($vote) - 1]['date'] !== $end_at) {
            array_push($vote, [
                'date' => $end_at,
                'all_order' => 0,
                'pay_order' => 0
            ]);
        }

        $new_vote = [];

        $one_day_second = 86400;

        for ($i = 0; $i < count($vote); $i++) {
            if ($i == count($vote) - 1) {
                array_push($new_vote, $vote[$i]);
            } else {
                $curr_time = Carbon::parse($vote[$i]['date']);
                $next_time = Carbon::parse($vote[$i + 1]['date']);

                $curr_timestamp = $curr_time->timestamp;
                $next_timestamp = $next_time->timestamp;

                array_push($new_vote, $vote[$i]);

                $diff = $next_timestamp - $curr_timestamp;

                if ($diff > $one_day_second) {
                    for ($m = $curr_timestamp + $one_day_second; $m < $next_timestamp; $m += $one_day_second) {
                        array_push($new_vote, [
                            'date' => Carbon::createFromTimestamp($m)->toDateString(),
                            'count' => 0
                        ]);
                    }
                }
            }
        }

//        return ApiResponse::makeResponse(true, ['vote' => $new_vote, 'total' => $total->first()->vote_num], 200);

        return ['vote' => $new_vote, 'total' => $total->first()->vote_num];
    }
}