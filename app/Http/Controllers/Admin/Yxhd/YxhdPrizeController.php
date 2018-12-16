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
use App\Components\Yxhd\YxhdPrizeManager;
use App\Components\Yxhd\YxhdRuleManager;
use App\Components\Yxhd\YxhdTeamManager;
use App\Http\Controllers\ApiResponse;
use App\Models\OptInfo;
use App\Models\OptRecord;
use App\Models\Yxhd\YxhdPrize;
use App\Models\Yxhd\YxhdOrder;
use App\Models\Yxhd\YxhdRecord;
use App\Models\Yxhd\YxhdUser;
use function Couchbase\defaultDecoder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class YxhdPrizeController
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
        $yxhdPrizes = YxhdPrizeManager::getListByCon($con_arr, true);
        foreach ($yxhdPrizes as $yxhdPrize) {
            $yxhdPrize = YxhdPrizeManager::getInfoByLevel($yxhdPrize, '');
        }

        return view('admin.yxhd.yxhdPrize.index', ['datas' => $yxhdPrizes, 'con_arr' => $con_arr]);
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
        $yxhdPrize = new YxhdPrize();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $yxhdPrize = YxhdPrizeManager::getById($data['id']);
//            dd($yxhdPrize);
        }
        $yxhdPrize = YxhdPrizeManager::setInfo($yxhdPrize, $data);

        return view('admin.yxhd.yxhdPrize.edit', ['admin' => $admin, 'data' => $yxhdPrize
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
        $yxhdPrize = new YxhdPrize();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $yxhdPrize = YxhdPrizeManager::getById($data['id']);
//            dd($yxhdPrize);
        }

        $yxhdPrize = YxhdPrizeManager::setInfo($yxhdPrize, $data);
        $yxhdPrize->admin_id = $admin->id;      //记录管理员id
        $yxhdPrize->save();

        return ApiResponse::makeResponse(true, $yxhdPrize, ApiResponse::SUCCESS_CODE);
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
        $yxhdPrize = YxhdPrizeManager::getById($id);
        //设置大赛状态
        $yxhdPrize->status = $data['status'];
        $yxhdPrize->save();
        return ApiResponse::makeResponse(true, $yxhdPrize, ApiResponse::SUCCESS_CODE);
    }

}