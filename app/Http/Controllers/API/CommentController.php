<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API;


use App\Components\FTableManager;
use App\Components\GuanZhuManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Components\CommentManager;
use App\Http\Controllers\ApiResponse;
use App\Models\GuanZhu;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController
{
    /*
     * 评论接口
     *
     * By TerryQi
     *
     * 2018-09-19
     *
     */
    public function setComment(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
            'f_id' => 'required',
            'f_table' => 'required',
            'content' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //评论
        $comment = new Comment();
        $comment = CommentManager::setInfo($comment, $data);
        $comment->save();

        //统计数据
        FTableManager::addStatistics($data['f_table'], $data['f_id'], 'comm_num', 1);

        return ApiResponse::makeResponse(true, "评论成功", ApiResponse::SUCCESS_CODE);
    }


    /*
     * 取消评论
     *
     * By TerryQi
     *
     * 2018-09-19
     */
    public function cancelComment(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $comment = CommentManager::getById($data['id']);
        $comment->save();

        //统计数据
        FTableManager::addStatistics($data['f_table'], $data['f_id'], 'comm_num', -1);

        return ApiResponse::makeResponse(true, "取消评论", ApiResponse::SUCCESS_CODE);
    }

}





