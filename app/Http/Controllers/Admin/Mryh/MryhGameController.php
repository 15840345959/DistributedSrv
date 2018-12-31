<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\Mryh;

use App\Components\AdminManager;
use App\Components\DateTool;
use App\Components\QNManager;
use App\Components\RequestValidator;
use App\Components\RuleManager;
use App\Components\SMSManager;
use App\Components\Utils;
use App\Components\Mryh\MryhGameManager;

use App\Components\VertifyManager;
use App\Http\Controllers\ApiResponse;
use App\Models\Mryh\MryhGame;
use App\Models\Vertify;
use Illuminate\Http\Request;


class MryhGameController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        $search_word = null;
        $game_status = null;
        $type = null;
        $id = null;
        $creator_id = null;
        $creator_type = null;

        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }
        if (array_key_exists('game_status', $data) && !Utils::isObjNull($data['game_status'])) {
            $game_status = $data['game_status'];
        }
        if (array_key_exists('type', $data) && !Utils::isObjNull($data['type'])) {
            $type = $data['type'];
        }
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $id = $data['id'];
        }
        if (array_key_exists('creator_id', $data) && !Utils::isObjNull($data['creator_id'])) {
            $creator_id = $data['creator_id'];
        }
        if (array_key_exists('creator_type', $data) && !Utils::isObjNull($data['creator_type'])) {
            $creator_type = $data['creator_type'];
        }

        $con_arr = array(
            'search_word' => $search_word,
            'game_status' => $game_status,
            'type' => $type,
            'id' => $id,
            'creator_id' => $creator_id,
            'creator_type' => $creator_type,
        );
        $mryhGames = MryhGameManager::getListByCon($con_arr, true);
        foreach ($mryhGames as $mryhGame) {
            $mryhGame = MryhGameManager::getInfoByLevel($mryhGame, '2');
        }
//        dd($mryhGames);
        return view('admin.mryh.mryhGame.index', ['datas' => $mryhGames, 'con_arr' => $con_arr]);
    }

    /*
     * 添加、编辑活动-get
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
        $mryhGame = new MryhGame();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $mryhGame = MryhGameManager::getById($data['id']);
            $mryhGame = MryhGameManager::getInfoByLevel($mryhGame, '0123');
//            dd($mryhGame);
        }
        $mryhGame = MryhGameManager::setInfo($mryhGame, $data);

        //生成活动规则选项
        $rules = RuleManager::getListByCon(['status' => '1', 'busi_name' => 'mryh', 'position' => '1'], false);

        return view('admin.mryh.mryhGame.edit', ['admin' => $admin, 'data' => $mryhGame
            , 'upload_token' => $upload_token, 'item' => $item, 'rules' => $rules]);
    }

    /*
     * 添加、编辑活动-post
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
        $mryhGame = new MryhGame();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $mryhGame = MryhGameManager::getById($data['id']);
            //已经结束的活动不允许修改，如果需要修改请联系技术组
            /*
             * 2018-12-2
             *
             * 优化逻辑，已经结束的活动不允许修改活动的参赛、开始结束时间、参赛金额等，从前端控制，后端做二次校验
             *
             */
            if ($mryhGame->game_status == '2') {
                if (array_key_exists('join_start_time', $data) || array_key_exists('join_end_time', $data)
                    || array_key_exists('game_start_time', $data) || array_key_exists('game_end_time', $data)
                    || array_key_exists('join_price', $data) || array_key_exists('target_join_day', $data))
                    return ApiResponse::makeResponse(false, "已经结束的活动不允许编辑核心信息，请联系技术组处理", ApiResponse::INNER_ERROR);
            }
        } else {
            //如果是新建活动，应该设置创建者信息
            $mryhGame->creator_type = '0';
            $mryhGame->creator_id = $admin->id;
        }
        $mryhGame = MryhGameManager::setInfo($mryhGame, $data);
        $mryhGame->save();

        return ApiResponse::makeResponse(true, $mryhGame, ApiResponse::SUCCESS_CODE);
    }


    //设置活动状态
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
        $mryhGame = MryhGameManager::getById($id);
        //如果是启动服务，判断活动配置项目的合法性
        if ($data['status'] == '1') {
            if (Utils::isObjNull($mryhGame->name)) {
                return ApiResponse::makeResponse(false, "活动名称未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($mryhGame->img)) {
                return ApiResponse::makeResponse(false, "首页封皮未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($mryhGame->rule_id)) {
                return ApiResponse::makeResponse(false, "活动详情未关联", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($mryhGame->type)) {
                return ApiResponse::makeResponse(false, "活动类型未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($mryhGame->join_price) || $mryhGame->join_price == 0) {
                return ApiResponse::makeResponse(false, "参与金额未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($mryhGame->target_join_day) || $mryhGame->target_join_day == 0) {
                return ApiResponse::makeResponse(false, "参与天数未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($mryhGame->show_start_time)) {
                return ApiResponse::makeResponse(false, "展示开始时间未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($mryhGame->show_end_time)) {
                return ApiResponse::makeResponse(false, "展示结束时间未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($mryhGame->join_start_time)) {
                return ApiResponse::makeResponse(false, "参与开始时间未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($mryhGame->join_end_time)) {
                return ApiResponse::makeResponse(false, "参与结束时间未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($mryhGame->game_start_time)) {
                return ApiResponse::makeResponse(false, "活动开始时间未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($mryhGame->game_end_time)) {
                return ApiResponse::makeResponse(false, "活动结束时间未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($mryhGame->share_title)) {
                return ApiResponse::makeResponse(false, "分享标题未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($mryhGame->share_img)) {
                return ApiResponse::makeResponse(false, "分享图片未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($mryhGame->share_desc)) {
                return ApiResponse::makeResponse(false, "分享描述未设置", ApiResponse::INNER_ERROR);
            }
        }

        //设置活动状态
        $mryhGame->status = $data['status'];
        $mryhGame->save();
        return ApiResponse::makeResponse(true, $mryhGame, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 复制活动
     *
     * By TerryQi
     *
     * 2018-07-24
     */
    public function copy(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, "没有活动id，请联系管理员处理", ApiResponse::INNER_ERROR);
        }

        $orgin_game = MryhGameManager::getById($data['id']);

        $new_game = new MryhGame();
        $new_game->name = $orgin_game->name . "-由" . $admin->name . "复制" . DateTool::getCurrentTime();
        $new_game->intro_html = $orgin_game->intro_html;
        $new_game->intro_text = $orgin_game->intro_text;
        $new_game->rule_id = $orgin_game->rule_id;
        $new_game->type = $orgin_game->type;
        $new_game->password = $orgin_game->password;
        $new_game->max_join_num = $orgin_game->max_join_num;
        $new_game->share_title = $orgin_game->share_title . "由" . $admin->name . "复制" . DateTool::getCurrentTime();
        $new_game->share_img = $orgin_game->share_img;
        $new_game->share_desc = $orgin_game->share_desc;
        $new_game->creator_type = "0";
        $new_game->creator_id = $admin->id;
        $new_game->save();

        return ApiResponse::makeResponse(true, $new_game, ApiResponse::SUCCESS_CODE);
    }

    /*
     * 编辑每天一画预设奖金
     *
     * By TerryQi
     *
     * 2018-12-18
     */
    public function editAdvPrice(Request $request)
    {
        $data = $request->all();
        //        dd($data);
        $admin = $request->session()->get('admin');
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数' . $requestValidationResult]);
        }
        $mryhGame = MryhGameManager::getById($data['id']);

        //发送验证码
        $code = Utils::getRandNum(4);

        $vertifyInfo = new Vertify();
        $vertifyInfo->code = $code;
        $vertifyInfo->phonenum = Utils::SUPER_ADMIN_PHONENUM;
        $vertifyInfo->save();
        //发送验证码
        SMSManager::sendSMS(Utils::SUPER_ADMIN_PHONENUM, Utils::SUPER_ADMIN_PHONENUM, $code . ',5分钟');

        return view('admin.mryh.mryhGame.editAdvPrice', ['admin' => $admin, 'data' => $mryhGame]);
    }


    /*
     * 修改每天一画预设奖金
     *
     * By TerryQi
     *
     * 2018-12-18
     */
    public function editAdvPricePost(Request $request)
    {
        $data = $request->all();
        //        dd($data);
        $admin = $request->session()->get('admin');
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
            'code' => 'required',
            'adv_price' => 'required'
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //校验code的完整性
        $con_arr = array(
            'phonenum' => Utils::SUPER_ADMIN_PHONENUM,
            'code' => $data['code'],
            'status' => '0'
        );
        $vertifyInfo = VertifyManager::getListByCon($con_arr, false)->first();
        if (!$vertifyInfo) {
            return ApiResponse::makeResponse(false, "校验码不正确", ApiResponse::INNER_ERROR);
        }
        $vertifyInfo->status = '1';
        $vertifyInfo->save();

        //每天一画活动
        $mryhGame = MryhGameManager::getById($data['id']);
        $mryhGame->adv_price = $data['adv_price'];
        $mryhGame->save();

        /////////////////////////////////////////////////////////
        ///
        //2018-12-18 此处有一个逻辑待补充，即要保存至t_opt_record中
        //
        //////////////////////////////////////////////////////////

        return ApiResponse::makeResponse(true, "修改预设奖金成功", ApiResponse::SUCCESS_CODE);
    }

}