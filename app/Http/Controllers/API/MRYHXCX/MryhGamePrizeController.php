<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API\MRYHXCX;


use App\Components\DateTool;
use App\Components\LoginManager;
use App\Components\Mryh\MryhADManager;
use App\Components\Mryh\MryhCouponManager;
use App\Components\Mryh\MryhGameManager;
use App\Components\Mryh\MryhGamePrizeManager;
use App\Components\Mryh\MryhJoinManager;
use App\Components\Mryh\MryhJoinOrderManager;
use App\Components\Mryh\MryhUserCouponManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Mryh\MryhJoin;
use App\Models\Mryh\MryhJoinOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yansongda\Pay\Pay;

class MryhGamePrizeController
{

    //相关配置
    const ACCOUNT_CONFIG = "wechat.payment.mryh";     //配置文件位置
    const BUSI_NAME = "mryh";      //业务名称

    /*
     * ！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！
     * 进行奖励-测试奖励，此接口不允许对外开放，即调测完毕后必须去掉路由！！！！！！！！！！！！！！！！！！！！！！
     * ！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！
     *
     * By TerryQi
     *
     * 2018-08-17
     */
    public function testPrize(Request $request)
    {
        $data = $request->all();

        $app = app(self::ACCOUNT_CONFIG);
        $result = MryhGamePrizeManager::sendPrize($app, Utils::generateTradeNo(), 'oX2Ol5MCzwhFAsr5SOUP7CzUtwfQ', 'NO_CHECK', '', '150', '达成目标奖励金');

        return ApiResponse::makeResponse(true, $result, ApiResponse::SUCCESS_CODE);
    }
}





