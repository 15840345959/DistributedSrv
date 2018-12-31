<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\Vote;

use App\Components\AdminManager;
use App\Components\DateTool;
use App\Components\OptInfoManager;
use App\Components\OptRecordManager;
use App\Components\QNManager;
use App\Components\RequestValidator;
use App\Components\UserManager;
use App\Components\Utils;
use App\Components\Vote\VoteActivityManager;
use App\Components\Vote\VoteRuleManager;
use App\Components\Vote\VoteShareRecordManager;
use App\Components\Vote\VoteTeamManager;
use App\Components\Vote\VoteUserManager;
use App\Http\Controllers\ApiResponse;
use App\Models\OptInfo;
use App\Models\OptRecord;
use App\Models\Vote\VoteActivity;
use App\Models\Vote\VoteOrder;
use App\Models\Vote\VoteRecord;
use App\Models\Vote\VoteShareRecord;
use App\Models\Vote\VoteUser;
use function Couchbase\defaultDecoder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class VoteActivityController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        $id = null;
        $search_word = null;
        $apply_status = null;
        $vote_status = null;
        $valid_status = null;
        $c_admin_id1 = null;
        $vote_team_id = null;
        $status = null;
        $vote_end_at = null;

        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $id = $data['id'];
        }
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
        if (array_key_exists('vote_team_id', $data) && !Utils::isObjNull($data['vote_team_id'])) {
            $vote_team_id = $data['vote_team_id'];
        }
        if (array_key_exists('status', $data) && !Utils::isObjNull($data['status'])) {
            $status = $data['status'];
        }
        if (array_key_exists('vote_end_at', $data) && !Utils::isObjNull($data['vote_end_at'])) {
            $vote_end_at = $data['vote_end_at'];
        }

        $con_arr = array(
            'id' => $id,
            'search_word' => $search_word,
            'apply_status' => $apply_status,
            'vote_status' => $vote_status,
            'valid_status' => $valid_status,
            'c_admin_id1' => $c_admin_id1,
            'vote_team_id' => $vote_team_id,
            'vote_end_at' => $vote_end_at,
            'status' => $status
        );
        $voteActivities = VoteActivityManager::getListByCon($con_arr, true);
        foreach ($voteActivities as $voteActivity) {
            $voteActivity = VoteActivityManager::getInfoByLevel($voteActivity, '2');
        }

        //输出地推团队信息
        $vote_teams = VoteTeamManager::getListByCon([], false);

        return view('admin.vote.voteActivity.index', ['datas' => $voteActivities, 'con_arr' => $con_arr, 'vote_teams' => $vote_teams]);
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
        $voteActivity = new VoteActivity();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $voteActivity = VoteActivityManager::getById($data['id']);
            $voteActivity = VoteActivityManager::getInfoByLevel($voteActivity, '0123');
//            dd($voteActivity);
        }
        $voteActivity = VoteActivityManager::setInfo($voteActivity, $data);

        //生成负责人选项
        $c_admins = AdminManager::getListByCon(['status' => '1'], false);
        $vote_teams = VoteTeamManager::getListByCon(['status' => '1'], false);

        //生成大赛规则选项
        $rules = VoteRuleManager::getListByCon(['status' => '1'], false);

        return view('admin.vote.voteActivity.edit', ['admin' => $admin, 'data' => $voteActivity
            , 'upload_token' => $upload_token, 'item' => $item, 'c_admins' => $c_admins, 'vote_teams' => $vote_teams, 'rules' => $rules]);
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

        $voteActivity = VoteActivityManager::setInfo($voteActivity, $data);
        $voteActivity->admin_id = $admin->id;      //记录管理员id
        $voteActivity->save();

        return ApiResponse::makeResponse(true, $voteActivity, ApiResponse::SUCCESS_CODE);
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
        $admin = $request->session()->get('admin');
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, "没有大赛id，请联系管理员处理", ApiResponse::INNER_ERROR);
        }

        $orgin_activity = VoteActivityManager::getById($data['id']);

        $new_activity = new VoteActivity();
        $new_activity->name = $orgin_activity->name . "-由" . $admin->name . "复制" . DateTool::getCurrentTime();
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
        $new_activity->sel_tp_ad_url = $orgin_activity->sel_tp_ad_url;
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
        $new_activity->admin_id = $admin->id;
        $new_activity->c_admin_id1 = $orgin_activity->c_admin_id1;
        $new_activity->c_admin_id2 = $orgin_activity->c_admin_id2;
        $new_activity->vote_team_id = $orgin_activity->vote_team_id;

        $new_activity->save();

        return ApiResponse::makeResponse(true, $new_activity, ApiResponse::SUCCESS_CODE);
    }

    public function settle(Request $request)
    {
        $method = $request->method();

        $data = $request->all();

        $admin = $request->session()->get('admin');

        switch ($method) {
            case 'GET':
                $info = null;

                if (array_key_exists('id', $data)) {
                    $activity = VoteActivity::find(array_get($data, 'id'));

                    //操作记录
                    $con_arr = array(
                        'f_table' => "activity",
                        'f_id' => $activity->id
                    );
                    $optRecords = OptRecordManager::getListByCon($con_arr, false);
                    foreach ($optRecords as $optRecord) {
                        $optRecord = OptRecordManager::getInfoByLevel($optRecord, '0');
                    }
                    //操作记录
                    $con_arr = array(
                        "type" => Utils::OPT_TYPE_ACTIVITY
                    );
                    $optInfos = OptInfoManager::getListByCon($con_arr, false);

                    return view('admin.vote.voteActivity.settle', ['admin' => $admin, 'data' => $activity, 'optRecords' => $optRecords, 'optInfos' => $optInfos]);
                }
                break;
            case 'POST':
                DB::beginTransaction();
                try {
                    DB::commit();
                    return ApiResponse::makeResponse(true, '', ApiResponse::SUCCESS_CODE);
                } catch (\Exception $e) {
                    DB::rollback();
                    return ApiResponse::makeResponse(false, $e->getMessage(), $e->getCode());
                }
                break;
            default:
                break;
        }
    }

    /*
     * 导出获奖证书
     *
     * By leek
     *
     * 2018-12-06
     *
     */
    public function prizeStatementsWeb(Request $request)
    {
        $data = $request->all();

        $activity = VoteActivity::find(array_get($data, 'id'));

        $first_prize_num = $activity->first_prize_num;
        $second_prize_num = $activity->second_prize_num;
        $third_prize_num = $activity->third_prize_num;

//        $title = ['编号', '姓名', '奖项', '发证日期'];
//        $title = implode("\t", $title);
//
//        ob_get_clean();
//        ob_start();
//
//        echo iconv('utf-8', 'gbk', $title) . "\n";

        $prizeStatements = collect([]);

        $prize_user = VoteUser::where('activity_id', array_get($data, 'id'))
            ->where('status', '=', 1)
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

//            $rows = implode("\t", $row) . "\n";
//            Utils::processLog(__METHOD__, '', $rows);
//            echo iconv('utf-8', 'gbk', $rows);

            $prizeStatements->push($row);
        };

        $prizeStatements->all();

//        return response('')->header('Content-Disposition', 'attachment; filename=' . $activity->name . '.xls')
//            ->header('Accept-Ranges', 'bytes')
//            ->header('Content-Length', ob_get_length())
//            ->header('Content-Type', 'application/vnd.ms-excel;charset=utf-8');

        return view('admin.vote.voteActivity.prize', ['datas' => $prizeStatements, 'id' => array_get($data, 'id')]);

    }

    /*
     * 导出获奖证书
     *
     * By leek
     *
     * 2018-12-06
     *
     */
    public function prizeStatementsExcel(Request $request)
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
            ->where('status', '=', 1)
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


    /**
     * @param \App\Http\Controllers\Admin\Vote\Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function qrCode(Request $request, $id)
    {
        $data = $request->all();

        return view('admin.vote.voteActivity.qrcode', [
            'id' => $id
        ]);
    }


    /*
     * 综合信息
     *
     * By TerryQi
     *
     * 2018-12-07
     */
    public function info(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, "没有大赛id，请联系管理员处理", ApiResponse::INNER_ERROR);
        }
        //获取活动基本信息
        $voteActivity = VoteActivityManager::getById($data['id']);
        $voteActivity = VoteActivityManager::getInfoByLevel($voteActivity, '3');

        return view('admin.vote.voteActivity.info', ['data' => $voteActivity]);
    }


    /*
     * 订单统计数据-获取成功/失败订单统计
     *
     * By TerryQi
     *
     * 2018-12-07
     *
     */
    public function order(Request $request)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, "没有大赛id，请联系管理员处理", ApiResponse::INNER_ERROR);
        }
        $voteActivity_id = $data['id'];     //活动id

        $days_num = 7;     //默认是按周的活动
        $start_at = null;
        $end_at = Carbon::now()->addDay(1)->toDateString(); //获取到下一天，因为end_at时间为2018-11-28 00:00:00这种形式
        //如果存在days_num，代表传入了日期间隔
        if (array_key_exists('days_num', $data) && !Utils::isObjNull($data['days_num']) && is_numeric($data['days_num'])) {
            $days_num = intval($data['days_num']);
        }
        $start_at = Carbon::now()->subDay($days_num)->toDateString();

        //总订单数统计
        $order_total_num = VoteOrder::where('activity_id', '=', $voteActivity_id)->whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)->count();

        $voteOrders = VoteOrder::where('activity_id', '=', $voteActivity_id)
            ->whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)
            ->groupBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as value')
            ])
            ->toArray();

        $voteOrders = Utils::replZero($voteOrders, $start_at, $end_at);

        //支付成功订单数
        $pay_order_total_num = VoteOrder::where('activity_id', '=', $voteActivity_id)->where('pay_status', '=', '1')
            ->whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)->count();

        $pay_voteOrders = VoteOrder::where('activity_id', '=', $voteActivity_id)->where('pay_status', '=', '1')
            ->whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)
            ->groupBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as value')
            ])
            ->toArray();

        $pay_voteOrders = Utils::replZero($pay_voteOrders, $start_at, $end_at);

        $ret = [
            'order_arr' => $voteOrders,
            'order_total_num' => $order_total_num,
            'pay_order_arr' => $pay_voteOrders,
            'pay_order_total_num' => $pay_order_total_num,
        ];

        return ApiResponse::makeResponse(true, $ret, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 投票数金额数
     *
     * By TerryQi
     *
     * 2018-12-07
     *
     */
    public function vote_money(Request $request)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, "没有大赛id，请联系管理员处理", ApiResponse::INNER_ERROR);
        }
        $voteActivity_id = $data['id'];     //活动id

        $days_num = 7;     //默认是按周的活动
        $start_at = null;
        $end_at = Carbon::now()->addDay(1)->toDateString(); //获取到下一天，因为end_at时间为2018-11-28 00:00:00这种形式
        //如果存在days_num，代表传入了日期间隔
        if (array_key_exists('days_num', $data) && !Utils::isObjNull($data['days_num']) && is_numeric($data['days_num'])) {
            $days_num = intval($data['days_num']);
        }
        $start_at = Carbon::now()->subDay($days_num)->toDateString();

        //总投票数
        $vote_total_num = VoteRecord::where('activity_id', '=', $voteActivity_id)
            ->whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)->count();

        $voteRecords = VoteRecord::where('activity_id', '=', $voteActivity_id)
            ->whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)
            ->groupBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as value')
            ]);

        $voteRecords = Utils::replZero($voteRecords, $start_at, $end_at);

        //总订单金额
        $order_total_money = VoteOrder::where('activity_id', '=', $voteActivity_id)->where('pay_status', '=', '1')
            ->whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)->sum('total_fee');

        $pay_voteOrders = VoteOrder::where('activity_id', '=', $voteActivity_id)->where('pay_status', '=', '1')
            ->whereDate('created_at', '>=', $start_at)
            ->whereDate('created_at', '<=', $end_at)
            ->groupBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('sum(total_fee) as value')
            ])
            ->toArray();

        $pay_voteOrders = Utils::replZero($pay_voteOrders, $start_at, $end_at);

        $ret = [
            'vote_arr' => $voteRecords,
            'vote_total_num' => $vote_total_num,
            'order_arr' => $pay_voteOrders,
            'order_total_money' => $order_total_money,
        ];

        return ApiResponse::makeResponse(true, $ret, ApiResponse::SUCCESS_CODE);

    }

    /*
         * 分享明细
         *
         * By leek
         *
         * 2018-12-06
         *
         */
    public function shareIndex(Request $request)
    {
        $data = $request->all();

        //配置条件
        $activity_id = null;
        $user_id = null;
        $start_at = DateTool::dateAdd('D', -30, DateTool::getToday(), 'Y-m-d');
        $end_at = DateTool::dateAdd('D', 1, DateTool::getToday(), 'Y-m-d');

        if (array_key_exists('activity_id', $data) && !Utils::isObjNull($data['activity_id'])) {
            $activity_id = $data['activity_id'];
        }
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }

        if (array_key_exists('start_at', $data) && !Utils::isObjNull($data['start_at'])) {
            $start_at = $data['start_at'];
        }
        if (array_key_exists('end_at', $data) && !Utils::isObjNull($data['end_at'])) {
            $end_at = $data['end_at'];
        }
        $con_arr = array(
            'start_at' => $start_at,
            'end_at' => $end_at,
            'activity_id' => $activity_id,
            'user_id' => $user_id,
            'orderby' => [
                'user_id' => 'desc',
                'valid_status' => 'desc'
            ]
        );
        $vote_users = VoteUserManager::getListByCon($con_arr, false);
        foreach ($vote_users as $vote_user) {
            $vote_user = VoteUserManager::getInfoByLevel($vote_user, '01');
        }

        return view('admin.vote.voteActivity.share', ['datas' => $vote_users, 'activity_id' => array_get($data, 'activity_id'), 'con_arr' => $con_arr]);
    }
}