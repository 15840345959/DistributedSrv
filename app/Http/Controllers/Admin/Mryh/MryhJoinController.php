<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\Mryh;

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


class MryhJoinController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        $game_id = null;
        $user_id = null;

        if (array_key_exists('game_id', $data) && !Utils::isObjNull($data['game_id'])) {
            $game_id = $data['game_id'];
        }
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }
        $con_arr = array(
            'game_id' => $game_id,
            'user_id' => $user_id,
        );

        $mryhJoins = MryhJoinManager::getListByCon($con_arr, true);
//        dd($mryhJoins);
        foreach ($mryhJoins as $mryhJoin) {
            $mryhJoin = MryhJoinManager::getInfoByLevel($mryhJoin, '013');
        }
//        dd($mryhJoins);
        return view('admin.mryh.mryhJoin.index', ['datas' => $mryhJoins, 'con_arr' => $con_arr]);
    }
}