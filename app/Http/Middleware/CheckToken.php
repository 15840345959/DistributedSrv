<?php

namespace App\Http\Middleware;

use App\Components\UserManager;
use App\Http\Controllers\ApiResponse;
use Closure;
use Illuminate\Support\Facades\Log;


class CheckToken
{


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $data = $request->all();
        //合规校验
        if (!array_key_exists('token', $data)) {
            return ApiResponse::makeResponse(false, ApiResponse::$returnMessage[ApiResponse::TOKEN_ERROR], ApiResponse::TOKEN_ERROR);
        }
        if (!array_key_exists('user_id', $data)) {
            return ApiResponse::makeResponse(false, ApiResponse::$returnMessage[ApiResponse::USER_ID_LOST], ApiResponse::USER_ID_LOST);
        }
        //校验token的合法性
        $result = UserManager::ckeckToken($data['user_id'], $data['token']);
        if (!$result) {
            return ApiResponse::makeResponse(false, ApiResponse::$returnMessage[ApiResponse::TOKEN_ERROR], ApiResponse::TOKEN_ERROR);
        }

        if ($result->status != 1) {
            return ApiResponse::makeResponse(false, ApiResponse::$returnMessage[ApiResponse::USER_DISABLE], ApiResponse::USER_DISABLE);
        }

        return $next($request);
    }

}
