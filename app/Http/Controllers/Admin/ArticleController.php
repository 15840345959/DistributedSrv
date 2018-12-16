<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 11:32
 */

namespace App\Http\Controllers\Admin;

use App\Components\ArticleManager;
use App\Components\DateTool;
use App\Components\LoginManager;
use App\Components\QNManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Components\XCXFormManager;
use App\Components\YSB\YSBSendXCXTplMessageManager;
use App\Http\Controllers\ApiResponse;
use App\Models\AD;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController
{

    /*
     * 首页
     *
     * By mtt
     *
     * 2018-4-9
     */
    public static function index(Request $request)
    {
        $admin = $request->session()->get('admin');
        $data = $request->all();
        //相关搜素条件
        $search_word = null;    //搜索条件
        $id = null;     //作品id
        $user_id = null;        //用户信息
        $article_type_id = null;        //作品类型
        $recomm_flag = null;
        $audit_status = null;
        $busi_name = null;      //业务归属


        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $id = $data['id'];
        }
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }
        if (array_key_exists('article_type_id', $data) && !Utils::isObjNull($data['article_type_id'])) {
            $article_type_id = $data['article_type_id'];
        }
        if (array_key_exists('busi_name', $data) && !Utils::isObjNull($data['busi_name'])) {
            $busi_name = $data['busi_name'];
        }
        if (array_key_exists('recomm_flag', $data) && !Utils::isObjNull($data['recomm_flag'])) {
            $recomm_flag = $data['recomm_flag'];
        }
        if (array_key_exists('audit_status', $data) && !Utils::isObjNull($data['audit_status'])) {
            $audit_status = $data['audit_status'];
        }
        $con_arr = array(
            'search_word' => $search_word,
            'id' => $id,
            'user_id' => $user_id,
            'article_type_id' => $article_type_id,
            'recomm_flag' => $recomm_flag,
            'audit_status' => $audit_status,
            'busi_name' => $busi_name
        );
        $articles = ArticleManager::getListByCon($con_arr, true);
        foreach ($articles as $article) {
            $article = ArticleManager::getInfoByLevel($article, '');
        }
        return view('admin.article.index', ['admin' => $admin, 'datas' => $articles, 'con_arr' => $con_arr]);
    }


    /*
     * 设置作品状态
     *
     * By mtt
     *
     * 2018-4-9
     */
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数轮播图id$id']);
        }
        $article = ArticleManager::getById($data['id']);
        $article = ArticleManager::setInfo($article, $data);
        $article->save();

        /*
         * 增加服务通知，及如果是小程序的话，则增加服务通知
         *
         * 2018年11月15日
         *
         * By TerryQi
         */
        //如果是艺术榜
        if ($article->busi_name == Utils::BUSI_NAME_YSB) {
            //如果是审核类的
            if (array_key_exists('audit_status', $data) && !Utils::isObjNull($data['audit_status'])) {
                $audit_status = $data['audit_status'];
                YSBSendXCXTplMessageManager::sendAuditMessage($article, $audit_status);
            }
        }
        return ApiResponse::makeResponse(true, $article, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 编辑作品信息
     *
     * By TerryQi
     *
     * 2018-09-23
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

        $article = new Article();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $article = ArticleManager::getById($data['id']);
            $article = ArticleManager::getInfoByLevel($article, '0');
//            dd($article);
        }
        $article = ArticleManager::setInfo($article, $data);

        //生成管理员信息（此处作者信息为）
        $con_arr = array(
            'type' => '1',
            'status' => '1',
        );
        $admin_users = UserManager::getListByCon($con_arr, false);

        return view('admin.article.edit', ['admin' => $admin, 'data' => $article
            , 'upload_token' => $upload_token, 'item' => $item, 'admin_users' => $admin_users]);
    }


    /*
     * 添加、编辑作品信息
     *
     * By TerryQi
     *
     * 2018-09-23
     */
    public function editPost(Request $request)
    {
        $data = $request->all();

//        dd($data);

        $admin = $request->session()->get('admin');

        $article = new Article();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $article = ArticleManager::getById($data['id']);
        }
        $article = ArticleManager::setInfo($article, $data);
//        dd($article);
        $article->save();
        return ApiResponse::makeResponse(true, $article, ApiResponse::SUCCESS_CODE);

    }
}





