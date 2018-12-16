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
use App\Components\B\BMessageManager;
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


class MessageController
{
    // 首页
    public function index(Request $request)
    {
        $data = $request->all();
        $team = $request->session()->get('team');

        $search_word = null;
        $busi_name = null;
        $level = null;

        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }

        if (array_key_exists('busi_name', $data) && !Utils::isObjNull($data['busi_name'])) {
            $busi_name = array_get($data, 'busi_name');
        }

        if (array_key_exists('level', $data) && !Utils::isObjNull($data['level'])) {
            $level = array_get($data, 'level');
        }

        $con_arr = array(
            'search_word' => $search_word,
            'busi_name' => $busi_name,
            'level' => $level,
            'status' => 1
        );

        $messages = BMessageManager::getListByCon($con_arr, true);

        foreach ($messages as $message) {
            unset($message->content_html);
            $message = BMessageManager::getInfoByLevel($message, '0');
        }

        return view('vote.team.message.index', ['datas' => $messages, 'con_arr' => $con_arr]);
    }

    // 信息
    public function info(Request $request)
    {
        $team = $request->session()->get('team');

        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->route('team.error.500', ['msg' => '合规校验失败，请检查参数' . $requestValidationResult]);
        }

        $data = $request->all();

        $message = BMessageManager::getById(array_get($data, 'id'));


        $admin =  AdminManager::getById($message->admin_id);
        return view('vote.team.message.info', ['data' => $message, 'admin' => $admin]);
    }
}