<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2017/9/28
 * Time: 10:30
 */

namespace App\Components;

use App\Models\GuanZhu;
use App\Models\Login;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserManager
{

    /*
     * 根据id获取用户信息，带token
     *
     * By TerryQi
     *
     * 2017-09-28
     */
    public static function getByIdWithToken($id)
    {
        $class = substr(explode('\\', __CLASS__)[count(explode('\\', __CLASS__)) - 1], 0, -7);

        if (Cache::get("$class:$id")) {
//            Utils::processLog(__METHOD__, '', '命中缓存');
            $info = Cache::get("$class:$id");
            return $info;
        }

        $info = User::where('id', '=', $id)->first();
        Cache::put("$class:$id", $info, 60 * 24 * 7);

        return $info;
    }

    /*
     * 根据id获取用户信息
     *
     * By TerryQi
     *
     * 2017-09-28
     */
    public static function getById($id)
    {
        $info = self::getByIdWithToken($id);

        if ($info) {
            $info->token = null;
        }

        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-06-07
     */
    public static function getInfoByLevel($info, $level)
    {
        if ($info->gender) {
            $info->gender_str = Utils::user_gender_val[$info->gender];
        }
        $info->type_str = Utils::USER_TYPE_VAL[$info->type];
        $info->status_str = Utils::user_status_val[$info->status];
        return $info;
    }


    /*
         * 根据条件获取列表
         *
         * By TerryQi
         *
         * 2018-06-06
         */
    public static function getListByCon($con_arr, $is_paginate)
    {
        $infos = new User();
        //相关条件
        if (array_key_exists('id', $con_arr) && !Utils::isObjNull($con_arr['id'])) {
            $infos = $infos->where('id', '=', $con_arr['id']);
        }
        if (array_key_exists('id_arr', $con_arr) && !Utils::isObjNull($con_arr['id_arr'])) {
            $infos = $infos->wherein('id', $con_arr['id_arr']);
        }
        if (array_key_exists('avatar_not_null', $con_arr) && !Utils::isObjNull($con_arr['avatar_not_null'])) {
            $infos = $infos->whereNotNull('avatar');
        }
        if (array_key_exists('phonenum', $con_arr) && !Utils::isObjNull($con_arr['phonenum'])) {
            $infos = $infos->where('phonenum', '=', $con_arr['phonenum']);
        }
        if (array_key_exists('password', $con_arr) && !Utils::isObjNull($con_arr['password'])) {
            $infos = $infos->where('password', '=', $con_arr['password']);
        }
        if (array_key_exists('level', $con_arr) && !Utils::isObjNull($con_arr['level'])) {
            $infos = $infos->where('level', '=', $con_arr['level']);
        }
        if (array_key_exists('type', $con_arr) && !Utils::isObjNull($con_arr['type'])) {
            $infos = $infos->where('type', '=', $con_arr['type']);
        }
        if (array_key_exists('search_word', $con_arr) && !Utils::isObjNull($con_arr['search_word'])) {
            $keyword = $con_arr['search_word'];
            $infos = $infos->where(function ($query) use ($keyword) {
                $query->where('phonenum', 'like', "%{$keyword}%")
                    ->orwhere('nick_name', 'like', "%{$keyword}%")
                    ->orwhere('real_name', 'like', "%{$keyword}%");
            });
        }
        //2018-12-26，用于替换存量的在微信上的头像
        if (array_key_exists('avatar_search_word', $con_arr) && !Utils::isObjNull($con_arr['avatar_search_word'])) {
            $infos = $infos->where('avatar', 'like', "%" . $con_arr['avatar_search_word'] . "%");
        }
        $infos = $infos->orderby('id', 'desc');
        //配置规则
        if ($is_paginate) {
            $page_size = Utils::PAGE_SIZE;
            //如果con_arr中有page_size信息
            if (array_key_exists('page_size', $con_arr) && !Utils::isObjNull($con_arr['page_size'])) {
                $page_size = $con_arr['page_size'];
            }
            $infos = $infos->paginate($page_size);
        } else {
            $infos = $infos->get();
        }
        return $infos;
    }


    /*
     * 根据user_code和token校验合法性，全部插入、更新、删除类操作需要使用中间件
     *
     * By TerryQi
     *
     * 2017-09-14
     *
     * 返回值
     *
     */
    public static function ckeckToken($id, $token)
    {
        $class = substr(explode('\\', __CLASS__)[count(explode('\\', __CLASS__)) - 1], 0, -7);
        Utils::processLog(__METHOD__, '', "ckeckToken class：" . json_encode($class) . " id:" . $id);

        if (Cache::get("$class:$id")) {
//            Utils::processLog(__METHOD__, '', '命中缓存');
            $info = Cache::get("$class:$id");

            if ($info->token == $token) {
                return true;
            } else {
                return false;
            }
        }

        $info = User::where('id', $id)->where('token', $token)->first();

        if ($info) {
            Cache::put("$class:$id", $info, 60 * 24 * 7);
            return true;
        } else {
            return false;
        }


//        //根据id、token获取用户信息
//        $count = User::where('id', '=', $id)->where('token', '=', $token)->count();
//        if ($count > 0) {
//            return true;
//        } else {
//            return false;
//        }
    }


    /*
     * 配置用户信息，用于更新用户信息和新建用户信息
     *
     * By TerryQi
     *
     * 2017-09-28
     *
     * PS：公众号和小程序输出的字段不一样
     */
    public static function setInfo($info, $data)
    {
        if (array_key_exists('nick_name', $data)) {
            $info->nick_name = array_get($data, 'nick_name');
        }
        if (array_key_exists('real_name', $data)) {
            $info->real_name = array_get($data, 'real_name');
        }
        if (array_key_exists('avatar', $data)) {
            $info->avatar = array_get($data, 'avatar');
        }
        if (array_key_exists('phonenum', $data)) {
            $info->phonenum = array_get($data, 'phonenum');
        }
        if (array_key_exists('gender', $data)) {
            $info->gender = array_get($data, 'gender');
        }
        if (array_key_exists('status', $data)) {
            $info->status = array_get($data, 'status');
        }
        if (array_key_exists('type', $data)) {
            $info->type = array_get($data, 'type');
        }
        if (array_key_exists('token', $data)) {
            $info->token = array_get($data, 'token');
        }
        if (array_key_exists('country', $data)) {
            $info->country = array_get($data, 'country');
        }
        if (array_key_exists('province', $data)) {
            $info->province = array_get($data, 'province');
        }
        if (array_key_exists('city', $data)) {
            $info->city = array_get($data, 'city');
        }
        if (array_key_exists('language', $data)) {
            $info->language = array_get($data, 'language');
        }
        if (array_key_exists('level', $data)) {
            $info->level = array_get($data, 'level');
        }
        if (array_key_exists('sign', $data)) {
            $info->sign = array_get($data, 'sign');
        }
        if (array_key_exists('score', $data)) {
            $info->score = array_get($data, 'score');
        }
        return $info;
    }

    // 生成guid
    /*
     * 生成uuid全部用户相同，uuid即为token
     *
     */
    public static function getGUID()
    {
        if (function_exists('com_create_guid')) {
            return com_create_guid();
        } else {
            mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));

            $uuid = substr($charid, 0, 8)
                . substr($charid, 8, 4)
                . substr($charid, 12, 4)
                . substr($charid, 16, 4)
                . substr($charid, 20, 12);
            return $uuid;
        }
    }


    /*
   * 生成验证码
   *
   * By TerryQi
   */
    public static function sendVertify($phonenum)
    {
        $vertify_code = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);  //生成4位验证码
        $vertify = new Vertify();
        $vertify->phonenum = $phonenum;
        $vertify->code = $vertify_code;
        $vertify->save();
        /*
         * 预留，需要触发短信端口进行验证码下发
         */
        if ($vertify) {
            SMSManager::sendSMSVerification($phonenum, $vertify_code);
            return true;
        }
        return false;
    }

    /*
     * 校验验证码
     *
     * By TerryQi
     *
     * 2017-11-28
     */
    public static function judgeVertifyCode($phonenum, $vertify_code)
    {
        $vertify = Vertify::where('phonenum', '=', $phonenum)
            ->where('code', '=', $vertify_code)->where('status', '=', '0')->first();
        if ($vertify) {
            //验证码置为失效
            $vertify->status = '1';
            $vertify->save();
            return true;
        } else {
            return false;
        }
    }


    /*
     * 用户登录接口
     *
     * 说明：针对公众号、小程序等，只有登录流程，没有注册流程
     *
     * By TerryQi
     *
     * 2018-07-03
     *
     * account为账户类型，详见数据字典
     *
     * $data为封装的信息，其中包括登录信息和用户基本信息，需要拆开
     *
     */
    public static function login($account_type, $data)
    {
        if ($data == null) {
            return null;
        }

        $user = null;   //返回的用户信息
        //根据登录账号类型的不同，分别处理
        switch ($account_type) {

            case Utils::ACCOUNT_TYPE_FWH:       //公众号登录
                $user = self::loginFWH($data);
                break;
            case Utils::ACCOUNT_TYPE_XCX:   //小程序登录
                $user = self::loginXCX($data);
                break;
        }

        //进行用户的头像处理
        /*
         * 2018-12-26日进行优化，在运营过程中发现，部分用户头像丢失的问题，经排查问题为用户更换头像，则微信登录时的头像则失效，因此在登录时需要进行用户头像的处理
         *
         * 如果用户头像属于三方，则进行七牛上传，并更新
         *
         * By TerryQi
         *
         */
        UserAvaterManager::setAvaterToQN($user->id);

        return $user;
    }


    /*
     * 公众号的登录和注册流程
     *
     * By TerryQi
     *
     * 2018-07-03
     *
     * 其中data中应该包含openid、unionid（可选）信息
     */
    public static function loginFWH($data)
    {
        Utils::processLog(__METHOD__, '', " " . "data:" . json_encode($data));
        $user = null;   //应返回用户信息
        //第一步，入参中是否有unionid信息，如果有，则通过uniond判断用户，平台中是否已有用户信息
        if (array_key_exists('unionid', $data)) {
            $con_arr = array(
                've_value2' => $data['unionid']
            );
            $login = LoginManager::getListByCon($con_arr, false)->first();  //根据unionid获取登录信息
            //如果已经有用户信息，则获取用户信息，并进行登录信息补全，返回用户信息
            if ($login) {
                Utils::processLog(__METHOD__, '', " " . "condition1 login:" . json_encode($login));
                $user = UserManager::getByIdWithToken($login->user_id);
                //补全登录信息
                self::setLoginFWH($user->id, $data);
                return $user;
            }
        }
        //第二步，入参是否有openid信息，如果有，则通过openid判断用户，平台中是否已有用户信息
        if (array_key_exists('openid', $data)) {
            $con_arr = array(
                've_value1' => $data['openid']
            );
            $login = LoginManager::getListByCon($con_arr, false)->first();  //根据unionid获取登录信息
            //如果已经有用户信息，则获取用户信息，并进行登录信息补全，返回用户信息
            if ($login) {
                Utils::processLog(__METHOD__, '', " " . "condition2 login:" . json_encode($login));
                $user = UserManager::getByIdWithToken($login->user_id);
                //补全登录信息
                self::setLoginFWH($login->user_id, $data);
                return $user;
            }
        }
        //第三步，如果均未返回用户信息，则代表需要新注册用户
        //注册用户
        $user = new User();
        $user = UserManager::setInfo($user, $data);
        $user->token = UserManager::getGUID();
        $user->save();
        //如果是新注册用户，则给一个new_flag==true//////////////////////////////////////////////////
        $user->new_flag = true;
        //new_flag是新用户标识，主要标识用户信息////////////////////////////////////////////////////
        //补全登录信息
        $login = self::setLoginFWH($user->id, $data);
        Utils::processLog(__METHOD__, '', "condition3 login:" . json_encode($login));
        return $user;
    }


    /*
     * 公众号补全登录信息，主要解决uniond、openid等缺失的问题
     *
     * By TerryQi
     *
     * 2018-07-03
     *
     * $data中应有busi_name、openid、unionid，分别映射login表中的busi_name、ve_value1、ve_value2
     *
     * $user_id为用户id，因此需要注册成功再处理绑定关系
     *
     * return false：失败 true：成功
     *
     */
    public static function setLoginFWH($user_id, $data)
    {
        Utils::processLog(__METHOD__, '', " " . "user_id:" . $user_id . " data:" . json_encode($data));
        //user_id如果为空不能往下走
        if (Utils::isObjNull($user_id)) {
            return null;
        }
        //获取基本信息
        $busi_name = $data['busi_name'];    //业务名
        $openid = $data['openid'];      //openid
        $unionid = null;        //unionid
        if (array_key_exists('unionid', $data)) {
            $unionid = $data['unionid'];        //unionid
        }
        //根据openid获取用户信息
        $con_arr = array(
            've_value1' => $openid
        );
        $login = LoginManager::getListByCon($con_arr, false)->first();
        Utils::processLog(__METHOD__, '', " " . "login:" . json_encode($login));
        //如果有值，就进行信息补全
        if (!$login) {
            $login = new Login();
        }
        $login->user_id = $user_id;
        $login->account_type = Utils::ACCOUNT_TYPE_FWH;
        $login->busi_name = $busi_name;
        $login->ve_value1 = $openid;
        $login->ve_value2 = $unionid;
        $login->save();

        Utils::processLog(__METHOD__, '', " " . "login:" . json_encode($login));

        return $login;
    }


    /*
     * 小程序的登录和注册流程
     *
     * By TerryQi
     *
     * 2018-07-04
     *
     * $data中应该包含openid、unionid（可选）、session信息
     */
    public static function loginXCX($data)
    {
        Utils::processLog(__METHOD__, '', " " . "data:" . json_encode($data));
        $user = null;   //应返回用户信息
        //第一步，入参中是否有unionid信息，如果有，则通过uniond判断用户，平台中是否已有用户信息
        if (array_key_exists('unionid', $data)) {
            $con_arr = array(
                've_value2' => $data['unionid']
            );
            $login = LoginManager::getListByCon($con_arr, false)->first();  //根据unionid获取登录信息
            Utils::processLog(__METHOD__, '', " " . "condition1 login pos1:" . json_encode($login));
            //如果已经有用户信息，则获取用户信息，并进行登录信息补全，返回用户信息
            if ($login) {
                Utils::processLog(__METHOD__, '', " " . "condition1 login pos2:" . json_encode($login));
                $user = UserManager::getByIdWithToken($login->user_id);
                //补全登录信息
                self::setLoginXCX($user->id, $data);
                return $user;
            }
        }
        //第二步，入参是否有openid信息，如果有，则通过openid判断用户，平台中是否已有用户信息
        if (array_key_exists('openid', $data)) {
            $con_arr = array(
                've_value1' => $data['openid']
            );
            $login = LoginManager::getListByCon($con_arr, false)->first();  //根据unionid获取登录信息
            Utils::processLog(__METHOD__, '', " " . "condition2 login pos1:" . json_encode($login));
            //如果已经有用户信息，则获取用户信息，并进行登录信息补全，返回用户信息
            if ($login) {
                Utils::processLog(__METHOD__, '', " " . "condition2 login pos2:" . json_encode($login));
                $user = UserManager::getByIdWithToken($login->user_id);
                //补全登录信息
                self::setLoginXCX($login->user_id, $data);
                return $user;
            }
        }
        //第三步，如果均未返回用户信息，则代表需要新注册用户
        //注册用户
        $user = new User();
        $user = UserManager::setInfo($user, $data);
        $user->token = UserManager::getGUID();
        $user->save();

        //如果是新注册用户，则给一个new_flag==true//////////////////////////////////////////////////
        $user->new_flag = true;
        //new_flag是新用户标识，主要标识用户信息////////////////////////////////////////////////////

        Utils::processLog(__METHOD__, '', " " . "user:" . json_encode($user));
        //补全登录信息
        $login = self::setLoginXCX($user->id, $data);
        Utils::processLog(__METHOD__, '', " " . "condition3 login:" . json_encode($login));
        return $user;
    }

    /*
     * 小程序补全登录信息，主要解决uniond、openid等缺失的问题
     *
     * By TerryQi
     *
     * 2018-07-03
     *
     * $data中应有busi_name、openid、unionid，分别映射login表中的busi_name、ve_value1、ve_value2
     *
     * $user_id为用户id，因此需要注册成功再处理绑定关系
     *
     * return false：失败 true：成功
     *
     */
    public static function setLoginXCX($user_id, $data)
    {
        Utils::processLog(__METHOD__, '', " " . "user_id:" . $user_id . " data:" . json_encode($data));
        //user_id如果为空不能往下走
        if (Utils::isObjNull($user_id)) {
            return null;
        }
        //获取基本信息
        $busi_name = $data['busi_name'];    //业务名
        $openid = $data['openid'];      //openid
        $unionid = null;        //unionid
        if (array_key_exists('unionid', $data)) {
            $unionid = $data['unionid'];        //unionid
        }
        //根据openid获取用户信息
        $con_arr = array(
            've_value1' => $openid
        );
        $login = LoginManager::getListByCon($con_arr, false)->first();
        Utils::processLog(__METHOD__, '', " " . "login:" . json_encode($login));
        //如果有值，就进行信息补全
        if (!$login) {
            Utils::processLog(__METHOD__, '', " " . "不存在login，则新建login信息");
            $login = new Login();
        }
        $login->user_id = $user_id;
        $login->account_type = Utils::ACCOUNT_TYPE_XCX;
        $login->busi_name = $busi_name;
        $login->ve_value1 = $openid;
        $login->ve_value2 = $unionid;
        $login->save();

        Utils::processLog(__METHOD__, '', " " . "login:" . json_encode($login));

        return $login;
    }


    /*
     * 将服务号web.auth的用户数据转为$data形式
     *
     * By TerryQi
     *
     * 2018-07-18
     */
    public static function convertFWHDataToData($original_user)
    {
        if (!array_key_exists('openid', $original_user) || Utils::isObjNull($original_user['openid'])) {
            return null;
        }

        $data = array(
            "openid" => $original_user['openid'],
            'nick_name' => $original_user['nickname'],
            'gender' => $original_user['sex'],
            'language' => $original_user['language'],
            'avatar' => $original_user['headimgurl'],
            'country' => $original_user['country'],
            'province' => $original_user['province'],
            'city' => $original_user['city'],
            'busi_name' => $original_user['busi_name']
        );
        if (array_key_exists('unionid', $original_user)) {
            $data['unionid'] = $original_user['unionid'];
        }
        return $data;
    }


    /*
     * 将服务号的session_val转换为user_data数组数据
     *
     * By TerryQi
     *
     * 2018-07-18
     */
    public static function convertSessionValToUserData($session_val, $busi_name)
    {
        //获取用户相关信息
        Utils::processLog(__METHOD__, '', " session_val : " . json_encode($session_val));
        $user_val = $session_val['default']->toArray();

        Utils::processLog(__METHOD__, '', "user_val:" . json_encode($user_val));
        $original_user = $user_val['original']; //获取用户基本信息
        $original_user['busi_name'] = $busi_name;
        //封装数据
        $user_data = self::convertFWHDataToData($original_user);
        return $user_data;
    }


    /*
     * 将小程序的消息解密的数据返回至前端
     *
     * By TerryQi
     *
     * 2018-11-22
     */
    public static function convertDecryptDatatoUserData($decrytData)
    {
        $data = array(
            'openid' => $decrytData['openId'],
            'nick_name' => $decrytData['nickName'],
            'gender' => $decrytData['gender'],
            'language' => $decrytData['language'],
            'city' => $decrytData['city'],
            'province' => $decrytData['province'],
            'country' => $decrytData['country'],
            'avatar' => $decrytData['avatarUrl'],
            'unionid' => $decrytData['unionId']
        );
        return $data;
    }


    /*
     * 业务统计数据
     *
     * By TerryQi
     *
     * 2018-08-14
     */
    public static function addStatistics($user_id, $item, $num)
    {
        $user = self::getByIdWithToken($user_id);
        switch ($item) {
            case "yq_num":
                $user->yq_num = $user->yq_num + $num;
                break;
            case "rel_num":
                $user->rel_num = $user->rel_num + $num;
                break;
        }
        $user->save();
    }


    /*
     * 进行消息解密
     *
     * By TerryQi
     *
     * 2018-11-22
     *
     * @app为外部信息，code、vi（注意已经解密）、encryptedData（注意已经解密）、env_appid_name
     */
    public static function decryptData($app, $code, $iv, $encryptedData, $env_appid_name)
    {
        Utils::processLog(__METHOD__, '', "code:" . $code . " iv:" . $iv . " encrytedData:" . $encryptedData . " env_appid_name:" . $env_appid_name);
        $code = $code;
        $result = $app->auth->session($code);
        Utils::processLog(__METHOD__, '', json_encode($result));
        //如果出错，返回null
        if (array_key_exists('errcode', $result)) {
            return null;
        }
        $sessionKey = $result['session_key'];
        Utils::processLog(__METHOD__, '', "sessionKey:" . json_encode($sessionKey));
        $appid = env($env_appid_name);
        Utils::processLog(__METHOD__, '', "appid:" . $appid);
        $pc = new WXBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $result);
        Utils::processLog(__METHOD__, '', "errorCode:" . json_encode($errCode));
        if ($errCode == 0) {
            return (array)json_decode($result, true);
        } else {
            return null;
        }
    }


    /*
     * 随机获取头像数据
     *
     * By TerryQi
     *
     * 2018-12-28
     */
    public static function getRandomAvatar()
    {
        $avatar_url = Utils::AVATAR_ARR[rand(0, count(Utils::AVATAR_ARR) - 1)];
        return $avatar_url;
    }

}