<?php
/**
 * File_Name:ApiResponse.php
 * Author: leek
 * Date: 2017/8/23
 * Time: 14:37
 */

namespace App\Http\Controllers;

use App\Components\Utils;
use Illuminate\Support\Facades\Log;

class ApiResponse
{
    /*
     * 以下为错误码表，全部逻辑错误遵循该表
     *
     *
     */

    //通用错误码
    const SUCCESS_CODE = 200;       //成功

    const MISSING_PARAM = 901;   //缺少参数
    const INNER_ERROR = 902;     //逻辑错误
    const UNKNOW_ERROR = 999;   //未知错误

    //token及用户校验错误
    const TOKEN_ERROR = 101;    //token校验失败
    const USER_ID_LOST = 102;    //用户编码丢失
    const REGISTER_FAILED = 103;    //注册失败
    const NO_USER = 104;    //未找到用户
    const VERTIFY_ERROR = 105;    //验证码验证失败
    const PHONENUM_DUP = 106;    //手机号重复
    const PHONENUM_ALREAD_REGISTED = 107; //手机号已经注册过
    const USER_DISABLE = 108; //手机号已经注册过

    //投票错误
    const VOTE_OUTOF_NUM = 301;     //投票数已用完
    const VOTE_ALREADY_APPLY = 302;     //已经报名，正在审核中

    //营销活动
    const USER_SCORE_LACK = 501;        //用户积分不足
    const DEDUCT_SCORE_ERROR = 502;   //扣减积分失败


    //映射错误信息
    public static $returnMessage = [
        self::SUCCESS_CODE => '调用成功',

        self::MISSING_PARAM => '缺少参数',
        self::INNER_ERROR => '内部错误',
        self::UNKNOW_ERROR => '未知错误',

        self::TOKEN_ERROR => 'token校验失败',
        self::USER_ID_LOST => '缺少用户编码',
        self::REGISTER_FAILED => '注册失败',
        self::NO_USER => '未找到用户',
        self::VERTIFY_ERROR => '验证码验证失败',
        self::PHONENUM_DUP => '手机号码重复',
        self::PHONENUM_ALREAD_REGISTED => '手机号已经注册',
        self::USER_DISABLE => '用户被禁用',

        //投票相关
        self::VOTE_OUTOF_NUM => '投票数已用完',
        self::VOTE_ALREADY_APPLY => '已经报名，正在审核中',

        //营销活动
        self::USER_SCORE_LACK => '用户积分不足',
    ];

    //格式化返回
    /*
     * 返回数据包括
     *
     * result：代表接口调用的逻辑结果 true代表成功 false代表失败
     * code：错误码，用于前端进行逻辑处理，特殊错误码应与后端特殊定义，以便处理特殊逻辑
     * message：错误返回信息，文字描述
     * ret：返回值，具体业务返回结果
     *
     */
    //格式化返回
    public static function makeResponse($result, $ret, $code, $mapping_function = null, $params = [])
    {
        $rsp = [];
        $rsp['code'] = $code;

        if ($result === true) {
            $rsp['result'] = true;
            $rsp['message'] = self::$returnMessage[$code];
            $rsp['ret'] = $ret;
        } else {
            $rsp['result'] = false;
            if ($ret) {
                $rsp['message'] = $ret;
            } else {
                if (array_key_exists($code, self::$returnMessage)) {
                    $rsp['message'] = self::$returnMessage[$code];
                } else {
                    $rsp['message'] = 'undefind error code';
                }
            }
            $rsp['ret'] = $ret;
        }
        Utils::backLog(__METHOD__, $rsp);
        return response()->json($rsp);
    }
}