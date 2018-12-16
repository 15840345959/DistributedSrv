<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Vote\Team;

use App\Components\AdminManager;
use App\Components\DateTool;
use App\Components\OptInfoManager;
use App\Components\OptRecordManager;
use App\Components\QNManager;
use App\Components\RequestValidator;
use App\Components\Utils;
use App\Components\Vote\VoteActivityManager;
use App\Components\Vote\VoteGuanZhuManager;
use App\Components\Vote\VoteRecordManager;
use App\Components\Vote\VoteRuleManager;
use App\Components\Vote\VoteShareRecordManager;
use App\Components\Vote\VoteTeamManager;
use App\Components\Vote\VoteUserManager;
use App\Http\Controllers\ApiResponse;
use App\Models\OptInfo;
use App\Models\OptRecord;
use App\Models\Vote\VoteActivity;
use App\Models\Vote\VoteUser;
use function Couchbase\defaultDecoder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class VoteUserController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
        $team = $request->session()->get('team');
        $search_word = null;
        $activity_id = null;
        $vote_user_id = null;
        $audit_status = null;

        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }
        if (array_key_exists('activity_id', $data) && !Utils::isObjNull($data['activity_id'])) {
            $activity_id = $data['activity_id'];
        }
        if (array_key_exists('vote_user_id', $data) && !Utils::isObjNull($data['vote_user_id'])) {
            $vote_user_id = $data['vote_user_id'];
        }
        if (array_key_exists('audit_status', $data) && !Utils::isObjNull($data['audit_status'])) {
            $audit_status = $data['audit_status'];
        }

        $activity_id_array = VoteActivityManager::getListByCon([
            'vote_team_id' => $team->id,
        ], false)->pluck('id');

        $con_arr = array(
            'search_word' => $search_word,
            'activity_id' => $activity_id,
            'where_in_activity_id' => $activity_id_array,
            'vote_user_id' => $vote_user_id,
            'audit_status' => $audit_status
        );

        $voteUsers = VoteUserManager::getListByCon($con_arr, true);

        foreach ($voteUsers as $voteUser) {
            $voteUser = VoteUserManager::getInfoByLevel($voteUser, '01');
        }

//        dd($voteUsers);

        return view('vote.team.voteUser.index', ['datas' => $voteUsers, 'con_arr' => $con_arr]);
    }
}