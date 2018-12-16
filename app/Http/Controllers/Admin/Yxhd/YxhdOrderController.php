<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\Yxhd;

use App\Components\AdminManager;
use App\Components\DateTool;
use App\Components\OptInfoManager;
use App\Components\OptRecordManager;
use App\Components\QNManager;
use App\Components\RequestValidator;
use App\Components\Utils;
use App\Components\Yxhd\YxhdOrderManager;
use App\Components\Yxhd\YxhdRuleManager;
use App\Components\Yxhd\YxhdTeamManager;
use App\Http\Controllers\ApiResponse;
use App\Models\OptInfo;
use App\Models\OptRecord;
use App\Models\Yxhd\YxhdOrder;
use App\Models\Yxhd\YxhdRecord;
use App\Models\Yxhd\YxhdUser;
use function Couchbase\defaultDecoder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class YxhdOrderController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        $id = null;
        $search_word = null;
        $activity_id = null;
        $user_id = null;
        $winning_status = null;

        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $id = $data['id'];
        }
        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }
        if (array_key_exists('activity_id', $data) && !Utils::isObjNull($data['activity_id'])) {
            $activity_id = $data['activity_id'];
        }
        if (array_key_exists('winning_status', $data) && !Utils::isObjNull($data['winning_status'])) {
            $winning_status = $data['winning_status'];
        }

        $con_arr = array(
            'id' => $id,
            'search_word' => $search_word,
            'activity_id' => $activity_id,
            'user_id' => $user_id,
            'winning_status' => $winning_status
        );
        $yxhdOrders = YxhdOrderManager::getListByCon($con_arr, true);
        foreach ($yxhdOrders as $yxhdOrder) {
            $yxhdOrder = YxhdOrderManager::getInfoByLevel($yxhdOrder, '012');
        }

        return view('admin.yxhd.yxhdOrder.index', ['datas' => $yxhdOrders, 'con_arr' => $con_arr]);
    }

}