<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Vote\Team;

use App\Components\AdminManager;
use App\Components\DateTool;
use App\Components\OptInfoManager;
use App\Components\OptRecordManager;
use App\Components\QNManager;
use App\Components\RequestValidator;
use App\Components\Utils;
use App\Components\Vote\VoteActivityManager;
use App\Components\Vote\VoteGuanZhuManager;
use App\Components\Vote\VoteRecordManager;
use App\Components\Vote\VoteRuleManager;
use App\Components\Vote\VoteShareRecordManager;
use App\Components\Vote\VoteTeamManager;
use App\Components\Vote\VoteUserManager;
use App\Http\Controllers\ApiResponse;
use App\Models\OptInfo;
use App\Models\OptRecord;
use App\Models\Vote\VoteActivity;
use App\Models\Vote\VoteUser;
use function Couchbase\defaultDecoder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ActivityController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
        $team = $request->session()->get('team');
        $search_word = null;
        $apply_status = null;
        $vote_status = null;
        $valid_status = null;
        $c_admin_id1 = null;

        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }
        if (array_key_exists('apply_status', $data) && !Utils::isObjNull($data['apply_status'])) {
            $apply_status = $data['apply_status'];
        }
        if (array_key_exists('vote_status', $data) && !Utils::isObjNull($data['vote_status'])) {
            $vote_status = $data['vote_status'];
        }
        if (array_key_exists('valid_status', $data) && !Utils::isObjNull($data['valid_status'])) {
            $valid_status = $data['valid_status'];
        }
        if (array_key_exists('c_admin_id1', $data) && !Utils::isObjNull($data['c_admin_id1'])) {
            $c_admin_id1 = $data['c_admin_id1'];
        }
        $con_arr = array(
            'search_word' => $search_word,
            'apply_status' => $apply_status,
            'vote_status' => $vote_status,
            'valid_status' => $valid_status,
            'c_admin_id1' => $c_admin_id1,
            'vote_team_id' => $team->id
        );

        $voteActivities = VoteActivityManager::getListByCon($con_arr, true);
        foreach ($voteActivities as $voteActivity) {
            $voteActivity = VoteActivityManager::getInfoByLevel($voteActivity, '2');
        }

        return view('vote.team.activity.index', ['datas' => $voteActivities, 'con_arr' => $con_arr]);
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
        $team = $request->session()->get('team');
        $method = $request->method();
        $data = $request->all();

        switch ($method) {
            case 'GET':
                $item = 0;
                if (array_key_exists('item', $data)) {
                    $item = $data['item'];
                }
                $upload_token = QNManager::uploadToken();
                $voteActivity = new VoteActivity();
                if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
                    $voteActivity = VoteActivityManager::getById($data['id']);
                    $voteActivity = VoteActivityManager::getInfoByLevel($voteActivity, '0123');
                }
                $voteActivity = VoteActivityManager::setInfo($voteActivity, $data);
                $c_admins = AdminManager::getListByCon(['status' => '1'], false);
                $vote_teams = VoteTeamManager::getListByCon(['status' => '1'], false);
                $rules = VoteRuleManager::getListByCon(['status' => '1'], false);

                return view('vote.team.activity.edit', ['team' => $team, 'data' => $voteActivity, 'upload_token' => $upload_token, 'item' => $item, 'c_admins' => $c_admins, 'vote_teams' => $vote_teams, 'rules' => $rules]);
                break;
            case "POST":
                if (array_key_exists('code', $data) && !Utils::isObjNull($data['code'])) {
                    $con_arr = array(
                        'code' => $data['code']
                    );
                    $code_voteActivity = VoteActivityManager::getListByCon($con_arr, false)->first();       //获关键字重复的活动
                    //如果关键字可以检索出活动
                    if ($code_voteActivity) {
                        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
                            //带id为编辑活动，如果code非自己则报错
                            if ($code_voteActivity->id != $data['id']) {
                                return ApiResponse::makeResponse(false, "关键字重复", ApiResponse::INNER_ERROR);
                            }
                        } else {
                            return ApiResponse::makeResponse(false, "关键字重复", ApiResponse::INNER_ERROR);
                        }
                    }
                }
                $voteActivity = new VoteActivity();
                if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
                    $voteActivity = VoteActivityManager::getById($data['id']);
                }

                if (array_key_exists('apply_info_1', $data) && Utils::isObjNull(array_get($data, 'apply_info_1'))) {
                    $data['apply_info_1'] = '请输入我的宣言';
                }

                if (array_key_exists('apply_info_2', $data) && Utils::isObjNull(array_get($data, 'apply_info_2'))) {
                    $data['apply_info_2'] = '请输入作品名称';
                }

                if (array_key_exists('apply_info_3', $data) && Utils::isObjNull(array_get($data, 'apply_info_3'))) {
                    $data['apply_info_3'] = '请输入作品介绍';
                }

                $data['vote_team_id'] = $team->id;

                $voteActivity = VoteActivityManager::setInfo($voteActivity, $data);
//                $voteActivity->admin_id = $admin->id;      //记录管理员id
                $voteActivity->save();

                return ApiResponse::makeResponse(true, $voteActivity, ApiResponse::SUCCESS_CODE);
                break;
        }
    }


    //设置大赛状态
    public function setActivityStatus(Request $request)
    {
        $data = $request->all();

        $id = array_get($data, 'id');

        if (is_numeric($id) !== true) {
            return redirect()->route('team.error.500', ['msg' => '合规校验失败，请检查参数礼品id$id']);
        }
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'status' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->route('team.error.500', ['msg' => '合规校验失败，请检查参数' . $requestValidationResult]);
        }
        $voteActivity = VoteActivityManager::getById($id);
        //如果是启动服务，判断大赛配置项目的合法性
        if ($data['status'] == '1') {
            if (Utils::isObjNull($voteActivity->name)) {
                return ApiResponse::makeResponse(false, "大赛名称未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($voteActivity->img)) {
                return ApiResponse::makeResponse(false, "首页封皮未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($voteActivity->rule_id)) {
                return ApiResponse::makeResponse(false, "活动详情未关联", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($voteActivity->gift_html)) {
                return ApiResponse::makeResponse(false, "奖品介绍未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($voteActivity->apply_html)) {
                return ApiResponse::makeResponse(false, "参赛说明未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($voteActivity->index_ad_img)) {
                return ApiResponse::makeResponse(false, "首页广告图未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($voteActivity->sel_gift_ids)) {
                return ApiResponse::makeResponse(false, "投票礼品未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($voteActivity->sel_tp_ad_ids)) {
                return ApiResponse::makeResponse(false, "投票成功页面未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($voteActivity->apply_start_time)) {
                return ApiResponse::makeResponse(false, "报名开始时间未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($voteActivity->apply_end_time)) {
                return ApiResponse::makeResponse(false, "报名结束时间未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($voteActivity->vote_start_time)) {
                return ApiResponse::makeResponse(false, "投票开始时间未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($voteActivity->vote_end_time)) {
                return ApiResponse::makeResponse(false, "投票结束时间未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($voteActivity->share_title)) {
                return ApiResponse::makeResponse(false, "分享标题未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($voteActivity->share_img)) {
                return ApiResponse::makeResponse(false, "分享图片未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($voteActivity->share_desc)) {
                return ApiResponse::makeResponse(false, "分享描述未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($voteActivity->first_prize_num)) {
                return ApiResponse::makeResponse(false, "一等奖数量未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($voteActivity->second_prize_num)) {
                return ApiResponse::makeResponse(false, "二等奖数量未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($voteActivity->third_prize_num)) {
                return ApiResponse::makeResponse(false, "三等奖数量未设置", ApiResponse::INNER_ERROR);
            }
        }

        //设置大赛状态
        $voteActivity->status = $data['status'];
        $voteActivity->save();
        return ApiResponse::makeResponse(true, $voteActivity, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 复制大赛
     *
     * By TerryQi
     *
     * 2018-07-24
     */
    public function copy(Request $request)
    {
        $data = $request->all();
        $team = $request->session()->get('team');
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, "没有大赛id，请联系管理员处理", ApiResponse::INNER_ERROR);
        }

        $orgin_activity = VoteActivityManager::getById($data['id']);

        $new_activity = new VoteActivity();
        $new_activity->name = $orgin_activity->name . "-由" . $team->name . "复制" . DateTool::getCurrentTime();
        $new_activity->music = $orgin_activity->music;
        $new_activity->video = $orgin_activity->video;
        $new_activity->gzh_ewm = $orgin_activity->gzh_ewm;
        $new_activity->notice_text = $orgin_activity->notice_text;
        $new_activity->notice_url = $orgin_activity->notice_url;
        $new_activity->intro = $orgin_activity->intro;
        $new_activity->rule_id = $orgin_activity->rule_id;
        $new_activity->present_rule_id = $orgin_activity->present_rule_id;
        $new_activity->jg_intro_html = $orgin_activity->jg_intro_html;
        $new_activity->gift_html = $orgin_activity->gift_html;
        $new_activity->apply_html = $orgin_activity->apply_html;
        $new_activity->sel_gift_ids = $orgin_activity->sel_gift_ids;
        $new_activity->sel_index_ad_ids = $orgin_activity->sel_index_ad_ids;
        $new_activity->sel_pm_ad_ids = $orgin_activity->sel_pm_ad_ids;
        $new_activity->sel_tp_ad_ids = $orgin_activity->sel_tp_ad_ids;
        $new_activity->show_ad_mode = $orgin_activity->show_ad_mode;
        $new_activity->apply_start_time = $orgin_activity->apply_start_time;
        $new_activity->apply_end_time = $orgin_activity->apply_end_time;
        $new_activity->vote_start_time = $orgin_activity->vote_start_time;
        $new_activity->vote_end_time = $orgin_activity->vote_end_time;
        $new_activity->vote_mode = $orgin_activity->vote_mode;
        $new_activity->subscribe_mode = $orgin_activity->subscribe_mode;
        $new_activity->vote_audit_mode = $orgin_activity->vote_audit_mode;
        $new_activity->daily_vote_to_user_num = $orgin_activity->daily_vote_to_user_num;
        $new_activity->daily_vote_num = $orgin_activity->daily_vote_num;
        $new_activity->vote_notice_mode = $orgin_activity->vote_notice_mode;
        $new_activity->gift_notice_mode = $orgin_activity->gift_notice_mode;
        $new_activity->lock_num_mode = $orgin_activity->lock_num_mode;
        $new_activity->vote_vertify_mode = $orgin_activity->vote_vertify_mode;
        $new_activity->apply_min_num = $orgin_activity->apply_min_num;
        $new_activity->vote_message_mode = $orgin_activity->vote_message_mode;
        $new_activity->share_title = $orgin_activity->share_title;
        $new_activity->share_img = $orgin_activity->share_img;
        $new_activity->share_desc = $orgin_activity->share_desc;
        $new_activity->first_prize_num = $orgin_activity->first_prize_num;
        $new_activity->second_prize_num = $orgin_activity->second_prize_num;
        $new_activity->third_prize_num = $orgin_activity->third_prize_num;
        $new_activity->honor_prize_num = $orgin_activity->honor_prize_num;
//        $new_activity->admin_id = $admin->id;
        $new_activity->c_admin_id1 = $orgin_activity->c_admin_id1;
        $new_activity->c_admin_id2 = $orgin_activity->c_admin_id2;
        $new_activity->vote_team_id = $orgin_activity->vote_team_id;

        $new_activity->save();

        return ApiResponse::makeResponse(true, $new_activity, ApiResponse::SUCCESS_CODE);
    }


    public function prizeStatements(Request $request)
    {
        $data = $request->all();

        $activity = VoteActivity::find(array_get($data, 'id'));

        $first_prize_num = $activity->first_prize_num;
        $second_prize_num = $activity->second_prize_num;
        $third_prize_num = $activity->third_prize_num;

        $title = ['编号', '姓名', '奖项', '发证日期'];
        $title = implode("\t", $title);

        ob_get_clean();
        ob_start();

        echo iconv('utf-8', 'gbk', $title) . "\n";

        $prize_user = VoteUser::where('activity_id', array_get($data, 'id'))
            ->orderby('vote_num', 'desc')
            ->get();

        foreach ($prize_user as $index => $item) {
            $index = $index + 1;
            Utils::processLog(__METHOD__, '', $index);
            Utils::processLog(__METHOD__, '', json_encode($item->toArray()));
            $prize = null;
            if ($index <= $first_prize_num) {
                Utils::processLog(__METHOD__, '', '金奖');
                $prize = '金奖';
            } else if ($index <= $first_prize_num + $second_prize_num) {
                Utils::processLog(__METHOD__, '', '银奖');
                $prize = '银奖';
            } else if ($index <= $first_prize_num + $second_prize_num + $third_prize_num) {
                Utils::processLog(__METHOD__, '', '铜奖');
                $prize = '铜奖';
            } else if ($item->vote_num >= 500) {
                Utils::processLog(__METHOD__, '', '优秀奖');
                $prize = '优秀奖';
            } else {
                continue;
            }

            $row = array();
            $row['code'] = $activity->code . '-' . $item->code;
            $row['name'] = $item->name ? explode(" ", $item->name)[0] : '';
//            $row['phonenum'] = $item->phonenum;
            $row['prize'] = $prize ? $prize : ' ';
            $row['time'] = substr($activity->vote_end_time, 0, 10);

            $rows = implode("\t", $row) . "\n";
            Utils::processLog(__METHOD__, '', $rows);
            echo iconv('utf-8', 'gbk', $rows);
        };

        return response('')->header('Content-Disposition', 'attachment; filename=' . $activity->name . '.xls')
            ->header('Accept-Ranges', 'bytes')
            ->header('Content-Length', ob_get_length())
            ->header('Content-Type', 'application/vnd.ms-excel;charset=utf-8');
    }

    /*
     * 添加、编辑投票选手
     *
     * By Leek
     *
     * 2018-4-9
     *
     */
    public function editVoteUser(Request $request)
    {
        $methods = $request->method();

        $data = $request->all();

        $team = $request->session()->get('team');
        switch ($methods) {
            case 'GET':
                $upload_token = QNManager::uploadToken();

                $voteUser = new VoteUser();
                if (array_key_exists('id', $data)) {
                    $voteUser = VoteUserManager::getById($data['id']);
                } else {
                    //如果是新建用户其中大赛的id必传
                    $requestValidationResult = RequestValidator::validator($request->all(), [
                        'activity_id' => 'required',
                    ]);
                    if ($requestValidationResult !== true) {
                        return redirect()->route('team.error.500', ['msg' => '合规校验失败，请检查参数' . $requestValidationResult]);
                    }
                }
                $voteUser = VoteUserManager::setInfo($voteUser, $data);
                return view('vote.team.voteUser.edit', ['team' => $team, 'data' => $voteUser, 'upload_token' => $upload_token]);
                break;
            case 'POST':
                $voteUser = null;
                if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
                    $voteUser = VoteUserManager::getById($data['id']);
                    $voteUser = VoteUserManager::setInfo($voteUser, $data);
                } else {
                    $voteUser = new VoteUser();
//                    $voteUser->admin_id = $admin->id;      //记录管理员id
                    //从系统导入的参赛选手自动设置为生效+审核通过
                    $voteUser->audit_status = '1';
                    $voteUser->status = '1';
                    $voteUser = VoteUserManager::setInfo($voteUser, $data);
                    $voteUser->save();
                    //如果是新建用户-增加活动统计记录
                    VoteActivityManager::addStatistics($voteUser->activity_id, 'join_num', 1);
                }
                $voteUser->save();
                //记录编号
                VoteUserManager::setCode($voteUser);
                return ApiResponse::makeResponse(true, $voteUser, ApiResponse::SUCCESS_CODE);
                break;
        }
    }


    /*
     * 导入参赛选手
     *
     * By Leek
     *
     * 2018-07-31
     */
    public function importVoteUser(Request $request)
    {
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'activity_id' => 'required',
        ]);

        if ($requestValidationResult !== true) {
            return redirect()->route('team.error.500', ['msg' => '合规校验失败，请检查参数大赛id']);
        }

        $methods = $request->method();

        $data = $request->all();

//        dd($data);

        $team = $request->session()->get('team');

        switch ($methods) {
            case 'GET':
                //获取大赛信息
                $voteActivity = new VoteActivity();
                if (array_key_exists('activity_id', $data) && !Utils::isObjNull($data['activity_id'])) {
                    $voteActivity = VoteActivityManager::getById($data['activity_id']);
                }
                //生成七牛token
                $upload_token = QNManager::uploadToken();
                return view('vote.team.activity.importVoteUser', ['team' => $team, 'data' => $voteActivity, 'upload_token' => $upload_token]);
                break;
            case 'POST':
                $activity_id = $data['activity_id'];
                //业务数据
                $name_arr = [];
                $img_arr = [];
                $video_arr = [];
                //加载业务数据
                if (array_key_exists('name', $data)) {
                    $name_arr = $data['name'];
                }
                if (array_key_exists('img', $data)) {
                    $img_arr = $data['img'];
                }
                if (array_key_exists('video', $data)) {
                    $video_arr = $data['video'];
                }
                //如果没有作者信息
                if (count($name_arr) == 0) {
                    return ApiResponse::makeResponse(false, "未传入选手姓名", ApiResponse::INNER_ERROR);
                }
                //如果数组长度不同
                if (count($img_arr) > 0) {
                    if (count($name_arr) != count($img_arr)) {
                        return ApiResponse::makeResponse(false, "选手与作品数不匹配", ApiResponse::INNER_ERROR);
                    }
                }
                if (count($video_arr) > 0) {
                    if (count($name_arr) != count($video_arr)) {
                        return ApiResponse::makeResponse(false, "选手与作品数不匹配", ApiResponse::INNER_ERROR);
                    }
                }

                for ($i = 0; $i < count($name_arr); $i++) {
                    $voteUser = new VoteUser();
                    $data_obj = array(
                        'activity_id' => $activity_id,
                    );
                    $data_obj['activity_id'] = $activity_id;
                    $data_obj['name'] = $name_arr[$i];
                    if (count($img_arr) > 0) {
                        $data_obj['img'] = $img_arr[$i];
                    }
                    if (count($video_arr) > 0) {
                        $data_obj['video'] = $video_arr[$i];
                    }
//            dd($data_obj);
                    $voteUser = VoteUserManager::setInfo($voteUser, $data_obj);
//                    $voteUser->admin_id = $admin->id;      //记录管理员id
                    //从系统导入的参赛选手自动设置为生效+审核通过
                    $voteUser->audit_status = '1';
                    $voteUser->status = '1';
                    $voteUser->save();
                    //记录编号
                    VoteUserManager::setCode($voteUser);
                    VoteActivityManager::addStatistics($activity_id, 'join_num', 1);
                }
                return ApiResponse::makeResponse(true, "批量导入成功", ApiResponse::SUCCESS_CODE);
                break;
        }


    }

    /*
    * 导入参赛选手-视频类
    *
    * By TerryQi
    *
    * 2018-08-25
    */
    public function importVoteUserVideo(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $team = $request->session()->get('team');
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'activity_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->route('team.error.500', ['msg' => '合规校验失败，请检查参数大赛id']);
        }
        //获取大赛信息
        $voteActivity = new VoteActivity();
        if (array_key_exists('activity_id', $data) && !Utils::isObjNull($data['activity_id'])) {
            $voteActivity = VoteActivityManager::getById($data['activity_id']);
        }

        //生成七牛token
        $upload_token = QNManager::uploadToken();
        return view('vote.team.activity.importVoteUserVideo', ['team' => $team, 'data' => $voteActivity, 'upload_token' => $upload_token]);
    }


    //设置选手状态
    public function setVoteUserStatus(Request $request)
    {
        $data = $request->all();
        $id = array_get($data, 'id');
        if (is_numeric($id) !== true) {
            return redirect()->route('team.error.500', ['msg' => '合规校验失败，请检查参数选手id$id']);
        }
        $voteUser = VoteUserManager::getById($id);
        $voteUser->status = $data['status'];
        $voteUser->save();
        return ApiResponse::makeResponse(true, $voteUser, ApiResponse::SUCCESS_CODE);
    }

    //审核选手信息
    public function setVoteUserAuditStatus(Request $request)
    {
        $data = $request->all();

        $id = array_get($data, 'id');
        if (is_numeric($id) !== true) {
            return redirect()->route('team.error.500', ['msg' => '合规校验失败，请检查参数选手id$id']);
        }
        $voteUser = VoteUserManager::getById($id);

        $voteUser->audit_status = array_get($data, 'audit_status');
        $voteUser->save();
        return ApiResponse::makeResponse(true, $voteUser, ApiResponse::SUCCESS_CODE);
    }

    /*
     * 投票用户详细信息
     *
     * By TerryQi
     *
     * 2018-07-22
     *
     */
    public function voteUserInfo(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $team = $request->session()->get('team');
        //如果是新建用户其中大赛的id必传
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->route('team.error.500', ['msg' => '合规校验失败，请检查参数' . $requestValidationResult]);
        }
        $vote_user = VoteUserManager::getById($data['id']);
        $vote_user = VoteUserManager::getInfoByLevel($vote_user, '012');

        return view('vote.team.voteUser.info', ['team' => $team, 'data' => $vote_user]);
    }

    public function voteRecord(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $team = $request->session()->get('team');
        //搜索条件
        $vote_user_id = null;
        $user_id = null;
        if (array_key_exists('vote_user_id', $data) && !Utils::isObjNull($data['vote_user_id'])) {
            $vote_user_id = $data['vote_user_id'];
        }
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }
        $con_arr = array(
            'vote_user_id' => $vote_user_id,
            'user_id' => $user_id
        );
        $vote_records = VoteRecordManager::getListByCon($con_arr, true);
        foreach ($vote_records as $vote_record) {
            $vote_record = VoteRecordManager::getInfoByLevel($vote_record, '12');
        }
//        dd($vote_records);
        return view('vote.team.voteRecord.index', ['datas' => $vote_records, 'con_arr' => $con_arr]);
    }

    public function voteShareRecord(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $team = $request->session()->get('team');
        //搜索条件
        $vote_user_id = null;
        $user_id = null;
        if (array_key_exists('vote_user_id', $data) && !Utils::isObjNull($data['vote_user_id'])) {
            $vote_user_id = $data['vote_user_id'];
        }
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }
        $con_arr = array(
            'vote_user_id' => $vote_user_id,
            'user_id' => $user_id
        );
        $vote_share_records = VoteShareRecordManager::getListByCon($con_arr, true);
//        dd($vote_share_records);
        foreach ($vote_share_records as $vote_share_record) {
            $vote_share_record = VoteShareRecordManager::getInfoByLevel($vote_share_record, '01');
        }
//        dd($vote_share_records);
        return view('vote.team.voteShareRecord.index', ['datas' => $vote_share_records, 'con_arr' => $con_arr]);
    }

    public function VoteFollowRecord(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $team = $request->session()->get('team');
        //搜索条件
        $vote_user_id = null;
        $user_id = null;
        if (array_key_exists('vote_user_id', $data) && !Utils::isObjNull($data['vote_user_id'])) {
            $vote_user_id = $data['vote_user_id'];
        }
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }
        $con_arr = array(
            'vote_user_id' => $vote_user_id,
            'user_id' => $user_id
        );
        $vote_guanzhus = VoteGuanZhuManager::getListByCon($con_arr, true);
        foreach ($vote_guanzhus as $vote_guanzhu) {
            $vote_guanzhu = VoteGuanZhuManager::getInfoByLevel($vote_guanzhu, '01');
        }
//        dd($vote_guanzhus);
        return view('vote.team.voteFollowRecord.index', ['datas' => $vote_guanzhus, 'con_arr' => $con_arr]);
    }
}