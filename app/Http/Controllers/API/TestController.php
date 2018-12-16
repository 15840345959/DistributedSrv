<?php
/**
 * File_Name:TestController.php
 * Author: leek
 * Date: 2017/9/26
 * Time: 11:19
 */

namespace App\Http\Controllers\API;

use App\Components\DateTool;
use App\Components\GZH\WeChatManager;
use App\Components\MapManager;
use App\Components\TestManager;
use App\Components\Utils;
use App\Components\Vote\VoteActivityManager;
use App\Components\Vote\VoteCertSendManager;
use App\Components\Vote\VoteUserManager;
use App\Http\Controllers\ApiResponse;
use App\Http\Controllers\Controller;
use EasyWeChat\Kernel\Messages\Image;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use App\Components\RequestValidator;
use Yansongda\Pay\Log;


class TestController extends Controller
{
    //加密
    public function jiami(Request $request)
    {
        $data = $request->all();

        $result = encrypt($data['id']);

        return ApiResponse::makeResponse(true, $result, ApiResponse::SUCCESS_CODE);
    }

    //解密
    public function jiemi(Request $request)
    {
        $data = $request->all();
        $result = null;
        try {
            $result = decrypt($data['m_id']);
        } catch (DecryptException $e) {
            Utils::processLog(__METHOD__, '', "DecryptException e:" . $e);
        }

        return ApiResponse::makeResponse(true, $result, ApiResponse::SUCCESS_CODE);
    }


    //测试相关
    public function test(Request $request)
    {
        //此处封装参数，参数应该来自请求，此处仅为示例
        $param = array(
            'openid' => 'oJpZ11DU7GZpoW9W_NB5HwXrlYd8',       //项目pro_code应该统一管理，建议在Utils中定义一个通用变量
        );
        $result = Utils::curl('http://testapi.gowithtommy.com/rest/pay/js_pre_order/', $param, true);   //访问接口
        $result = json_decode($result, true);   //因为返回的已经是json数据，为了适配makeResponse方法，所以进行json转数组操作

        return ApiResponse::makeResponse(true, $result, ApiResponse::SUCCESS_CODE);
    }


    public function testYQM(Request $request)
    {
        $param = array(
            'openId' => "o51su5VKEsk7O2nqXrilFPcTe55k",
            'sign' => md5(base64_encode("openId|" . "o51su5VKEsk7O2nqXrilFPcTe55k" . "|Free|Edition"))
        );
//        dd(Utils::SERVER_URL);
        $result = Utils::curl(Utils::SERVER_URL . '/rest/user/public_number/invi_code/', $param, true);   //访问接口
        $result = json_decode($result, true);   //因为返回的已经是json数据，为了适配makeResponse方法，所以进行json转数组操作
        return ApiResponse::makeResponse(true, $result, ApiResponse::SUCCESS_CODE);
    }

    //图片合并
    public function testMergePic(Request $request)
    {
        $path_1 = public_path('img/') . 'fxhb_bg.jpg';
        $path_2 = public_path('img/') . 'user18_yq_code.jpg';
        $image_1 = imagecreatefromjpeg($path_1);
        $image_2 = imagecreatefromjpeg($path_2);
        list($width, $height) = getimagesize($path_2);
        //生成缩略图 二维码 200*200
        $ewm_width = 200;
        $ewm_height = 200;
        $image_2_resize = imagecreatetruecolor($ewm_width, $ewm_height);
        imagecopyresized($image_2_resize, $image_2, 0, 0, 0, 0, $ewm_width, $ewm_height, $width, $height);

        $image_3 = imageCreatetruecolor(imagesx($image_1), imagesy($image_1));
        $color = imagecolorallocate($image_3, 255, 255, 255);
        imagefill($image_3, 0, 0, $color);
        imageColorTransparent($image_3, $color);
        imagecopyresampled($image_3, $image_1, 0, 0, 0, 0, imagesx($image_1), imagesy($image_1), imagesx($image_1), imagesy($image_1));
        imagecopymerge($image_3, $image_2_resize, 45, 1100, 0, 0, imagesx($image_2_resize), imagesy($image_2_resize), 100);
        imagejpeg($image_3, public_path('img/') . 'merge.jpg');

        return ApiResponse::makeResponse(true, public_path('img/') . 'merge.jpg', ApiResponse::SUCCESS_CODE);
    }

    /*
     * 根据经纬度获取地址信息
     *
     * By TerryQi
     *
     * 2018-01-09
     *
     */
    public function getLocation(Request $request)
    {
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'lat' => 'required',
            'lon' => 'required',
        ]);
        $data = $request->all();
        $result = MapManager::getLocationByLatLon($data['lat'], $data['lon']);
        return ApiResponse::makeResponse(true, $result, ApiResponse::SUCCESS_CODE);
    }

    /*
     * 78元邀请码代码
     *
     * By TerryQi
     *
     * 2018-04-11
     */
    public function test78InviteCode(Request $request)
    {
        $param = array();
//        $result = Utils::curl(Utils::SERVER_URL.'/rest/user/public_number/invi_code/', $param, false);   //访问接口
        $result = Utils::curl(Utils::SERVER_URL . '/rest/user/public_number_pay/invi_code/', $param, false);   //访问接口
        dd($result);
    }


    /*
     * 测试生成证书
     *
     * By TerryQi
     *
     * 2018-09-12
     */
    public function sendCert(Request $request)
    {
        $keyword = "HAA-1";
        $vote_user_id = VoteUserManager::getIdByCode($keyword);
        Utils::processLog(__METHOD__, '', " " . "选手id:" . $vote_user_id);
        //找到选手信息
        if ($vote_user_id != null) {
            $vote_user = VoteUserManager::getById($vote_user_id);
            $vote_activity = VoteActivityManager::getById($vote_user->activity_id);
            Utils::processLog(__METHOD__, '', "活动信息:" . json_encode($vote_activity));
            //如果投票活动未结束
            if ($vote_activity->vote_status != '2') {
                Utils::processLog(__METHOD__, '', " " . "活动还没有结束");
                return $vote_activity->name . "还没有结束，请您持续关注";
            }
            //活动已经结束
            $prize = VoteUserManager::getPrize($vote_user_id);
            Utils::processLog(__METHOD__, '', " " . "获得奖励信息 prize:" . $prize);
            //没有获得奖励
            if ($prize == null) {
                Utils::processLog(__METHOD__, '',  "非常感谢您参加" . $vote_activity->name . "，很遗憾您没有获得奖项，如有疑问请联系大赛组委会。");
                return "非常感谢您参加" . $vote_activity->name . "，很遗憾您没有获得奖项，如有疑问请联系大赛组委会。";
            } else {
                //获得奖励，则返回证书
                /*
                 * By TerryQi
                 */
                Utils::processLog(__METHOD__, '',  "activity code:" . $vote_activity->code);
                Utils::processLog(__METHOD__, '',   "vote user code:" . $vote_user->code);
                $cert_no = $vote_activity->code . '-' . $vote_user->code;        //证书编号
                Utils::processLog(__METHOD__, '',   "证书编号 cert_no:" . json_encode($cert_no));
                $info_arr = [
                    'name' => VoteUserManager::getVoteUserName($vote_user->name),
                    'prize' => $prize,
                    'cert_no' => $cert_no,
                    'date' => DateTool::getYMDChi($vote_activity->vote_end_time)
                ];
                Utils::processLog(__METHOD__, '', "生成证书条件 info_arr:" . json_encode($info_arr));
                $cert_path = VoteUserManager::generateCert($info_arr);
                Utils::processLog(__METHOD__, '', " " . "生成证书路径 cert_path:" . $cert_path);
                $app = app('wechat.official_account.isart');
                $temp_media_id = WeChatManager::createMediaId($cert_path, $app);
                Utils::processLog(__METHOD__, '', "生成临时素材 temp_media_id:" . $temp_media_id);
                //发送证书
                $image = new Image($temp_media_id);
                $app->customer_service->message($image)
                    ->to("o0irR0oQxxABTTjB84rQmY4KYaAM")
                    ->send();
                return "祝贺您在大赛中取得名次，电子版证书已经发放，请您注意查收。";
            }
        }
    }

    /*
     * 生成投票大赛证书
     *
     * By TerryQi
     *
     * 2018-09-11
     */
    public function voteCert(Request $request)
    {
        //证书基础文件
        $cert_base_path = public_path('img/vote/cert/vote_cert_base.jpg');
//        dd($cert_base_path);
        $cert_base_img = imagecreatefromjpeg($cert_base_path);

        //生成新图片
        $generate_cert_path = public_path('img/vote/cert/' . Utils::generateTradeNo() . '.jpg');
        imagejpeg($cert_base_img, $generate_cert_path);
        $generate_cert_img = imagecreatefromjpeg($generate_cert_path);

        $fontfile = public_path('docs/css/fonts/msyh.ttf');

        //选手姓名
        $user_name = "藏奇";
        // 分配颜色和透明度
        $color = imagecolorallocatealpha($generate_cert_img, 0, 0, 0, 0);
        imagettftext($generate_cert_img, 70, 0, 2900, 680, $color, $fontfile, $user_name);

        //所获奖项
        $prize_name = "金  奖";
        $color = imagecolorallocatealpha($generate_cert_img, 245, 0, 0, 0);
        imagettftext($generate_cert_img, 80, 0, 3000, 1200, $color, $fontfile, $prize_name);

        //证书编号
        $cert_no = "HBA-12";
        $color = imagecolorallocatealpha($generate_cert_img, 0, 0, 0, 0);
        imagettftext($generate_cert_img, 50, 0, 950, 2650, $color, $fontfile, $cert_no);

        //发证日期
        $cert_time = "2018年11月2日";
        $color = imagecolorallocatealpha($generate_cert_img, 0, 0, 0, 0);
        imagettftext($generate_cert_img, 50, 0, 950, 2790, $color, $fontfile, $cert_time);


        //生成图片数据
        imagejpeg($generate_cert_img, $generate_cert_path);
        //销毁数据
        imagedestroy($generate_cert_img);


        return ApiResponse::makeResponse(true, "证书生成成功", ApiResponse::SUCCESS_CODE);
    }

    //生成计划任务
    public function sendCertSchedule(Request $request)
    {
        VoteCertSendManager::voteCertSendSchedule();

        return ApiResponse::makeResponse(true, "证书生成成功", ApiResponse::SUCCESS_CODE);
    }

}