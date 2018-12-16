<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\GZH;

use App\Components\GZH\AdminManager;
use App\Components\QNManager;
use App\Components\GZH\MenuManager;
use App\Components\Utils;
use App\Components\GZH\WeChatManager;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\GZH\Menu;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class MenuController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        //相关搜素条件
        $busi_name = null;
        if (array_key_exists('busi_name', $data) && !Utils::isObjNull($data['busi_name'])) {
            $busi_name = $data['busi_name'];
        }
        $con_arr = array(
            'busi_name' => $busi_name,
            'f_id' => '0'             //f_id为0，即为父菜单
        );
//        dd($con_arr);
        $menus = MenuManager::getListByCon($con_arr, true);

        foreach ($menus as $menu) {
            $menu = MenuManager::getInfoByLevel($menu, '0');
            $sub_con_arr = array(
                'f_id' => $menu->id
            );
            $sub_menus = MenuManager::getListByCon($sub_con_arr, false);
            foreach ($sub_menus as $sub_menu) {
                $sub_menu = MenuManager::getInfoByLevel($sub_menu, false);
            }
            $menu->sub_menus = $sub_menus;
        }
//        dd($con_arr);
        return view('admin.gzh.menu.index', ['datas' => $menus, 'con_arr' => $con_arr]);
    }

    /*
     * 添加、编辑菜单-get
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
        $menu = new Menu();
        if (array_key_exists('id', $data)) {
            $menu = MenuManager::getById($data['id']);
        }
        $menu = MenuManager::setInfo($menu, $data);
//        $menu = MenuManager::getInfoByLevel($menu, '');
        //获取全部顶级菜单
        $con_arr = array(
            'f_id' => '0',
            'level' => '1',
            'busi_name' => $data['busi_name']
        );
        $f_menus = MenuManager::getListByCon($con_arr, false);
        return view('admin.gzh.menu.edit', ['admin' => $admin, 'data' => $menu, 'upload_token' => $upload_token, 'f_menus' => $f_menus]);
    }

    /*
     * 添加、编辑菜单-post
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
        $menu = new Menu();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $menu = MenuManager::getById($data['id']);
        }
        $menu = MenuManager::setInfo($menu, $data);
        $menu->admin_id = $admin->id;      //记录管理员id
        $menu->save();

        return ApiResponse::makeResponse(true, $menu, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 删除菜单
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
        $menu = Menu::find($id);
        $menu->delete();

        return ApiResponse::makeResponse(true, "删除成功", ApiResponse::SUCCESS_CODE);
    }

    /*
     * 生成公众号菜单
     *
     * By TerryQi
     *
     * 2018-07-09
     */
    public function create(Request $request)
    {
        $data = $request->all();
        //必须传入busi_name
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'busi_name' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数' . $requestValidationResult]);
        }
        $con_arr = array(
            'busi_name' => $data['busi_name'],
            'f_id' => '0'
        );
//        dd($con_arr);
        $menus = MenuManager::getListByCon($con_arr, true);
        foreach ($menus as $menu) {
            $menu = MenuManager::getInfoByLevel($menu, '0');
            $sub_con_arr = array(
                'f_id' => $menu->id
            );
            $sub_menus = MenuManager::getListByCon($sub_con_arr, false);
            foreach ($sub_menus as $sub_menu) {
                $sub_menu = MenuManager::getInfoByLevel($sub_menu, false);
            }
            $menu->sub_menus = $sub_menus;
        }
        //生成菜单json
        if ($menus) {
            foreach ($menus as $k => $menu) {
                $buttons[$k]['name'] = $menu['name'];
                //如果level==0，没有下级菜单
                if ($menu['level'] == '0') {
                    //菜单按钮类型
                    $buttons[$k]['type'] = $menu['type'];
                    //根据类型进行设置
                    if ($menu['type'] == "view") {
                        $buttons[$k]['url'] = $menu['url'];
                    }
                    if ($menu['type'] == "media_id") {
                        $buttons[$k]['media_id'] = $menu['url'];
                    }
                    if ($menu['type'] == "click") {
                        $buttons[$k]['key'] = $menu['key'];
                    }
                    if ($menu['type'] == "miniprogram") {
                        $buttons[$k]['url'] = $menu['url'];
                        $buttons[$k]['appid'] = $menu['appid'];
                        $buttons[$k]['pagepath'] = $menu['pagepath'];
                    }
                } else {
                    //有下级菜单
                    $buttons[$k]['sub_button'] = array();
                    foreach ($menu['sub_menus'] as $v => $child) {
                        $buttons[$k]['sub_button'][$v]['name'] = $child['name'];
                        $buttons[$k]['sub_button'][$v]['type'] = $child['type'];
                        //根据类型进行设置
                        if ($child['type'] == "view") {
                            $buttons[$k]['sub_button'][$v]['url'] = $child['url'];
                        }
                        if ($child['type'] == "media_id") {
                            $buttons[$k]['sub_button'][$v]['media_id'] = $child['media_id'];
                        }
                        if ($child['type'] == "click") {
                            $buttons[$k]['sub_button'][$v]['key'] = $child['key'];
                        }
                        if ($child['type'] == "miniprogram") {
                            $buttons[$k]['sub_button'][$v]['url'] = $child['url'];
                            $buttons[$k]['sub_button'][$v]['appid'] = $child['appid'];
                            $buttons[$k]['sub_button'][$v]['pagepath'] = $child['pagepath'];
                        }
                    }
                }
            }
        } else {
            $buttons = array();
        }
        Utils::processLog(__METHOD__, '', " " . "buttons:" . json_encode($buttons));
        $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL[$data['busi_name']]);
        WeChatManager::deleteMenu($app);
        $result = WeChatManager::createMenu($app, $buttons);
        Utils::processLog(__METHOD__, '', " " . "result:" . json_encode($result));
        return ApiResponse::makeResponse(true, "创建成功", ApiResponse::SUCCESS_CODE);
    }

}