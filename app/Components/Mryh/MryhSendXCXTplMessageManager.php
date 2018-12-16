<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\Mryh;

use App\Components\ArticleManager;
use App\Components\DateTool;
use App\Components\GuanZhuManager;
use App\Components\LoginManager;
use App\Components\Mryh\MryhJoinManager;
use App\Components\Mryh\MryhWithdrawCashManager;
use App\Components\UserManager;
use App\Components\XCXFormManager;
use App\Components\XCXTplMessageManager;
use App\Models\YSB\YSBUser;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class MryhSendXCXTplMessageManager
{

    const ACCOUNT_CONFIG = "wechat.mini_program.mryh";     //配置文件位置

    /*
     * 发送参赛成功/失败结果通知
     *
     * By TerryQi
     *
     * 2018-11-15
     *
     */
    public static function sendJoinResultMessage($join_id)
    {
        //获取参赛信息
        $mryhJoin = MryhJoinManager::getById($join_id);
        if (!$mryhJoin) {
            return false;
        }
        //补充参赛信息
        $mryhJoin = MryhJoinManager::getInfoByLevel($mryhJoin, "01");

        //获取是否有未使用的xcx_form
        $con_arr = array(
            'user_id' => $mryhJoin->user_id,
            'busi_name' => Utils::BUSI_NAME_MRYH,
            'f_table' => Utils::F_TABLE_MRYH_JOIN,
            'f_id' => $mryhJoin->id,
            'used_flag' => '0'        //未使用
        );
        $xcxForm = XCXFormManager::getListByCon($con_arr, false)->first();
        //是否存在发送可能性
        if (!$xcxForm) {
            return false;
        }

        //进行消息发送
        $login = LoginManager::getListByCon(['user_id' => $mryhJoin->user_id, 'account_type' => 'xcx', 'busi_name' => Utils::BUSI_NAME_MRYH], false)->first();
        //存在不登录信息
        if (!$login) {
            return false;
        }

        $xcxForm->used_flag = '1';      //设置为已使用
        $xcxForm->save();

        //配置信息
        $touser = $login->ve_value1;
        $page = "pages/myGameInfo/main?join_id=" . $mryhJoin->id;       //跳转到参赛详情页面
        $form_id = $xcxForm->form_id;

        $app = app(self::ACCOUNT_CONFIG);

        $keyword1 = $mryhJoin->game->name;      //活动名称
        $keyword2 = $mryhJoin->game_status == '1' ? '挑战成功' : '挑战失败';
        $keyword3 = $mryhJoin->game_status == '1' ? '点击查看挑战记录，领取获奖证书，您的挑战金已经退还，奖励金将于活动结束后发放。' : '点击查看挑战记录，不要气馁，更多挑战等你来。';

        $param = array(
            'touser' => $touser,
            'page' => $page,
            'form_id' => $form_id
        );
        $info = array(
            'keyword1' => $keyword1,
            'keyword2' => $keyword2,
            'keyword3' => $keyword3
        );
        $result = XCXTplMessageManager::sendMessage($app, Utils::XCX_MRYH_JOIN_RESULT_NOFITY, $param, $info);

        return $result;

    }


    /*
     * 发送大赛参赛提醒
     *
     * By TerryQi
     *
     * 2018-11-25
     */
    public static function sendJoinNotifyMessage($join_id)
    {
        //获取参赛信息
        $mryhJoin = MryhJoinManager::getById($join_id);
        if (!$mryhJoin) {
            return false;
        }
        //补充参赛信息
        $mryhJoin = MryhJoinManager::getInfoByLevel($mryhJoin, "01");

        //获取是否有未使用的xcx_form
        $con_arr = array(
            'user_id' => $mryhJoin->user_id,
            'busi_name' => Utils::BUSI_NAME_MRYH,
            'f_table' => Utils::F_TABLE_MRYH_JOIN,
            'f_id' => $mryhJoin->id,
            'used_flag' => '0'        //未使用
        );
        $xcxForm = XCXFormManager::getListByCon($con_arr, false)->first();
        //是否存在发送可能性
        if (!$xcxForm) {
            return false;
        }

        //进行消息发送
        $login = LoginManager::getListByCon(['user_id' => $mryhJoin->user_id, 'account_type' => 'xcx', 'busi_name' => Utils::BUSI_NAME_MRYH], false)->first();
        //存在不登录信息
        if (!$login) {
            return false;
        }

        $xcxForm->used_flag = '1';      //设置为已使用
        $xcxForm->save();

        //配置信息
        $touser = $login->ve_value1;
        $page = "pages/myGameInfo/main?join_id=" . $mryhJoin->id;       //跳转到参赛详情页面
        $form_id = $xcxForm->form_id;

        $app = app(self::ACCOUNT_CONFIG);

        $keyword1 = $mryhJoin->game->name;      //活动名称
        $keyword2 = DateTool::getCurrentTime();
        $keyword3 = "请您注意上传作品，完成今日任务。";

        $param = array(
            'touser' => $touser,
            'page' => $page,
            'form_id' => $form_id
        );
        $info = array(
            'keyword1' => $keyword1,
            'keyword2' => $keyword2,
            'keyword3' => $keyword3
        );
        $result = XCXTplMessageManager::sendMessage($app, Utils::XCX_MRYH_JOIN_SCHEDULE_NOTIFY, $param, $info);

        return $result;
    }


    /*
     * 发送提现通知
     *
     * By TerryQi
     *
     * 2018-11-26
     *
     */
    public static function sendWithdrawNotify($withdrawCash_id)
    {
        //获取提现记录信息
        $mryh_withdrawCash = MryhWithdrawCashManager::getById($withdrawCash_id);
        //如果没有提现记录信息
        if (!$mryh_withdrawCash) {
            return false;
        }
        $mryh_withdrawCash = MryhWithdrawCashManager::getInfoByLevel($mryh_withdrawCash, '01');

        //获取是否有未使用的xcx_form
        $con_arr = array(
            'user_id' => $mryh_withdrawCash->user_id,
            'busi_name' => Utils::BUSI_NAME_MRYH,
            'f_table' => Utils::F_TABLE_MRYH_WITHDRAW,
            'f_id' => $mryh_withdrawCash->id,
            'used_flag' => '0'        //未使用
        );
        $xcxForm = XCXFormManager::getListByCon($con_arr, false)->first();
        //是否存在发送可能性
        if (!$xcxForm) {
            return false;
        }
        //进行消息发送
        $login = LoginManager::getListByCon(['user_id' => $mryh_withdrawCash->user_id, 'account_type' => 'xcx', 'busi_name' => Utils::BUSI_NAME_MRYH], false)->first();
        //存在不登录信息
        if (!$login) {
            return false;
        }

        $xcxForm->used_flag = '1';      //设置为已使用
        $xcxForm->save();

        //配置信息
        $touser = $login->ve_value1;
        $page = "pages/index/main";       //跳转到参赛详情页面
        $form_id = $xcxForm->form_id;


        $app = app(self::ACCOUNT_CONFIG);

        $keyword1 = $mryh_withdrawCash->amount;      //提现金额
        $keyword2 = Utils::MRYH_WITHDRAW_CASH_WITHDRAW_STATUS[$mryh_withdrawCash->withdraw_status];
        $keyword3 = "感谢您参加每天一画，再接再厉，赢取奖励。";

        $param = array(
            'touser' => $touser,
            'page' => $page,
            'form_id' => $form_id
        );
        $info = array(
            'keyword1' => $keyword1,
            'keyword2' => $keyword2,
            'keyword3' => $keyword3
        );
        $result = XCXTplMessageManager::sendMessage($app, Utils::XCX_MRYH_WITHDRAW_NOFITY, $param, $info);

        return $result;
    }


    /*
     * 发送邀请目标达成提醒
     *
     * By TerryQi
     *
     * 2018-11-26
     */
    public static function sendInviteSuccessNotify($user_id)
    {
        //获取是否有未使用的xcx_form
        $con_arr = array(
            'user_id' => $user_id,
            'busi_name' => Utils::BUSI_NAME_MRYH,
            'f_table' => Utils::F_TABLE_MRYH_GAME,
            'used_flag' => '0'        //未使用
        );
        $xcxForm = XCXFormManager::getListByCon($con_arr, false)->first();
        //是否存在发送可能性
        if (!$xcxForm) {
            return false;
        }

        //进行消息发送
        $login = LoginManager::getListByCon(['user_id' => $user_id, 'account_type' => 'xcx', 'busi_name' => Utils::BUSI_NAME_MRYH], false)->first();
        //存在不登录信息
        if (!$login) {
            return false;
        }
        $xcxForm->used_flag = '1';      //设置为已使用
        $xcxForm->save();

        //配置信息
        $touser = $login->ve_value1;
        $page = "pages/gameDetail/main?game_id=" . $xcxForm->f_id;       //活动页面
        $form_id = $xcxForm->form_id;

        $app = app(self::ACCOUNT_CONFIG);

        $keyword1 = "每天一画邀请好友免费参与任务";      //活动名称
        $keyword2 = '任务完成';
        $keyword3 = '点击参加每天一画任务，赢取奖励金';

        $param = array(
            'touser' => $touser,
            'page' => $page,
            'form_id' => $form_id
        );
        $info = array(
            'keyword1' => $keyword1,
            'keyword2' => $keyword2,
            'keyword3' => $keyword3
        );
        $result = XCXTplMessageManager::sendMessage($app, Utils::XCX_MRYH_JOIN_RESULT_NOFITY, $param, $info);

        return $result;

    }


}