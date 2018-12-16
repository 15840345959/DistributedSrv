<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API;


use App\Components\FeedBackManager;
use App\Components\GuanZhuManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\FeedBack;
use App\Models\GuanZhu;
use Illuminate\Http\Request;

class FeedBackController
{
    /*
     * 反馈接口
     *
     * By TerryQi
     *
     * 218-06-21
     *
     * opt: 0代表取消关注 1:代表关注
     */
    public function commit(Request $request)
    {
        $data = $request->all();

        $requestValidationResult = RequestValidator::validator($request->all(), [
            'content' => 'required',
            'busi_name' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }

        $feedBack = new FeedBack();
        $feedBack = FeedBackManager::setInfo($feedBack, $data);
        $feedBack->save();

        return ApiResponse::makeResponse(true, $feedBack, ApiResponse::SUCCESS_CODE);
    }
}





