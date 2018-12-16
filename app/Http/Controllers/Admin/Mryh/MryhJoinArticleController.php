<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\Mryh;

use App\Components\Mryh\MryhJoinArticleManager;
use App\Components\Mryh\MryhJoinManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\Vote\VoteUser;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class MryhJoinArticleController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        $game_id = null;
        $user_id = null;
        $join_id = null;

        if (array_key_exists('game_id', $data) && !Utils::isObjNull($data['game_id'])) {
            $game_id = $data['game_id'];
        }
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }
        if (array_key_exists('join_id', $data) && !Utils::isObjNull($data['join_id'])) {
            $join_id = $data['join_id'];
        }
        $con_arr = array(
            'game_id' => $game_id,
            'user_id' => $user_id,
            'join_id' => $join_id
        );

        $mryhJoinArticles = MryhJoinArticleManager::getListByCon($con_arr, true);
//        dd($mryhJoinArticles);
        foreach ($mryhJoinArticles as $mryhJoinArticle) {
            $mryhJoinArticle = MryhJoinArticleManager::getInfoByLevel($mryhJoinArticle, '0123');
        }
//        dd($mryhJoinArticles);
        return view('admin.mryh.mryhJoinArticle.index', ['datas' => $mryhJoinArticles, 'con_arr' => $con_arr]);
    }
}