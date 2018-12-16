<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API\YSBXCX;


use App\Components\ArticleManager;
use App\Components\UserManager;
use App\Components\YSB\YSBADManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\YSB\YSBUserManager;
use App\Http\Controllers\ApiResponse;
use App\Models\YSB\YSBUser;
use Illuminate\Http\Request;

class YSBUserController
{
    /*
     * 艺术榜个人首页
     *
     * By mtt
     *
     * 2018-09-27
     */
    public function person(Request $request)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'p_user_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //用户id
        $p_user_id = $data['p_user_id'];
        $user = UserManager::getById($p_user_id);
        if (!$user) {
            return ApiResponse::makeResponse(false, "用户不存在", ApiResponse::INNER_ERROR);
        }
        //用户基本信息
        $ysbUser = YSBUserManager::getListByCon(['user_id' => $user->id], false)->first();
        //此处增加逻辑，确保有ysbUser
        if (!$ysbUser) {
            $ysbUser = new YSBUser();
            $ysbUser->user_id = $user->id;
            $ysbUser->save();
        }
        $ysbUser = YSBUserManager::getInfoByLevel($ysbUser, '123');
        $user->ysbUser = $ysbUser;
        //用户作品信息
        $articles = ArticleManager::getListByCon(['user_id' => $user->id, 'busi_name' => 'ysb'], false);
        $user->articles = $articles;
        //系统作品信息
        $xt_articles = ArticleManager::getListByCon(['busi_name' => 'ysb', 'sys_flag' => '1'], false);
        $user->xt_articles = $xt_articles;
        return ApiResponse::makeResponse(true, $user, ApiResponse::SUCCESS_CODE);
    }

}





