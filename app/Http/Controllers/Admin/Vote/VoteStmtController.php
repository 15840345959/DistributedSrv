<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\Vote;

use App\Components\AdminManager;
use App\Components\DateTool;
use App\Components\OptInfoManager;
use App\Components\OptRecordManager;
use App\Components\QNManager;
use App\Components\RequestValidator;
use App\Components\Utils;
use App\Components\Vote\VoteActivityManager;
use App\Components\Vote\VoteRuleManager;
use App\Components\Vote\VoteTeamManager;
use App\Http\Controllers\ApiResponse;
use App\Models\OptInfo;
use App\Models\OptRecord;
use App\Models\Vote\VoteActivity;
use App\Models\Vote\VoteOrder;
use App\Models\Vote\VoteUser;
use function Couchbase\defaultDecoder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class VoteStmtController
{
    //管理报表
    /*
     * 日管理报表
     *
     *
     */
    public function daily(Request $request)
    {
        $data = $request->all();
        //配置数据
        $date_at = DateTool::getToday();
        if (array_key_exists('date_at', $data) && !Utils::isObjNull($data['date_at'])) {
            $date_at = $data['date_at'];
        }
        //配置条件
        $con_arr = array(
            'date_at' => $date_at
        );

        $voteActivities = VoteActivityManager::getListByCon(['vote_end_at' => $date_at], true);
        foreach ($voteActivities as $voteActivity) {
            $voteActivity = VoteActivityManager::getInfoByLevel($voteActivity, '2');
        }

        //地推团队当天的收益列表
        $voteTeams = VoteTeamManager::getListByCon([], false);
        foreach ($voteTeams as $voteTeam) {
            $voteTeam = VoteTeamManager::getInfoByLevel($voteTeam, '');
            //获取全部的地推活动列表
            $voteActivity_id_arr = VoteActivity::where('vote_team_id', '=', $voteTeam->id)->pluck('id');
            //获得当天所有的收益
            $voteTeam->daily_total_money = VoteOrder::where('pay_status', '=', '1')
                ->wherein('activity_id', $voteActivity_id_arr)->whereDate('created_at', $date_at)->sum('total_fee');
        }

        //当日归属地推团队总金额
        $team_voteActivity_id_arr = VoteActivity::whereNotNull('vote_team_id')->pluck('id');
        $daily_team_total_money = VoteOrder::where('pay_status', '=', '1')->wherein('activity_id', $team_voteActivity_id_arr)
            ->whereDate('created_at', $date_at)->sum('total_fee');

        //当日总金额
        $daily_total_money = VoteOrder::where('pay_status', '=', '1')
            ->whereDate('created_at', $date_at)->sum('total_fee');

        return view('admin.vote.voteStmt.daily', ['voteActivities' => $voteActivities,
            'con_arr' => $con_arr, 'voteTeams' => $voteTeams, 'daily_total_money' => $daily_total_money, 'daily_team_total_money' => $daily_team_total_money]);
    }


}