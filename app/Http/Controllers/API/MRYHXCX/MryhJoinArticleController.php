<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API\MRYHXCX;


use App\Components\ArticleManager;
use App\Components\DateTool;
use App\Components\LoginManager;
use App\Components\Mryh\MryhADManager;
use App\Components\Mryh\MryhCouponManager;
use App\Components\Mryh\MryhGameManager;
use App\Components\Mryh\MryhJoinArticleManager;
use App\Components\Mryh\MryhJoinManager;
use App\Components\Mryh\MryhJoinOrderManager;
use App\Components\Mryh\MryhUserCouponManager;
use App\Components\Mryh\MryhUserManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Components\Mryh\MryhSendXCXTplMessageManager;
use App\Http\Controllers\ApiResponse;
use App\Models\Mryh\MryhJoin;
use App\Models\Mryh\MryhJoinArticle;
use App\Models\Mryh\MryhJoinOrder;
use App\Models\XCXForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yansongda\Pay\Pay;

class MryhJoinArticleController
{

    //相关配置
    const ACCOUNT_CONFIG = "wechat.payment.mryh";     //配置文件位置
    const BUSI_NAME = "mryh";      //业务名称

    /*
     * 参加活动上传作品
     *
     * By TerryQi
     *
     * 2018-08-17
     */
    public function upload(Request $request)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
            'join_id' => 'required',
            'article' => 'required'
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //是否参与活动
        $mryhJoin = MryhJoinManager::getById($data['join_id']);
        if (!$mryhJoin) {
            return ApiResponse::makeResponse(false, "没有找到参赛记录", ApiResponse::INNER_ERROR);
        }
        //活动是否有效
        $mryhGame = MryhGameManager::getById($mryhJoin->game_id);
        if ($mryhGame->status == '0') {
            return ApiResponse::makeResponse(false, "该场次活动已经失效", ApiResponse::INNER_ERROR);
        }
        //是否存在用户在每天一画中的业务信息
        $mryhUser = MryhUserManager::getByUserId($data['user_id']);
        if (!$mryhUser) {
            return ApiResponse::makeResponse(false, "没有找到每天一画的用户信息", ApiResponse::INNER_ERROR);
        }
        //该活动参与记录是否归属该用户
        if ($mryhJoin->user_id != $data['user_id']) {
            return ApiResponse::makeResponse(false, "该条参与记录不属于此用户", ApiResponse::INNER_ERROR);
        }
        //活动是否在进行中
        if ($mryhJoin->game_status != '0') {
            return ApiResponse::makeResponse(false, "比赛不在进行中", ApiResponse::INNER_ERROR);
        }
        //可以参加大赛
        //补充article中的user_id信息
        $data['article']["user_id"] = $mryhUser->user_id;
        //补充busi_name信息
        if (array_key_exists('busi_name', $data) && !Utils::isObjNull($data['busi_name'])) {
            $data['article']['busi_name'] = $data['busi_name'];         //配置一下busi_name
        }
        $article = ArticleManager::setArticle($data['article']);
        //如果有form_id，则配置小程序服务消息
        if (array_key_exists('form_id', $data['article']) && !Utils::isObjNull($data['article']['form_id'])) {
            $xcxForm = new XCXForm();
            $xcxForm->user_id = $article->user_id;
            $xcxForm->busi_name = $article->busi_name;
            $xcxForm->total_num = 1;
            $xcxForm->form_id = $data['article']['form_id'];
            $xcxForm->f_table = Utils::F_TABLE_MRYH_JOIN;
            $xcxForm->f_id = $mryhJoin->id;
            $xcxForm->save();
            Utils::processLog(__METHOD__, '', " " . "保存form id，用于消息发送:" . json_encode($xcxForm));
        }

        //生成参赛记录
        $mryhJoinArticle = new MryhJoinArticle();
        $mryhJoinArticle->join_id = $mryhJoin->id;
        $mryhJoinArticle->user_id = $mryhUser->user_id;
        $mryhJoinArticle->game_id = $mryhJoin->game_id;
        $mryhJoinArticle->article_id = $article->id;
        $mryhJoinArticle->date_at = DateTool::getToday();
        $mryhJoinArticle->save();
        Utils::processLog(__METHOD__, '', " " . "生成参赛记录:" . json_encode($mryhJoinArticle));

        //进行记录统计-参赛作品数增加、大赛参与作品数增加
        MryhJoinManager::addStatistics($mryhJoin->id, 'work_num', 1);
        MryhGameManager::addStatistics($mryhGame->id, 'work_num', 1);

        //当天上传作品数
        $upload_mryhJoinArticles_num = MryhJoinManager::uploadNumAtDate($mryhJoin->id, DateTool::getToday());
        Utils::processLog(__METHOD__, '', " " . "当天上传作品数:" . json_encode($upload_mryhJoinArticles_num));

        //如果此作品为当日上传的第一幅作品
        if ($upload_mryhJoinArticles_num <= 1) {
            MryhJoinManager::addStatistics($mryhJoin->id, 'join_day_num', 1);
            Utils::processLog(__METHOD__, '', " " . "增加参与天数");
        }

        //判断是否已经成功
        $mryhJoin = MryhJoinManager::getById($mryhJoin->id);
        Utils::processLog(__METHOD__, '', " " . "此时参赛记录信息：" . json_encode($mryhJoin));
        //如果成功且活动是参与中的状态
        /*
         * 2018-12-07进行逻辑调整，调整处为增加mryhJoin->game_status==0，活动在进行中的状态，一旦成功，game_status==1，避免多次进入错误逻辑
         *
         * By TerryQi
         */
        if ($mryhJoin->join_day_num >= $mryhGame->target_join_day && $mryhJoin->game_status == '0') {
            Utils::processLog(__METHOD__, '', "达成成功目标 " . "mryhJoin->join_day_num:" . $mryhJoin->join_day_num . " mryhGame->target_join_day:" . $mryhGame->target_join_day);
            //参与状态设置为成功
            $mryhJoin->game_status = '1';
            $mryhJoin->save();
            //活动的成功数增加
            MryhGameManager::addStatistics($mryhGame->id, 'success_num', 1);
            //进行退款
            Utils::processLog(__METHOD__, '', " " . "开始执行退款代码，活动信息：" . json_encode($mryhJoin));
            $app = app(self::ACCOUNT_CONFIG);
            //如果订单号不为空
            if (!Utils::isObjNull($mryhJoin->trade_no)) {
                $mryhJoinOrder = MryhJoinOrderManager::getByTradeNo($mryhJoin->trade_no);
                Utils::processLog(__METHOD__, '', "退款订单信息:" . json_encode($mryhJoinOrder));
                //如果有订单且trade_no不为空
                if ($mryhJoinOrder && !Utils::isObjNull($mryhJoinOrder->trade_no)) {
                    Utils::processLog(__METHOD__, '', "有订单，且需要退款:" . json_encode($mryhJoinOrder));
                    $result = MryhJoinOrderManager::refundByTradeNo($app, $mryhJoinOrder->trade_no, "您参与的 " . $mryhGame->name . " 活动达到挑战目标，退还参与金");
                    Utils::processLog(__METHOD__, '', "退款结果result:" . json_encode($result));
                }
            }

            //发送小程序成功提醒
            MryhSendXCXTplMessageManager::sendJoinResultMessage($mryhJoin->id);

            //2018-12-17增加逻辑，即如果失败，则增加mryh_user_info中的join_notify_num，即参赛提醒消息数
            $mryhUser = MryhUserManager::getByUserId($mryhJoin->user_id);
            $mryhUser->join_notify_num = $mryhUser->join_notify_num + 1;        //参赛提醒消息数+1
            $mryhJoin->save();

        }
        return ApiResponse::makeResponse(true, $mryhJoinArticle, ApiResponse::SUCCESS_CODE);
    }

    /*
     * 根据条件获取作品列表
     *
     * By TerryQi
     *
     * 2018-08-18
     */
    public function getListByCon(Request $request)
    {
        $data = $request->all();

        //配置条件
        $join_id = null;
        $user_id = null;
        $game_id = null;
        $article_id = null;
        $date_at = null;
        $level = '13';      //获取信息级别

        if (array_key_exists('join_id', $data) && !Utils::isObjNull($data['join_id'])) {
            $join_id = $data['join_id'];
        }
        if (array_key_exists('p_user_id', $data) && !Utils::isObjNull($data['p_user_id'])) {
            $user_id = $data['p_user_id'];
        }
        if (array_key_exists('game_id', $data) && !Utils::isObjNull($data['game_id'])) {
            $game_id = $data['game_id'];
        }
        if (array_key_exists('article_id', $data) && !Utils::isObjNull($data['article_id'])) {
            $article_id = $data['article_id'];
        }
        if (array_key_exists('date_at', $data) && !Utils::isObjNull($data['date_at'])) {
            $date_at = $data['date_at'];
        }

        //获取信息级别
        if (array_key_exists('level', $data) && !Utils::isObjNull($data['level'])) {
            $level = $data['level'];
        }

        $con_arr = array(
            'join_id' => $join_id,
            'user_id' => $user_id,
            'game_id' => $game_id,
            'article_id' => $article_id,
            'date_at' => $date_at
        );
        $mryhJoinArticles = MryhJoinArticleManager::getListByCon($con_arr, true);

        foreach ($mryhJoinArticles as $mryhJoinArticle) {
            $mryhJoinArticle = MryhJoinArticleManager::getInfoByLevel($mryhJoinArticle, $level);
        }
        return ApiResponse::makeResponse(true, $mryhJoinArticles, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 作品详情页面
     *
     * By TerryQi
     *
     * 2018-12-05
     *
     */
    public function getById(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }

        //获取joinArticle信息
        $mryhJoinArticle = MryhJoinArticleManager::getById($data['id']);
        if (!$mryhJoinArticle) {
            return ApiResponse::makeResponse(false, "未找到参与作品的信息", ApiResponse::INNER_ERROR);
        }

        //作品信息
        $article = ArticleManager::getById($mryhJoinArticle->article_id);
        $article = ArticleManager::getInfoByLevel($article, "03");
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $article = ArticleManager::setRel($data['user_id'], $article);
        }

        //参赛信息
        $mryhJoin = MryhJoinManager::getById($mryhJoinArticle->join_id);
        $mryhJoin = MryhJoinManager::getInfoByLevel($mryhJoin, '');

        //大赛信息
        $mryhGame = MryhGameManager::getById($mryhJoinArticle->game_id);
        $mryhGame = MryhGameManager::getInfoByLevel($mryhGame, '');

        //其他活动
        $other_mryhGames = MryhGameManager::getRecommendGames($mryhJoin->user_id, [$mryhJoin->game_id], 3);

        $page_data = array(
            'article' => $article,
            'mryhJoin' => $mryhJoin,
            'mryhGame' => $mryhGame,
            'other_games' => $other_mryhGames,
        );

        //增加统计信息
        ArticleManager::addStatistics($article->id, 'show_num', random_int(1, 3));      //随机增加1-3

        return ApiResponse::makeResponse(true, $page_data, ApiResponse::SUCCESS_CODE);

    }

}





