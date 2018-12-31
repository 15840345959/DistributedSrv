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
use App\Components\Mryh\MryhADManager;
use App\Components\Mryh\MryhFriendManager;
use App\Components\Mryh\MryhGameManager;
use App\Components\Mryh\MryhJoinArticleManager;
use App\Components\Mryh\MryhJoinManager;
use App\Components\Mryh\MryhSettingManager;
use App\Components\Mryh\MryhUserCouponManager;
use App\Components\Mryh\MryhUserManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Mryh\MryhJoin;
use App\Models\Mryh\MryhJoinArticle;
use App\Models\Mryh\MryhUser;
use Illuminate\Http\Request;
use Yansongda\Pay\Pay;

class MryhPageController
{
    /*
     * 获取每天一画小程序首页信息
     *
     * By TerryQi
     *
     * 2018-08-23
     */
    public function index(Request $request)
    {
        $data = $request->all();
        $user_id = null;
        //如果data中有user_id信息，则赋予user_id值
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }
        //获取轮播广告信息
        $mryhADs = MryhADManager::getListByCon(['status' => '1'], false);
        //去除描述html
        foreach ($mryhADs as $mryhAD) {
            unset($mryhAD->content_html);
        }

        //获取业务配置信息
        $mryhSetting = MryhSettingManager::getListByCon(['status' => '1'], false)->first();

        //完成用户获取
        /*
         * 优化业务，数据格式变化
         *
         * By TerryQi
         *
         */
        $mryhUsers_user_id_arr = MryhUser::take(50)->pluck('user_id');
        $users = UserManager::getListByCon(['id_arr' => $mryhUsers_user_id_arr, 'avatar_not_null' => true, 'page_size' => 5], true);

        //获取每天一画总共的参与用户数
        $mryh_total_user = MryhUserManager::getListByCon([], false)->count();
        //每天一画总金额

        $mryh_total_money = MryhGameManager::getListByCon(['status' => '1'], false)->sum('total_money')
            + MryhGameManager::getListByCon(['status' => '1'], false)->sum('adv_price');

        $mryh_total_money = round($mryh_total_money, 2);        //整体金额

        //如果有用户信息，则需要返回用户正在参加的大赛
        $mryhJoins = [];
        if (!Utils::isObjNull($user_id)) {
            $con_arr = array(
                'user_id' => $user_id,
                'game_status' => '0'
            );
            $mryhJoins = MryhJoinManager::getListByCon($con_arr, false);
            foreach ($mryhJoins as $mryhJoin) {
                $mryhJoin = MryhJoinManager::getInfoByLevel($mryhJoin, '0');
            }
        }

        //除了我参与的活动的其他活动
        $otherGames = MryhGameManager::getRecommendGames($user_id, [], 5);

        $articles = MryhJoinArticleManager::getListByCon(['page_size' => 3], true);
        foreach ($articles as $article) {
            $article = MryhJoinArticleManager::getInfoByLevel($article, '13');
        }
        //配置首页数据集合
        $page_data = array(
            'ads' => $mryhADs,
            'total_user' => $mryh_total_user,
            'total_money' => $mryh_total_money,
            'setting' => $mryhSetting,
            'users' => $users,
            'joins' => $mryhJoins,
            'other_games' => $otherGames,
            'articles' => $articles
        );
        return ApiResponse::makeResponse(true, $page_data, ApiResponse::SUCCESS_CODE);
    }

    /*
     * 获取活动页面接口
     *
     * By TerryQi
     *
     * 2018-08-23
     */
    public function game(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $mryhGame = MryhGameManager::getById($data['id']);
        if (!$mryhGame) {
            return ApiResponse::makeResponse(false, "未找到活动", ApiResponse::INNER_ERROR);
        }
        //配置信息
        $mryhGame = MryhGameManager::getInfoByLevel($mryhGame, '');

        //是否传入user_id
        $user_id = null;
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }

        //是否参与标识
        $mryhJoin = MryhJoinManager::getListByCon(['game_id' => $mryhGame->id, 'user_id' => $user_id], false)->first();
        if ($mryhJoin) {
            $mryhGame->join_flag = true;
            $mryhGame->join = $mryhJoin;
        } else {
            $mryhGame->join_flag = false;
            $mryhGame->join = null;
        }

        //活动最近15个参与记录
        $mryhJoins = MryhJoinManager::getListByCon(['game_id' => $mryhGame->id, 'page_size' => 8], true);
        foreach ($mryhJoins as $mryhJoin) {
            $mryhJoin = MryhJoinManager::getInfoByLevel($mryhJoin, '1');
            //2018-12-28增加逻辑,解决mryhJoins中有user头像为空的问题
            if (Utils::isObjNull($mryhJoin->user->avatar)) {
                Utils::processLog(__METHOD__, '', '解决mryhJoins中有user头像为空的问题 mryhJoin：' . json_encode($mryhJoin));
                $mryhJoin->user->avatar = UserManager::getRandomAvatar();        //替换头像，注意此处不能保存
            }
        }

        //是否有免费参与活动的资格
        $con_arr = array(
            'user_id' => $user_id,
            'used_status' => '0',       //未使用
            'mode' => '0'       //定向
        );
        $mryhUserCoupon = MryhUserCouponManager::getListByCon($con_arr, false)->first();

        //当前正在进行的其他活动，可以参与，是用户没有参与过的活动
        $other_mryhGames = MryhGameManager::getRecommendGames($user_id, [$mryhGame->id], 5);

        $page_data = array(
            'game' => $mryhGame,
            'joins' => $mryhJoins,
            'coupon' => $mryhUserCoupon,
            'other_games' => $other_mryhGames,
        );

        return ApiResponse::makeResponse(true, $page_data, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 获取参与页面接口
     *
     * By TerryQi
     *
     * 2018-08-23
     */
    public function join(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }

        $mryhJoin = MryhJoinManager::getById($data['id']);
        if (!$mryhJoin) {
            return ApiResponse::makeResponse(false, "未找到活动参与记录", ApiResponse::INNER_ERROR);
        }

        $mryhJoin = MryhJoinManager::getInfoByLevel($mryhJoin, '0');

        //获取朋友信息，二级朋友列表-分页
        $mryhFriends = MryhFriendManager::getListByConJoinGameLeve2($mryhJoin->user_id, true);
        Utils::processLog(__METHOD__, '', '二级朋友列表 mryhFriends：' . json_encode($mryhFriends));

        //我的作品列表
        $con_arr = array(
            'join_id' => $mryhJoin->id
        );
        $my_mryhJoinArticles = MryhJoinArticleManager::getListByCon($con_arr, false);
        foreach ($my_mryhJoinArticles as $mryhJoinArticle) {
            $mryhJoinArticle = MryhJoinArticleManager::getInfoByLevel($mryhJoinArticle, '3');
        }
        Utils::processLog(__METHOD__, '', '我的作品信息 mryhJoinArticle：' . json_encode($my_mryhJoinArticles));

        //获取其他人作品列表
        $con_arr = array(
            'game_id' => $mryhJoin->game_id,
        );
        $other_mryhJoinArticles = MryhJoinArticleManager::getListByCon($con_arr, false);
        foreach ($other_mryhJoinArticles as $other_mryhJoinArticle) {
            $other_mryhJoinArticle = MryhJoinArticleManager::getInfoByLevel($other_mryhJoinArticle, '13');
        }
        Utils::processLog(__METHOD__, '', '其他人的作品信息 other_mryhJoinArticles：' . json_encode($other_mryhJoinArticles));
        $page_data = array(
            'join' => $mryhJoin,
            'friends' => $mryhFriends,
            'my_articles' => $my_mryhJoinArticles,
            'other_articles' => $other_mryhJoinArticles,
        );
        return ApiResponse::makeResponse(true, $page_data, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 2018-12-26 获取参赛页面接口，升级-主要解决other_articles、friends、my_articles相关
     *
     * other_articles改为分页，好处是减少数据量，如果需要获取更多数据，则传入game_id，调用 api/mryh/joinArticle/getListByCon 接口
     *
     * friends参与该活动的最近15个用户
     *
     * my_articles改为分页，且最多输出30个作品，因为后续有每年一画，避免数据量太大，如果需要获取更多数据，则传入user_id，game_id，调用 api/mryh/joinArticle/getListByCon 接口
     *
     */
    public function v2_join(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }

        $mryhJoin = MryhJoinManager::getById($data['id']);
        if (!$mryhJoin) {
            return ApiResponse::makeResponse(false, "未找到活动参与记录", ApiResponse::INNER_ERROR);
        }

        $mryhJoin = MryhJoinManager::getInfoByLevel($mryhJoin, '0');

        //获得朋友信息
        $friend_user_id_arr = MryhJoinManager::getListByCon(['game_id' => $mryhJoin->game_id], true)->pluck('user_id');     //此处已经分页
        $mryhFriends = UserManager::getListByCon(['id_arr' => $friend_user_id_arr], false);

        //我的作品列表
        $con_arr = array(
            'join_id' => $mryhJoin->id,
            'page_size' => 31           //做多31个作品
        );
        $my_mryhJoinArticles = MryhJoinArticleManager::getListByCon($con_arr, true);
        foreach ($my_mryhJoinArticles as $mryhJoinArticle) {
            $mryhJoinArticle = MryhJoinArticleManager::getInfoByLevel($mryhJoinArticle, '3');
        }
        Utils::processLog(__METHOD__, '', '我的作品信息 mryhJoinArticle：' . json_encode($my_mryhJoinArticles));

        //获取其他人作品列表
        $con_arr = array(
            'game_id' => $mryhJoin->game_id,
        );
        $other_mryhJoinArticles = MryhJoinArticleManager::getListByCon($con_arr, true);
        foreach ($other_mryhJoinArticles as $other_mryhJoinArticle) {
            $other_mryhJoinArticle = MryhJoinArticleManager::getInfoByLevel($other_mryhJoinArticle, '13');
        }
        Utils::processLog(__METHOD__, '', '其他人的作品信息 other_mryhJoinArticles：' . json_encode($other_mryhJoinArticles));
        $page_data = array(
            'join' => $mryhJoin,
            'friends' => $mryhFriends,
            'my_articles' => $my_mryhJoinArticles,
            'other_articles' => $other_mryhJoinArticles,
        );

        return ApiResponse::makeResponse(true, $page_data, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 获取我的首页
     *
     * By TerryQi
     *
     * 2018-08-27
     */
    public function my(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //如果存在每天一画用户参与信息
        $mryhUser = MryhUserManager::getByUserId($data['user_id']);
        if (!$mryhUser) {
            return ApiResponse::makeResponse(false, "未找到用户信息", ApiResponse::INNER_ERROR);
        }
        $mryhUser = MryhUserManager::getInfoByLevel($mryhUser, '');

        //好友信息
        $mryhFriends = MryhFriendManager::getListByConJoinGameLeve2($mryhUser->user_id, true);

        //正在参赛的信息
        $con_arr = array(
            'game_status' => '0',
            'user_id' => $mryhUser->user_id
        );
        $mryhJoins = MryhJoinManager::getListByCon($con_arr, false);

        foreach ($mryhJoins as $mryhJoin) {
            $mryhJoin = MryhJoinManager::getInfoByLevel($mryhJoin, '02');
        }

        //参与次数
        $join_num = MryhJoinManager::getListByCon(['user_id' => $mryhUser->user_id], false)->count();
        //作品数
        $work_num = MryhJoinArticleManager::getListByCon(['user_id' => $mryhUser->user_id], false)->count();

        //带结算奖金
        $waitingJiesuan_prize = MryhJoin::where('user_id', '=', $mryhUser->user_id)->where('jiesuan_status', '=', '0')->sum('jiesuan_price');
        $waitingJiesuan_prize = round($waitingJiesuan_prize, 2);
        //总奖金
        $total_prize = MryhJoin::where('user_id', '=', $mryhUser->user_id)->sum('jiesuan_price');
        $total_prize = round($total_prize, 2);

        $page_data = array(
            'user' => $mryhUser,
            'join_num' => $join_num,
            'work_num' => $work_num,
            'waitingJiesuan_prize' => $waitingJiesuan_prize,
            'total_prize' => $total_prize,
            'friends' => $mryhFriends,
            'joins' => $mryhJoins
        );
        return ApiResponse::makeResponse(true, $page_data, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 获取用户主页
     *
     * By TerryQi
     *
     * 2018-08-27
     */
    public function person(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'p_user_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }

        //如果存在每天一画用户参与信息
        $p_mryhUser = MryhUserManager::getByUserId($data['p_user_id']);
        if (!$p_mryhUser) {
            return ApiResponse::makeResponse(false, "未找到用户信息", ApiResponse::INNER_ERROR);
        }
        $p_mryhUser = MryhUserManager::getInfoByLevel($p_mryhUser, '');

        //用户参与记录
        $con_arr = array(
            'user_id' => $p_mryhUser->user_id
        );
        $mryhJoins = MryhJoinManager::getListByCon($con_arr, false);
        foreach ($mryhJoins as $mryhJoin) {
            $mryhJoin = MryhJoinManager::getInfoByLevel($mryhJoin, '0');
        }

        //用户作品列表
        $mryhJoinArticles = MryhJoinArticleManager::getListByCon(['user_id' => $p_mryhUser->user_id], true);
        foreach ($mryhJoinArticles as $mryhJoinArticle) {
            $mryhJoinArticle = MryhJoinArticleManager::getInfoByLevel($mryhJoinArticle, '23');
        }

        //参与次数
        $join_num = MryhJoinManager::getListByCon(['user_id' => $p_mryhUser->user_id], false)->count();
        //作品数
        $work_num = MryhJoinArticleManager::getListByCon(['user_id' => $p_mryhUser->user_id], false)->count();
        //总奖金
        $total_prize = MryhJoin::where('user_id', '=', $p_mryhUser->user_id)->sum('jiesuan_price');

        $page_data = array(
            'user' => $p_mryhUser,
            'join_num' => $join_num,
            'work_num' => $work_num,
            'total_prize' => $total_prize,
            'joins' => $mryhJoins,
            'articles' => $mryhJoinArticles
        );

        return ApiResponse::makeResponse(true, $page_data, ApiResponse::SUCCESS_CODE);
    }

}





