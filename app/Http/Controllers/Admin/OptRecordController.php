<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/20
 * Time: 10:50
 */

namespace App\Http\Controllers\Admin;


use App\Components\DateTool;
use App\Components\OptInfoManager;
use App\Components\OptRecordManager;
use App\Components\QNManager;
use App\Components\RequestValidator;
use App\Components\Utils;
use App\Components\Vote\VoteActivityManager;
use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\ApiResponse;
use App\Models\OptRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OptRecordController
{
    /*
     * 首页
     *
     * By TerryQi
     *
     * 2018-4-20
     */
    public function edit(Request $request)
    {
        $method = $request->method();

        $data = $request->all();

        $admin = $request->session()->get('admin');

        $upload_token = QNManager::uploadToken();

        switch ($method) {
            case 'GET':
                $info = null;

                $requestValidationResult = RequestValidator::validator($request->all(), [
                    'f_id' => 'required',
                    'f_table' => 'required',
                ]);
                if ($requestValidationResult !== true) {
                    return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数' . $requestValidationResult]);
                }
                //获取操作选项
                $con_arr = array(
                    'f_table' => $data['f_table']
                );
                $optInfos = OptInfoManager::getListByCon($con_arr, false);

                return view('admin.optRecord.edit', ['admin' => $admin, 'optInfos' => $optInfos, 'f_id' => $data['f_id'], 'f_table' => $data['f_table'], 'upload_token' => $upload_token]);
                break;
            case 'POST':
//                dd($data);
                $optRecord = new OptRecord();
                $return = null;
                if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
                    $optRecord = OptRecordManager::getById($data['id']);
                }
                if (!$optRecord) {
                    $optRecord = new OptRecord();
                }
                $optRecord = OptRecordManager::setInfo($optRecord, $data);
                $optRecord->admin_id = $admin->id;
                $result = $optRecord->save();
//                dd($result);
                $optRecord = OptRecordManager::getById($optRecord->id);
                if ($result) {
                    //根据操作变更状态
                    switch ($optRecord->f_table) {
                        case Utils::OPT_TYPE_ACTIVITY:
                            $this->setActivitySettleStatus($optRecord);
                            break;
                    }
                    return ApiResponse::makeResponse(true, [], ApiResponse::SUCCESS_CODE);
                } else {
                    return ApiResponse::makeResponse(false, [], ApiResponse::INNER_ERROR);
                }
                break;
            default:
                break;
        }
    }

    //根据optRecord影响活动结算状态
    public function setActivitySettleStatus($opt_record)
    {
        $optInfo = OptInfoManager::getById($opt_record->opt_id);

        $activity = VoteActivityManager::getById($opt_record->f_id);

        switch ($optInfo->value) {
            case Utils::OPT_FINISHED:
                $activity->is_settle = 1;
                break;
            case Utils::OPT_HANDLING:
                $activity->is_settle = 0;
                break;
        }
        $activity->save();
        return;
    }

}











