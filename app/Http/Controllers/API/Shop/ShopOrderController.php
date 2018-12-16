<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API\Shop;


use App\Components\DateTool;
use App\Components\GoodsManager;
use App\Components\LoginManager;
use App\Components\Shop\ShopADManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\Shop\ShopOrderManager;
use App\Components\SMSManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Shop\ShopOrder;
use Illuminate\Http\Request;
use Yansongda\Pay\Pay;

class ShopOrderController
{

    const BUSI_NAME = "shop";      //业务名称

    /*
     * 下单接口
     *
     * By TerryQi
     *
     * 2018-11-12
     */
    public function payOrder(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
            'goods_id' => 'required',
            'goods_num' => 'required',
            'rec_name' => 'required',
            'rec_tel' => 'required',
            'rec_address' => 'required',
        ]);

        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //1)先进性商品有效性判断
        $goods = GoodsManager::getById($data['goods_id']);
        if (!$goods || $goods->status != '1') {     //如果商品不存在或者状态不为1-有效
            return ApiResponse::makeResponse(false, "商品状态不正确", ApiResponse::INNER_ERROR);
        }
        //2)判断用户的有效性，获取用户的openid
        $con_arr = array(
            'user_id' => $data['user_id'],
            'account_type' => 'fwh',
            'busi_name' => 'isart'
        );
        $login = LoginManager::getListByCon($con_arr, false)->first();
        if (!$login) {
            return ApiResponse::makeResponse(false, "未找到用户", ApiResponse::INNER_ERROR);
        }
        //计算订单信息
        $total_fee = $goods->price * $data['goods_num'];
        Utils::processLog(__METHOD__, 'goods', json_encode($goods));
        Utils::processLog(__METHOD__, 'total_fee', json_encode($total_fee));
        //生成订单并保存
        $shopOrder = new ShopOrder();
        $shopOrder = ShopOrderManager::setInfo($shopOrder, $data);
        $shopOrder->total_fee = $total_fee;
        $shopOrder->trade_no = Utils::generateTradeNo();    //生成订单
        $shopOrder->save();
        Utils::processLog(__METHOD__, '生成的订单信息', json_encode($shopOrder));

        $wx_order = array(
            'body' => $goods->name,
            'out_trade_no' => $shopOrder->trade_no,
            'total_fee' => $shopOrder->total_fee * 100,
            'trade_type' => 'JSAPI',
            'openid' => $login->ve_value1,
        );
        Utils::processLog(__METHOD__, "支付下单 " . "wx_order:" . json_encode($wx_order));
        $config = Utils::getPaymentConfig(self::BUSI_NAME);
        $result = Pay::wechat($config)->mp($wx_order);
        Utils::processLog(__METHOD__, "下单结果 " . "result:" . json_encode($result));

        return ApiResponse::makeResponse(true, json_decode($result), ApiResponse::SUCCESS_CODE);

    }


    /*
     * 支付结果通知接口
     *
     * By TerryQi
     *
     * 2018-11-13
     *
     */
    public function payNotify(Request $request)
    {
        $config = Utils::getPaymentConfig(self::BUSI_NAME);
        $wechat = Pay::wechat($config);
        try {
            $data = $wechat->verify($request->getContent()); // 是的，验签就这么简单！
            Utils::processLog(__METHOD__, '', 'Wechat payNotify', $data->all());
            //支付成功
            if ($data->result_code == "SUCCESS") {
                //订单号out_trade_no
                $trade_no = $data->out_trade_no;
                $shopOrder = ShopOrderManager::getByTradeNo($trade_no);

                if ($shopOrder->pay_status == '1') {
                    return $wechat->success();
                }
                //将订单设定为支付成功状态
                $shopOrder->pay_type = '0';           //支付状态，目前只有微信支付
                $shopOrder->pay_at = DateTool::getCurrentTime();   //支付时间
                $shopOrder->pay_status = '1';           //支付成功
                $shopOrder->save();

                //处理统计信息
                GoodsManager::addStatistics($shopOrder->goods_id, 'sale_num', $shopOrder->goods_num);        //商品销售数+goods_num
                GoodsManager::addStatistics($shopOrder->goods_id, 'left_num', $shopOrder->goods_num);        //商品销售数-goods_num

                //向运营管理员进行短信提醒
                $goods = GoodsManager::getById($shopOrder->goods_id);
                $editor = UserManager::getById($goods->editor_id);
                //如果找到了编辑并且有电话
                if ($editor && !Utils::isObjNull($editor->phonenum)) {
                    $sms_text = $shopOrder->trade_no . "," . $goods->name;
                    SMSManager::sendSMS($editor->phonenum, Utils::SHOP_SMS_TEMPLATE_PAYORDER, $sms_text);
                }
            }
            return $wechat->success();
        } catch (Exception $e) {
            Utils::processLog(__METHOD__, '', $e->getMessage());
        }
    }


}





