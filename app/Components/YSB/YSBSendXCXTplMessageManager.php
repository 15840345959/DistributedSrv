<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\YSB;

use App\Components\ArticleManager;
use App\Components\DateTool;
use App\Components\GuanZhuManager;
use App\Components\LoginManager;
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

class YSBSendXCXTplMessageManager
{

    const ACCOUNT_CONFIG = "wechat.mini_program.ysb";     //配置文件位置

    /*
     * 发送作品审核消息
     *
     * By TerryQi
     *
     * 2018-11-15
     */
    public static function sendAuditMessage($article, $audit_status)
    {
        $user = UserManager::getById($article->user_id);
        //如果是普通用户，才进行消息发送
        if ($user->type == "1") {
            return false;
        }
        //获取是否有未使用的xcx_form
        $con_arr = array(
            'user_id' => $article->user_id,
            'busi_name' => $article->busi_name,
            'f_table' => Utils::F_TABLB_ARTICLE,
            'f_id' => $article->id,
            'used_flag' => '0'        //未使用
        );
        $xcxForm = XCXFormManager::getListByCon($con_arr, false)->first();
        if (!$xcxForm) {
            return false;
        }
        //进行消息发送
        $login = LoginManager::getListByCon(['user_id' => $user->id, 'account_type' => 'xcx', 'busi_name' => $article->busi_name], false)->first();
        //存在不登录信息
        if (!$login) {
            return false;
        }

        $xcxForm->used_flag = '1';      //设置为已使用
        $xcxForm->save();

        //配置信息
        $touser = $login->ve_value1;
        $page = "/pages/folder/folder?tw_id=" . $article->id;       //跳转到作品页面
        $form_id = $xcxForm->form_id;

        $app = app(self::ACCOUNT_CONFIG);

        $keyword1 = ($audit_status == '1') ? "作品审核通过" : "作品审核驳回";
        $keyword2 = DateTool::getYMDHSChi(DateTool::getCurrentTime());
        $keyword3 = ($audit_status == '1') ?
            "您的作品《" . $article->name . "》受到了小伙伴的喜爱，审核通过，感谢您加入ISART艺术榜。"
            : "很遗憾，您的作品《" . $article->name . "》没有审核通过，再接再厉，感谢您加入ISART艺术榜，如有疑问请联系小编。";
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
        $result = XCXTplMessageManager::sendMessage($app, Utils::XCX_YSB_SCHEDULE_NOTIFY, $param, $info);

        return $result;
    }


}