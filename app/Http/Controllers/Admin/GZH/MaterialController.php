<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\GZH;

use App\Components\AdminManager;
use App\Components\QNManager;
use App\Components\GZH\MaterialManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\GZH\Material;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class MaterialController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        //相关搜素条件
        $busi_name = null;
        $type = null;
        if (array_key_exists('type', $data) && !Utils::isObjNull($data['type'])) {
            $type = $data['type'];
        }
        if (array_key_exists('busi_name', $data) && !Utils::isObjNull($data['busi_name'])) {
            $busi_name = $data['busi_name'];
        }
        $con_arr = array(
            'type' => $type,
            'busi_name' => $busi_name
        );
//        dd($con_arr);
        $materials = MaterialManager::getListByCon($con_arr, true);
        foreach ($materials as $material) {
            $material = MaterialManager::getInfoByLevel($material, '0');
        }
//        dd($materials);
        return view('admin.gzh.material.index', ['datas' => $materials, 'con_arr' => $con_arr]);
    }

    /*
     * 添加、编辑公众号素材-get
     *
     * 其中，必须传入busi_name
     *
     * By TerryQi
     *
     * 2018-4-9
     */
    public function edit(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');
        //必须传入busi_name
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'busi_name' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数' . $requestValidationResult]);
        }
        //生成七牛token
        $upload_token = QNManager::uploadToken();
        $material = new Material();
        if (array_key_exists('id', $data)) {
            $material = MaterialManager::getById($data['id']);
        }
        $material = MaterialManager::setInfo($material, $data);
        $material = MaterialManager::getInfoByLevel($material, '');

        return view('admin.gzh.material.edit', ['admin' => $admin, 'data' => $material, 'upload_token' => $upload_token]);
    }

    /*
     * 添加、编辑公众号素材-post
     *
     * By TerryQi
     *
     * 2018-4-9
     *
     * 其中busi_name参看Utils中的BUSI_NAME_VAL值，此为业务名称
     */
    public function editPost(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');

        //视频信息需要有title和description
        if ($data['type'] == "video") {
            //video_title
            if (array_key_exists('title', $data)) {
                $video_title = $data['title'];
            } else {
                $video_title = "ISART视频";
            }
            //video description
            if (array_key_exists('description', $data)) {
                $video_description = $data['description'];
            } else {
                $video_description = "ISART视频";
            }
        }
        //放置在storage/public/material文件夹下
        $file = $request->file('material');
        $file_name = Utils::generateTradeNo();
        Utils::processLog(__METHOD__, '', " " . "file_name:" . $file_name);
        $file_type = $file->getClientOriginalExtension();
        Utils::processLog(__METHOD__, '', " " . "file_type:" . $file_type);
        $path = $file->storeAs('material', $file_name . '.' . $file_type);
        Utils::processLog(__METHOD__, '', " " . "path:" . $path);
        $file_path = storage_path('app/' . $path);
        Utils::processLog(__METHOD__, '', " " . "file_path:" . $file_path);
        //根据类型不同，进行素材上传
        $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL[$data['busi_name']]);
        $result = null;     //上传结果
        switch ($data['type']) {
            case 'image':
                $result = $app->material->uploadImage($file_path);
                break;
            case 'video':
                $result = $app->material->uploadVideo($file_path, $video_title, $video_description);
                break;
            case 'voice':
                $result = $app->material->uploadVoice($file_path);
                break;
            case 'thumb':
                $result = $app->material->uploadThumb($file_path);
                break;

        }
        Utils::processLog(__METHOD__, '', " " . "result:" . json_encode($result));
        //存储素材
        $material = new Material();
        $material = MaterialManager::setInfo($material, $data);
        $material->media_id = $result['media_id'];
        if (array_key_exists('url', $result)) {
            $material->url = $result['url'];
        }
        $material->admin_id = $admin->id;
        $material->save();

        return ApiResponse::makeResponse(true, storage_path() . $path, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 删除公众号素材
     *
     * By mtt
     *
     * 2018-4-9
     */
    public function del(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return ApiResponse::makeResponse(false, "删除失败", ApiResponse::INNER_ERROR);
        }
        $material = Material::find($id);
        //删除公众号素材
        $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL[$data['busi_name']]);
        $result = $app->material->delete($material->media_id);
        Utils::processLog(__METHOD__, '', " " . "result:" . json_encode($result));
        //删除记录
        $material->delete();

        return ApiResponse::makeResponse(true, "删除成功", ApiResponse::SUCCESS_CODE);
    }


}