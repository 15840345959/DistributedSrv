<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\Mryh;

use App\Components\UserManager;
use App\Models\Mryh\MryhAD;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class MryhPicManager
{


    /*
     * 生成小程序的分享海报
     *
     * By TerryQi
     *
     * 2018-10-11
     *
     * 其中info中应该包含 name：昵称  avatar：头像 ewm_code：分享二维码code的路径
     *
     */
    public static function generateHaiBao($info_arr)
    {
        //合规校验
        if (!array_key_exists('name', $info_arr) || !array_key_exists('avatar', $info_arr)
            || !array_key_exists('ewm_code', $info_arr)) {
            return null;
        }
        //相关参数
        $name = $info_arr['name'];
        $avatar = $info_arr['avatar'];
        $ewm_code = $info_arr['ewm_code'];

        $haibao_base_path = public_path('img/mryh/haibao/mryh_hb_bg.jpg');
        $haibao_base_img = imagecreatefromjpeg($haibao_base_path);

        //生成新图片
        $file_name = Utils::generateTradeNo() . '.png';
        $generate_haibao_path = public_path('img/mryh/haibao/' . $file_name);
        imagejpeg($haibao_base_img, $generate_haibao_path);
        $generate_haibao_img = imagecreatefromjpeg($generate_haibao_path);

        //放置二维码
        $ewm_code_size = 130;
        $ewm_code_img = imagecreatefromjpeg($ewm_code);
        $ewm_code_img = Utils::resizeImage($ewm_code_img, $ewm_code_size, $ewm_code_size);      //调整二维码的大小
        imagecopymerge($generate_haibao_img, $ewm_code_img, 430, 600, 0, 0, imagesx($ewm_code_img), imagesy($ewm_code_img), 100);

        //放置头像
        $avatar_filename = Utils::downloadFile($avatar, public_path('img/mryh/haibao'), Utils::generateTradeNo() . '.png');
        $avatar_path = public_path('img/mryh/haibao/' . $avatar_filename);
        $avatar_size = 70;
        $avatar_img = Utils::getCirclePic($avatar_path);
        $avatar_img = Utils::resizeImage($avatar_img, $avatar_size, $avatar_size);        //调整头像大小
        imagecopymerge($generate_haibao_img, $avatar_img, 40, 635, 0, 0, imagesx($avatar_img), imagesy($avatar_img), 100);

        $fontfile = public_path('docs/css/fonts/msyh.ttf');

        //放置昵称
        $color = imagecolorallocatealpha($generate_haibao_img, 0, 0, 0, 0);
        imagettftext($generate_haibao_img, 18, 0, 120, 655, $color, $fontfile, $name);

        //放置口号
        $color = imagecolorallocatealpha($generate_haibao_img, 99, 99, 99, 0);
        imagettftext($generate_haibao_img, 12, 0, 130, 695, $color, $fontfile, "快来和我每天一画赢取奖金吧！");

        //生成图片数据
        imagejpeg($generate_haibao_img, $generate_haibao_path);

        return $file_name;
    }


    /*
     * 生成证书
     *
     * By TerryQi
     *
     * 2018-10-13
     */
    public static function generateCert($mryhJoin_id)
    {

    }

    /*
     * 生成活动的海报
     *
     * By TerryQi
     *
     * 2018-10-13
     */
    public static function generateGameHaiBao($mryhGame_id, $user_id, $app)
    {
        //获取每天一画活动信息
        $mryhGame = MryhGameManager::getById($mryhGame_id);
        Utils::processLog(__METHOD__, '', "每天一画信息:" . json_encode($mryhGame));
        //未找到活动
        if (!$mryhGame) {
            return null;
        }
//        //用户信息，通过配置获取用户基本信息
//        $user = UserManager::getById($user_id);
//        //生成海报-配置基础值
//        $name = ($user->nick_name == null) ? "ISART" : $user->nick_name;
//        $avatar = ($user->avatar == null) ? "logo180.jpg" : Utils::downloadFile($user->avatar, public_path('img/mryh/haibao'), Utils::generateTradeNo() . '.jpg');
//
//        Utils::processLog(__METHOD__, '', "name:" . json_encode($name) . " avatar:" . json_encode($avatar));

        $fontfile = public_path('docs/css/fonts/msyh.ttf');

        Utils::processLog(__METHOD__, '', "开始海报的配置");
        //海报底图
        $haibao_base_path = public_path('img/mryh/haibao/mryh_game_haibao_bg.jpg');
        $haibao_base_img = imagecreatefromjpeg($haibao_base_path);
        Utils::processLog(__METHOD__, '', "记载海报底图");
        //生成新图片
        $file_name = Utils::generateTradeNo() . '.png';
        Utils::processLog(__METHOD__, '', "file_name:" . $file_name);
        $generate_haibao_path = public_path('img/mryh/haibao/' . $file_name);
        imagejpeg($haibao_base_img, $generate_haibao_path);
        $generate_haibao_img = imagecreatefromjpeg($generate_haibao_path);
        Utils::processLog(__METHOD__, '', "生成新海报");
        //活动分享图片
        $share_img_filename = Utils::downloadFile($mryhGame->share_img, public_path('img/mryh/haibao'), Utils::generateTradeNo() . '.jpg');
        $share_img_path = public_path('img/mryh/haibao/' . $share_img_filename);
        Utils::processLog(__METHOD__, '', "share_img_path:" . $share_img_path);
        $share_img = imagecreatefromjpeg($share_img_path);
        $share_img = Utils::resizeImage($share_img, 510, 510);

        imagecopymerge($generate_haibao_img, $share_img, 0, 0, 0, 0, imagesx($share_img), imagesy($share_img), 100);
        Utils::processLog(__METHOD__, '', "集成分享图片");
        //生成小程序二维码
        $ewm_filename = Utils::generateTradeNo() . ".png";
        Utils::processLog(__METHOD__, '', "ewm_file_name:" . $ewm_filename);

        $ewm_response = $app->app_code->get('pages/gameDetail/main?game_id=' . $mryhGame->id, [
            'width' => 400,
        ]);
        $ewm_response->saveAs(public_path('img/mryh/haibao/'), $ewm_filename);
        $ewm_img = imagecreatefromjpeg(public_path('img/mryh/haibao/') . $ewm_filename);
        $ewm_img = Utils::resizeImage($ewm_img, 140, 140);

        imagecopymerge($generate_haibao_img, $ewm_img, 330, 650, 0, 0, imagesx($ewm_img), imagesy($ewm_img), 100);
        Utils::processLog(__METHOD__, '', "集成小程序二维码图片");
        //活动标题
        $color = imagecolorallocatealpha($generate_haibao_img, 0, 0, 0, 0);
        imagettftext($generate_haibao_img, 38, 0, 385, 575, $color, $fontfile, $mryhGame->target_join_day);
        imagettftext($generate_haibao_img, 38, 0, 386, 576, $color, $fontfile, $mryhGame->target_join_day);
//        imagettftext($generate_haibao_img, 46, 0, 464, 684, $color, $fontfile, $mryhGame->target_join_day);
        Utils::processLog(__METHOD__, '', "集成天数文字");
        //生成图片数据
        Utils::processLog(__METHOD__, '', "generate_haibao_path:" . $generate_haibao_path);
        imagejpeg($generate_haibao_img, $generate_haibao_path);
        Utils::processLog(__METHOD__, '', "完成海报图片的生成");
        return $file_name;
    }
}