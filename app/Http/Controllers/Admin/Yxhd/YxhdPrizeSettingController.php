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
use App\Components\Yxhd\YxhdActivityManager;
use App\Components\Yxhd\YxhdPrizeManager;
use App\Components\Yxhd\YxhdPrizeSettingManager;
use App\Components\Yxhd\YxhdRuleManager;
use App\Components\Yxhd\YxhdTeamManager;
use App\Http\Controllers\ApiResponse;
use App\Models\OptInfo;
use App\Models\OptRecord;
use App\Models\Yxhd\YxhdPrizeSetting;
use App\Models\Yxhd\YxhdOrder;
use App\Models\Yxhd\YxhdRecord;
use App\Models\Yxhd\YxhdUser;
use function Couchbase\defaultDecoder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class YxhdPrizeSettingController
{
    /*
     * 配置礼品信息
     *
     * By TerryQi
     *
     * 2018-12-11
     */
    public function edit(Request $request)
    {
        $data = $request->all();
        //        dd($data);
        $admin = $request->session()->get('admin');
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'activity_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数' . $requestValidationResult]);
        }

        //生成七牛token
        $upload_token = QNManager::uploadToken();

        //营销活动配置信息
        $yxhdPrizeSetting = new YxhdPrizeSetting();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $yxhdPrizeSetting = YxhdPrizeSettingManager::getById($data['id']);
        }

        $yxhdActivity = YxhdActivityManager::getById($data['activity_id']);     //营销活动信息
        $con_arr = array(
            'status' => '1',
        );
        $yxhdPrizes = YxhdPrizeManager::getListByCon($con_arr, true);

        foreach ($yxhdPrizes as $yxhdPrize) {
            $yxhdPrize = YxhdPrizeSettingManager::getInfoByLevel($yxhdPrize, '');
        }

//        dd($yxhdPrizeSetting);

        return view('admin.yxhd.yxhdPrizeSetting.edit', ['data' => $yxhdPrizeSetting,
            'yxhdPrizes' => $yxhdPrizes, 'upload_token' => $upload_token, 'yxhdActivity' => $yxhdActivity]);
    }


    /*
     * 配置奖品信息-post
     *
     * By TerryQi
     *
     * 2018-12-11
     *
     */
    public function editPost(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');

        $yxhdPrizeSetting = new YxhdPrizeSetting();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $yxhdPrizeSetting = YxhdPrizeSettingManager::getById($data['id']);
        }
        $yxhdPrizeSetting = YxhdPrizeSettingManager::setInfo($yxhdPrizeSetting, $data);
        $yxhdPrizeSetting->admin_id = $admin->id;
        $yxhdPrizeSetting->save();

//        dd($yxhdPrizeSetting);

        return ApiResponse::makeResponse(true, $yxhdPrizeSetting, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 删除信息
     *
     * By TerryQi
     *
     * 2018-12-11
     */
    public function del(Request $request, $id)
    {
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数id$id']);
        }
        $yxhdPrizeSetting = YxhdPrizeSettingManager::getById($id);
//        dd($yxhdPrizeSetting);
        $yxhdPrizeSetting->delete();

        return ApiResponse::makeResponse(true, $yxhdPrizeSetting, ApiResponse::SUCCESS_CODE);
    }

}