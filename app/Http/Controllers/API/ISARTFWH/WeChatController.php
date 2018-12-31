<?php
/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2018/1/11
 * Time: 9:43
 */

namespace App\Http\Controllers\API\ISARTFWH;


use App\Components\DateTool;
use App\Components\GZH\BusiWordManager;
use App\Components\GZH\WeChatManager;
use App\Components\InviteNumManager;
use App\Components\LoginManager;
use App\Components\GZH\ReplyManager;
use App\Components\Mryh\MryhJoinManager;
use App\Components\RequestValidator;
use App\Components\ScoreRecordManager;
use App\Components\UserManager;
use App\Components\UserTJManager;
use App\Components\Utils;
use App\Components\Vote\VoteActivityManager;
use App\Components\Vote\VoteOrderManager;
use App\Components\Vote\VoteUserManager;
use App\Http\Controllers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\InviteCodeRecord;
use App\Models\Menu;
use App\Models\Mryh\MryhCertSend;
use App\Models\UserTJ;
use App\Models\Vote\VoteCertSend;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use function GuzzleHttp\Psr7\str;
use Illuminate\Support\Facades\Log;
use Yansongda\Pay\Pay;
use Illuminate\Http\Request;


class WechatController extends Controller
{

    //相关配置
    const ACCOUNT_CONFIG = Utils::OFFICIAL_ACCOUNT_CONFIG_VAL['isart'];     //配置文件位置
    const BUSI_NAME = "isart";      //业务名称

    /**
     * 处理微信的请求消息
     *
     * 消息包括
     *
     * @return string
     */
    public function serve()
    {
        Utils::processLog(__METHOD__, '', " " . 'request arrived.');
        $app = app(self::ACCOUNT_CONFIG);
        $app->server->push(function ($message) {
            $app = app(self::ACCOUNT_CONFIG);
            Utils::processLog(__METHOD__, '', " " . "server receive:" . json_encode($message));
            $user_openid = $message['FromUserName'];  //用户公众号openid
            Utils::processLog(__METHOD__, '', " " . 'user_openid:' . $user_openid);
            $wechat_user = $app->user->get($user_openid);        //通过用户openid获取信息
            Utils::processLog(__METHOD__, '', " " . 'wechat_user:' . json_encode($wechat_user));
            //补全基本信息
            $data = array(
                "avatar" => $wechat_user['headimgurl'],
                "nick_name" => $wechat_user['nickname'],
                "gender" => $wechat_user['sex'],
                "country" => $wechat_user['country'],
                "province" => $wechat_user['province'],
                "language" => $wechat_user['language'],
                "city" => $wechat_user['city'],
                "openid" => $wechat_user['openid'],
                "busi_name" => self::BUSI_NAME
            );
            //如果存在unionid，则补全unionid
            if (array_key_exists('unionid', $wechat_user)) {
                $data['unionid'] = $wechat_user['unionid'];
            }
            Utils::processLog(__METHOD__, '', " " . "server data:" . json_encode($data));
            //进行用户登录/注册
            $user = UserManager::login(Utils::ACCOUNT_TYPE_FWH, $data);
            Utils::processLog(__METHOD__, '', " " . "user:" . json_encode($user));
            //根据消息类型分别进行处理
            switch ($message['MsgType']) {
                case 'event':
                    //点击事件
                    if ($message['Event'] == 'CLICK') {
                        switch ($message['EventKey']) {

                        }
                    }
                    //关注事件
                    if ($message['Event'] == 'subscribe') {
                        $con_arr = array(
                            'template_id' => "TEMPLATE_SUBSCRIBE",
                            'busi_name' => self::BUSI_NAME
                        );
                        $busiWords = BusiWordManager::getListByCon($con_arr, false);
                        Utils::processLog(__METHOD__, '', " " . "busiWords:" . json_encode($busiWords));
                        foreach ($busiWords as $busiWord) {
                            $busiWord_message = BusiWordManager::setWechatMessage($busiWord, $user);
                            Utils::processLog(__METHOD__, '', " " . "busiWord_message:" . json_encode($busiWord_message));
                            $app->customer_service->message($busiWord_message)
                                ->to($user_openid)
                                ->send();
                        }
                    }
                    //取消关注事件
                    if ($message['Event'] == 'unsubscribe') {

                    }
                    //扫描进入事件
                    if ($message['Event'] == 'SCAN') {

                    }
                    break;
                case 'text':        //文本消息
                    $keyword = $message['Content'];     //关键字
                    Utils::processLog(__METHOD__, '', " " . "关键词:" . $keyword);
                    //1)优先匹配投票活动
                    $con_arr = array(
                        'code' => $keyword
                    );
                    $vote_activity = VoteActivityManager::getListByCon($con_arr, false)->first();
                    //如果存在活动，则返回活动
                    if ($vote_activity) {
                        return VoteActivityManager::convertToNewsItem($vote_activity->id);
                    }
                    //2)匹配证书，即关键词中是否有-，或者输入选手姓名，选手姓名部分特殊处理，如果有重名用户，则需要联系组委会
                    $vote_user_id = VoteUserManager::getIdByKeyword($keyword);
                    //如果有重名
                    if ($vote_user_id == -1) {
                        $text = WeChatManager::setTextMessage("与您重名的小选手有很多，您可以输入参赛编号获得证书，参赛编号在个人主页中体现，形式为HAA-21。");
                        return $text;
                    }
                    //如果不为空，则派发证书
                    if ($vote_user_id != null) {
                        $vote_user = VoteUserManager::getById($vote_user_id);
                        $vote_activity = VoteActivityManager::getById($vote_user->activity_id);
                        Utils::processLog(__METHOD__, '', " " . "活动信息:" . json_encode($vote_activity));
                        //如果投票活动未结束
                        if ($vote_activity->vote_status != '2') {
                            Utils::processLog(__METHOD__, " " . "活动还没有结束");
                            return WeChatManager::setTextMessage($vote_activity->name . "还没有结束，请您持续关注。");
                        }
                        //活动已经结束
                        $prize = VoteUserManager::getPrize($vote_user_id);
                        Utils::processLog(__METHOD__, '', " " . "获得奖励信息 prize:" . $prize);
                        Utils::processLog(__METHOD__, '', " " . "获得奖励信息 prize:" . $prize);
                        //没有获得奖励
                        if ($prize == null) {
                            Utils::processLog(__METHOD__, '', " " . "非常感谢您参加" . $vote_activity->name . "，很遗憾您没有获得奖项，如有疑问请联系大赛组委会。");
                            return WeChatManager::setTextMessage("非常感谢您参加" . $vote_activity->name . "，很遗憾您没有获得奖项，如有疑问请联系大赛组委会。");
                        } else {
                            //新建发送证书任务
                            $vote_cert_send = new VoteCertSend();
                            $vote_cert_send->vote_user_id = $vote_user_id;
                            $vote_cert_send->to_openid = $user_openid;
                            $vote_cert_send->save();
                            $vote_user_name = VoteUserManager::getVoteUserName($vote_user->name);
                            $text = WeChatManager::setTextMessage("祝贺" . $vote_user_name . "在大赛中取得名次，电子版证书正在生成，将在5分钟内下发，请您注意查收。");
                            return $text;
                        }
                    }
                    //3)关键词数据库匹配
                    $con_arr = array(
                        'search_word' => $keyword
                    );
                    $reply = ReplyManager::getListByCon($con_arr, false)->first();
                    Utils::processLog(__METHOD__, '', " " . "reply:" . json_encode($reply));
                    //如果匹配成功，返回响应消息
                    if ($reply) {
                        return ReplyManager::setWechatMessage($reply, $user);
                    }
                    //4）匹配每天一画的奖状
                    $join_id = MryhJoinManager::getIdByCode($keyword);
                    if ($join_id != null) {
                        //每天一画的参与记录
                        $mryhJoin = MryhJoinManager::getById($join_id);
                        if ($mryhJoin == null) {
                            return WeChatManager::setTextMessage("没有找到每天一画-" . $keyword . "编号证书");
                        }
                        $mryhJoin = MryhJoinManager::getInfoByLevel($mryhJoin, '01');
                        //如果活动还在进行中
                        if ($mryhJoin->game_status == '0') {
                            return WeChatManager::setTextMessage($mryhJoin->user->nick_name . "选手，您参加的" . $mryhJoin->game->name . "还没有结束，请继续关注，坚持每天一画，赢取鼓励金~");
                        }
                        if ($mryhJoin->game_status == '1') {
                            $mryh_cert_send = new MryhCertSend();
                            $mryh_cert_send->join_id = $mryhJoin->id;
                            $mryh_cert_send->to_openid = $user_openid;
                            $mryh_cert_send->save();
                            return WeChatManager::setTextMessage($mryhJoin->user->nick_name . "选手，恭喜您" . $mryhJoin->game->name . "挑战成功，获得鼓励金，获奖证书将于5分钟内下发，请注意查收。");
                        }
                        if ($mryhJoin->game_status == '2') {
                            return WeChatManager::setTextMessage($mryhJoin->user->nick_name . "选手，很遗憾您参加的" . $mryhJoin->game->name . "挑战失败，没有获得鼓励金，再接再厉，点击参赛继续挑战！");
                        }
                    }

                    break;
                case 'image':

                    break;
                case 'voice':

                    break;
                case 'video':

                    break;
                case 'location':

                    break;
                case 'link':

                    break;
                // ... 其它消息
                default:
//                    return '';
                    break;
            }
        });
        $response = $app->server->serve();
        return $response;
    }


    /*
     * 投票支付结果通知
     *
     * By TerryQi
     *
     * 2018-07-24
     */
    public function votePayNotify(Request $request)
    {
//        dd($request->all());
        $config = Utils::getPaymentConfig('isart');
        $wechat = Pay::wechat($config);
        try {
            $data = $wechat->verify($request->getContent()); // 是的，验签就这么简单！
            Utils::processLog(__METHOD__, '', 'Wechat payNotify', $data->all());
            //支付成功
            if ($data->result_code == "SUCCESS") {
                //订单号out_trade_no
                $trade_no = $data->out_trade_no;
                $vote_order = VoteOrderManager::getByTradeNo($trade_no);
                /*
                 * 11月1日的问题
                 *
                 * 此处要注意，由于存在业务过大，导致没有及时响应微信的支付请求，微信方面重复触发支付消息，从而多次记录支付信息
                 *
                 * 后续应该添补逻辑，向系统管理员发送通知，进行业务稽核
                 *
                 * By TerryQi
                 *
                 * 2018-11-06
                 */
                if ($vote_order->pay_status == '1') {
                    return $wechat->success();
                }

                //将订单设定为支付成功状态
                $vote_order->pay_type = '0';           //支付状态，目前只有微信支付
                $vote_order->pay_at = DateTool::getCurrentTime();   //支付时间
                $vote_order->pay_status = '1';           //支付成功
                $vote_order->save();

                //处理统计信息
                //用户
                VoteUserManager::addStatistics($vote_order->vote_user_id, 'vote_num', $vote_order->as_vote_num);        //票数
                VoteUserManager::addStatistics($vote_order->vote_user_id, 'gift_money', $vote_order->total_fee);        //礼物总金额
                //活动
                VoteActivityManager::addStatistics($vote_order->activity_id, 'vote_num', $vote_order->as_vote_num);       //票数
                VoteActivityManager::addStatistics($vote_order->activity_id, 'gift_money', $vote_order->total_fee);       //礼物总金额
                VoteActivityManager::addStatistics($vote_order->activity_id, 'gift_num', $vote_order->gift_num);       //礼物总数

                //增加积分
                $scoreChange = array(
                    'user_id' => $vote_order->user_id,
                    'score' => (int)($vote_order->total_fee * Utils::YXHD_ORDER_MULTI_SCORE_VAL),
                    'opt' => '1',
                    'remark' => '投票大赛打赏奖励积分'
                );
                ScoreRecordManager::change($scoreChange);

            }
            return $wechat->success();
        } catch (Exception $e) {
            Utils::processLog(__METHOD__, '', $e->getMessage());
        }
    }

    /*
     * 小程序微信config测试
     *
     * By TerryQi
     *
     * 2018-07-18
     */
    public function wxConfig(Request $request)
    {
        $data = $request->all();
        Utils::processLog(__METHOD__, '', ' data:' . json_encode($data));
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'url' => 'required',
            'jsApiList' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $jsApiList = "onMenuShareAppMessage,onMenuShareTimeline";
        //获取items参数
        if (array_key_exists('jsApiList', $data) && !Utils::isObjNull($data['jsApiList'])) {
            $jsApiList = $data['jsApiList'];
        }
        //debug标识
        $debug = false;
        if (array_key_exists('debug', $data) && !Utils::isObjNull($data['debug'])) {
            $debug = $data['debug'];
        }
        Utils::processLog(__METHOD__, '', ' debug:' . $debug);

        //生成微信JS-SDK相关
        $wxConfig = null;
        $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL[self::BUSI_NAME]);
        Utils::processLog(__METHOD__, '', " " . "url:" . $data['url']);
        $app->jssdk->setUrl($data['url']);
        Utils::processLog(__METHOD__, '', ' debug:' . strval($debug));
        if (strval($debug) == "true") {
            $debug = true;
        } else {
            $debug = false;
        }
        $wxConfig = $app->jssdk->buildConfig(explode(",", $jsApiList), $debug);     //此处要有阿虎
        Utils::processLog(__METHOD__, '', " " . "wxConfig:" . json_encode($wxConfig));
        return ApiResponse::makeResponse(true, json_decode($wxConfig), ApiResponse::SUCCESS_CODE);

    }

}