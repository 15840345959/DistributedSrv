<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API;


use App\Components\ArticleManager;
use App\Components\CollectManager;
use App\Components\RequestValidator;
use App\Components\TWStepManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Article;
use App\Models\TWStep;
use App\Models\VisitHistory;
use App\Models\XCXForm;
use foo\bar;
use Illuminate\Http\Request;

class ArticleController
{
    /*
     * 编辑作品接口
     *
     * By mtt
     *
     * 2018-4-9
     */
    public function edit(Request $request)
    {
        $data = $request->all();

        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',            //用户id
            'busi_name' => 'required',
            'article' => 'required'             //作品的json结构体
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $data['article']['user_id'] = $data['user_id'];         //配置一下user_id
        $data['article']['busi_name'] = $data['busi_name'];         //配置一下busi_name
        //此处插入逻辑，如果使用canvas进行作画，则上传数据为img_base64编码，需要将该图片编码传入七牛并返回图片数据
        //纯base64字符串而不是以data开头的
//        if (array_key_exists('img_base64', $data) && !Utils::isObjNull($data['img_base64'])) {
//            $base64 = preg_replace("/\s/", '+', $data['img_base64']);
//        }
        $article = ArticleManager::setArticle($data['article']);

        //如果有form_id，则配置小程序服务消息
        if (array_key_exists('form_id', $data['article']) && !Utils::isObjNull($data['article']['form_id'])) {
            $xcxForm = new XCXForm();
            $xcxForm->user_id = $article->user_id;
            $xcxForm->busi_name = $article->busi_name;
            $xcxForm->form_id = $data['article']['form_id'];
            $xcxForm->total_num = 1;
            $xcxForm->f_table = Utils::F_TABLB_ARTICLE;
            $xcxForm->f_id = $article->id;
            $xcxForm->save();
        }

        return ApiResponse::makeResponse(true, $article, ApiResponse::SUCCESS_CODE);
    }

    /*
     * 删除作品接口
     *
     * By TerryQi
     *
     * 2018-11-02
     */
    public function delete(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $article_id = $data['id'];
        $user_id = $data['user_id'];
        //作品信息
        $article = ArticleManager::getById($article_id);
        if (!$article) {
            return ApiResponse::makeResponse(false, "作品不存在", ApiResponse::INNER_ERROR);
        }
        if ($article->user_id != $user_id) {
            return ApiResponse::makeResponse(false, "作品不归属该用户", ApiResponse::INNER_ERROR);
        }
        $article->delete();
        return ApiResponse::makeResponse(true, "删除成功", ApiResponse::SUCCESS_CODE);
    }


    /*
     * 根据id获取作品信息
     *
     * By TerryQi
     *
     * 2018-06-11
     */
    public function getById(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $level = "0";
        if (array_key_exists('level', $data) && !Utils::isObjNull($data['level'])) {
            $level = $data['level'];
        }
        $article = ArticleManager::getById($data['id']);
        //2018年11月21日日志排查结果，防止前端传入id为undefined的数据
        if (!$article) {
            return ApiResponse::makeResponse(false, "未找到作品", ApiResponse::INNER_ERROR);
        }
        $article = ArticleManager::getInfoByLevel($article, $level);
        //配置关系
        if (key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $article = ArticleManager::setRel($data['user_id'], $article);
        }
        //展示数+1
        ArticleManager::addStatistics($article->id, 'show_num', 1);

        //2018-11-30增加访问日志
        if (key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $visitHistory = new VisitHistory();
            $visitHistory->user_id = $data['user_id'];
            $visitHistory->f_table = Utils::F_TABLB_ARTICLE;
            $visitHistory->f_id = $article->id;
            $visitHistory->save();
        }

        return ApiResponse::makeResponse(true, $article, ApiResponse::SUCCESS_CODE);
    }

    /*
     * 根据条件获取列表信息
     *
     * By TerryQi
     *
     * 2018-06-14
     */
    public function getListByCon(Request $request)
    {
        $data = $request->all();
        //相关搜素条件
        $p_user_id = null;
        $busi_name = null;
        $search_word = null;
        $status = null;
        $article_type_id = null;
        $pri_flag = null;
        $allow_comment_flag = null;
        $ori_flag = null;
        $recomm_flag = null;


        //配置条件
        if (array_key_exists('p_user_id', $data) && !Utils::isObjNull($data['p_user_id'])) {
            $p_user_id = $data['p_user_id'];
        }
        if (array_key_exists('busi_name', $data) && !Utils::isObjNull($data['busi_name'])) {
            $busi_name = $data['busi_name'];
        }
        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }
        if (array_key_exists('status', $data) && !Utils::isObjNull($data['status'])) {
            $status = $data['status'];
        }
        if (array_key_exists('article_type_id', $data) && !Utils::isObjNull($data['article_type_id'])) {
            $article_type_id = $data['article_type_id'];
        }
        if (array_key_exists('pri_flag', $data) && !Utils::isObjNull($data['pri_flag'])) {
            $pri_flag = $data['pri_flag'];
        }
        if (array_key_exists('allow_comment_flag', $data) && !Utils::isObjNull($data['allow_comment_flag'])) {
            $allow_comment_flag = $data['allow_comment_flag'];
        }
        if (array_key_exists('ori_flag', $data) && !Utils::isObjNull($data['ori_flag'])) {
            $ori_flag = $data['ori_flag'];
        }
        if (array_key_exists('recomm_flag', $data) && !Utils::isObjNull($data['recomm_flag'])) {
            $recomm_flag = $data['recomm_flag'];
        }
        $con_arr = array(
            'user_id' => $p_user_id,
            'busi_name' => $busi_name,
            'search_word' => $search_word,
            'status' => '1',        //生效作品
            'aduit_status' => '1',      //审核通过
            'article_type_id' => $article_type_id,
            'pri_flag' => $pri_flag,
            'allow_comment_flag' => $allow_comment_flag,
            'ori_flag' => $ori_flag,
            'recomm_flag' => $recomm_flag,
            'sys_flag' => '0' //非系统预设
        );
        $articles = ArticleManager::getListByCon($con_arr, true);
        foreach ($articles as $article) {
            $article = ArticleManager::getInfoByLevel($article, '');
            unset($article->content_html);      //避免数据体量太大
            //根据当前的user_id获取信息
            if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
                $article = ArticleManager::setRel($data['user_id'], $article);
            }
        }
        return ApiResponse::makeResponse(true, $articles, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 随机获取文章
     *
     * By TerryQi
     *
     * 2018-11-05
     *
     * 该接口后续需要优化
     */
    public function getListByRand(Request $request)
    {
        $data = $request->all();
        //搜索条件
        $num = Utils::PAGE_SIZE;      //默认数据数为15条
        if (array_key_exists('num', $data) && is_numeric($data['num'])) {
            $num = $data['num'];
        }
        $con_arr = array(
            'num' => $num
        );
        $articles = ArticleManager::getListByRand($con_arr);
        foreach ($articles as $article) {
            $article = ArticleManager::getInfoByLevel($article, '');
            unset($article->content_html);      //避免数据体量太大
            //根据当前的user_id获取信息
            if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
                $article = ArticleManager::setRel($data['user_id'], $article);
            }
        }
        return ApiResponse::makeResponse(true, $articles, ApiResponse::SUCCESS_CODE);
    }

}





