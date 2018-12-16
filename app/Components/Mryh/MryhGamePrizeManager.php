<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\Mryh;

use App\Components\DateTool;
use App\Components\UserManager;
use App\Models\Mryh\MryhJoinOrder;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class MryhGamePrizeManager
{
    /*
     * 进行奖励
     *
     * By TerryQi
     *
     * 2018-08-19
     */
    public static function sendPrize($app, $partner_trade_no, $openid, $check_name, $re_user_name, $amount, $desc)
    {
        Utils::processLog(__METHOD__, '', "partner_trade_no:" . $partner_trade_no . " openid:" . $openid . ' check_name:' . $check_name . ' re_user_name:' . $re_user_name . ' amcount:' . $amount . ' desc:' . $desc);
        //校验金额，不能大于50元
        if ($amount >= 50 * 100) {
            return false;
        }
        $result = $app->transfer->toBalance([
            'partner_trade_no' => $partner_trade_no, // 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
            'openid' => $openid,
            'check_name' => 'NO_CHECK', // NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
            're_user_name' => '', // 如果 check_name 设置为FORCE_CHECK，则必填用户真实姓名
            'amount' => $amount, // 企业付款金额，单位为分
            'desc' => $desc, // 企业付款操作说明信息。必填
        ]);
        Utils::processLog(__METHOD__, '', "result:" . json_encode($result));
        return true;
    }

}