<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\Yxhd;

use App\Components\AdminManager;
use App\Components\DateTool;
use App\Components\OptInfoManager;
use App\Components\OptRecordManager;
use App\Components\QNManager;
use App\Components\RequestValidator;
use App\Components\Utils;
use App\Components\Yxhd\YxhdActivityManager;
use App\Components\Yxhd\YxhdPrizeManager;
use App\Components\Yxhd\YxhdPrizeSettingManager;
use App\Components\Yxhd\YxhdRuleManager;
use App\Components\Yxhd\YxhdTeamManager;
use App\Http\Controllers\ApiResponse;
use App\Models\OptInfo;
use App\Models\OptRecord;
use App\Models\Yxhd\YxhdActivity;
use App\Models\Yxhd\YxhdOrder;
use App\Models\Yxhd\YxhdRecord;
use App\Models\Yxhd\YxhdUser;
use function Couchbase\defaultDecoder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class YxhdActivityController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        $id = null;
        $search_word = null;

        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $id = $data['id'];
        }
        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }
        $con_arr = array(
            'id' => $id,
            'search_word' => $search_word,
        );
        $yxhdActivities = YxhdActivityManager::getListByCon($con_arr, true);
        foreach ($yxhdActivities as $yxhdActivity) {
            $yxhdActivity = YxhdActivityManager::getInfoByLevel($yxhdActivity, '');
        }

        return view('admin.yxhd.yxhdActivity.index', ['datas' => $yxhdActivities, 'con_arr' => $con_arr]);
    }

    /*
     * 添加、编辑大赛-get
     *
     * By TerryQi
     *
     * 2018-4-9
     */
    public function edit(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        //设置项目 setting item为设置项目，按照顺序排下来
        $item = 0;
        if (array_key_exists('item', $data)) {
            $item = $data['item'];
        }
        //生成七牛token
        $upload_token = QNManager::uploadToken();
        $yxhdActivity = new YxhdActivity();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $yxhdActivity = YxhdActivityManager::getById($data['id']);
//            dd($yxhdActivity);
        }
        $yxhdActivity = YxhdActivityManager::setInfo($yxhdActivity, $data);

        //营销活动奖品信息
        /*
         * By TerryQi
         *
         * 2018-12-10
         */
        $yxhdPrizeSettings = YxhdPrizeSettingManager::getListByCon(['activity_id' => $yxhdActivity->id], false);
        foreach ($yxhdPrizeSettings as $yxhdPrizeSetting) {
            $yxhdPrizeSetting = YxhdPrizeSettingManager::getInfoByLevel($yxhdPrizeSetting, '1');
        }

        return view('admin.yxhd.yxhdActivity.edit', ['admin' => $admin, 'data' => $yxhdActivity, 'yxhdPrizeSettings' => $yxhdPrizeSettings
            , 'upload_token' => $upload_token, 'item' => $item]);
    }

    /*
     * 添加、编辑大赛-post
     *
     * By TerryQi
     *
     * 2018-4-9
     *
     */
    public function editPost(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');
        //关键字是否重复
        if (array_key_exists('code', $data) && !Utils::isObjNull($data['code'])) {
            $con_arr = array(
                'code' => $data['code']
            );
            $code_yxhdActivity = YxhdActivityManager::getListByCon($con_arr, false)->first();       //获关键字重复的活动
            //如果关键字可以检索出活动
            if ($code_yxhdActivity) {
                if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
                    //带id为编辑活动，如果code非自己则报错
                    if ($code_yxhdActivity->id != $data['id']) {
                        return ApiResponse::makeResponse(false, "关键字重复", ApiResponse::INNER_ERROR);
                    }
                } else {
                    return ApiResponse::makeResponse(false, "关键字重复", ApiResponse::INNER_ERROR);
                }
            }
        }
        $yxhdActivity = new YxhdActivity();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $yxhdActivity = YxhdActivityManager::getById($data['id']);
//            dd($yxhdActivity);
        }

        $yxhdActivity = YxhdActivityManager::setInfo($yxhdActivity, $data);
        $yxhdActivity->admin_id = $admin->id;      //记录管理员id
        $yxhdActivity->save();

        return ApiResponse::makeResponse(true, $yxhdActivity, ApiResponse::SUCCESS_CODE);
    }


    //设置大赛状态
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数礼品id$id']);
        }
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'status' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数' . $requestValidationResult]);
        }
        $yxhdActivity = YxhdActivityManager::getById($id);
        //如果是启动服务，判断大赛配置项目的合法性
        if ($data['status'] == '1') {
            if (Utils::isObjNull($yxhdActivity->name)) {
                return ApiResponse::makeResponse(false, "大赛名称未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($yxhdActivity->img)) {
                return ApiResponse::makeResponse(false, "首页封皮未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($yxhdActivity->intro_html)) {
                return ApiResponse::makeResponse(false, "活动详情未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($yxhdActivity->code)) {
                return ApiResponse::makeResponse(false, "活动代码未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($yxhdActivity->join_score)) {
                return ApiResponse::makeResponse(false, "参赛积分未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($yxhdActivity->share_title)) {
                return ApiResponse::makeResponse(false, "分享图片未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($yxhdActivity->share_title)) {
                return ApiResponse::makeResponse(false, "分享标题未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($yxhdActivity->share_desc)) {
                return ApiResponse::makeResponse(false, "分享描述未设置", ApiResponse::INNER_ERROR);
            }
            $yxhdPrizeSettings = YxhdPrizeSettingManager::getListByCon(['activity_id' => $yxhdActivity->id], false);
            if ($yxhdPrizeSettings->count() < 3) {
                return ApiResponse::makeResponse(false, "奖品配置小于3个", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($yxhdActivity->share_desc)) {
                return ApiResponse::makeResponse(false, "分享描述未设置", ApiResponse::INNER_ERROR);
            }
        }

        //设置大赛状态
        $yxhdActivity->status = $data['status'];
        $yxhdActivity->save();
        return ApiResponse::makeResponse(true, $yxhdActivity, ApiResponse::SUCCESS_CODE);
    }


}