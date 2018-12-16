<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 11:32
 */

namespace App\Components;


use App\Components\Utils;
use App\Components\YSB\YSBSendXCXTplMessageManager;
use App\Models\Article;
use App\Models\TWStep;
use Illuminate\Support\Facades\Log;

class ArticleManager
{

    /*
     * 根据id获取轮播图信息
     *
     * By mtt
     *
     * 2018-4-9
     */
    public static function getById($id)
    {
        $info = Article::where('id', $id)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-06-14
     *
     * 0:带图文信息  1、带点赞明细 2、带收藏明细 3、带评论明细
     *
     */
    public static function getInfoByLevel($info, $level)
    {
        Utils::processLog(__METHOD__, '', json_encode($info));
        $info->user = UserManager::getById($info->user_id);
        //配置基本信息
        $info->status_str = Utils::ARTICLE_STAUS_VAL[$info->status];
        $info->pri_flag_str = Utils::ARTICLE_PRI_FLAG_VAL[$info->pri_flag];
        $info->allow_comment_flag_str = Utils::ARTICLE_ALLOW_COMMENT_FLAG_VAL[$info->allow_comment_flag];
        $info->ori_flag_str = Utils::ARTICLE_ORI_FLAG_VAL[$info->ori_flag];
        $info->apply_recomm_flag_str = Utils::ARTICLE_APPLY_RECOMM_FLAG_VAL[$info->apply_recomm_flag];
        $info->recomm_flag_str = Utils::ARTICLE_RECOMM_FLAG_VAL[$info->recomm_flag];
        $info->audit_status_str = Utils::ARTICLE_AUDIT_STATUS_VAL[$info->audit_status];
        //避免报错
        if (array_key_exists($info->busi_name, Utils::BUSI_NAME_VAL)) {
            $info->busi_name_str = Utils::BUSI_NAME_VAL[$info->busi_name];
        }
        $info->sys_flag_str = Utils::ARTICLE_SYS_FLAG_VAL[$info->sys_flag];

        //获取作品类型信息
        $info->article_type = ArticleTypeManager::getById($info->article_type_id);

        //0带图文性信息
        if (strpos($level, '0') !== false) {
            $con_arr = array(
                'f_id' => $info->id,
                'f_table' => 'article',
            );
            $info->twSteps = TWStepManager::getListByCon($con_arr, false);
        }
        //1带点赞明细
        if (strpos($level, '1') !== false) {
            $con_arr = array(
                'f_id' => $info->id,
                'f_table' => 'article',
            );
            $zans = ZanManager::getListByCon($con_arr, true);
            foreach ($zans as $zan) {
                $zan = ZanManager::getInfoByLevel($zan, 0);
            }
            $info->zans = $zans;
        }
        //2带收藏明细
        if (strpos($level, '2') !== false) {
            $con_arr = array(
                'f_id' => $info->id,
                'f_table' => 'article',
            );
            $collects = CollectManager::getListByCon($con_arr, true);
            foreach ($collects as $collect) {
                $collect = ZanManager::getInfoByLevel($collect, 0);
            }
            $info->collects = $collects;
        }
        //3带评论信息
        if (strpos($level, '3') !== false) {
            $con_arr = array(
                'f_id' => $info->id,
                'f_table' => 'article',
            );
            $comments = CommentManager::getListByCon($con_arr, true);
            foreach ($comments as $comment) {
                $comment = CommentManager::getInfoByLevel($comment, 0);
            }
            $info->comments = $comments;
        }

        return $info;
    }


    /*
     * 根据条件获取信息
     *
     * By mtt
     *
     * 2018-4-9
     */
    public static function getListByCon($con_arr, $is_paginate)
    {
        $infos = new Article();
        if (array_key_exists('search_word', $con_arr) && !Utils::isObjNull($con_arr['search_word'])) {
            $infos = $infos->where('name', 'like', '%' . $con_arr['search_word'] . '%');
        }
        if (array_key_exists('id', $con_arr) && !Utils::isObjNull($con_arr['id'])) {
            $infos = $infos->where('id', '=', $con_arr['id']);
        }
        if (array_key_exists('user_id', $con_arr) && !Utils::isObjNull($con_arr['user_id'])) {
            $infos = $infos->where('user_id', '=', $con_arr['user_id']);
        }
        if (array_key_exists('status', $con_arr) && !Utils::isObjNull($con_arr['status'])) {
            $infos = $infos->where('status', '=', $con_arr['status']);
        }
        if (array_key_exists('busi_name', $con_arr) && !Utils::isObjNull($con_arr['busi_name'])) {
            $infos = $infos->where('busi_name', '=', $con_arr['busi_name']);
        }
        if (array_key_exists('article_type_id', $con_arr) && !Utils::isObjNull($con_arr['article_type_id'])) {
            $infos = $infos->where('article_type_id', '=', $con_arr['article_type_id']);
        }
        if (array_key_exists('pri_flag', $con_arr) && !Utils::isObjNull($con_arr['pri_flag'])) {
            $infos = $infos->where('pri_flag', '=', $con_arr['pri_flag']);
        }
        if (array_key_exists('allow_comment_flag', $con_arr) && !Utils::isObjNull($con_arr['allow_comment_flag'])) {
            $infos = $infos->where('allow_comment_flag', '=', $con_arr['allow_comment_flag']);
        }
        if (array_key_exists('ori_flag', $con_arr) && !Utils::isObjNull($con_arr['ori_flag'])) {
            $infos = $infos->where('ori_flag', '=', $con_arr['ori_flag']);
        }
        if (array_key_exists('recomm_flag', $con_arr) && !Utils::isObjNull($con_arr['recomm_flag'])) {
            $infos = $infos->where('recomm_flag', '=', $con_arr['recomm_flag']);
        }
        if (array_key_exists('audit_status', $con_arr) && !Utils::isObjNull($con_arr['audit_status'])) {
            $infos = $infos->where('audit_status', '=', $con_arr['audit_status']);
        }
        if (array_key_exists('sys_flag', $con_arr) && !Utils::isObjNull($con_arr['sys_flag'])) {
            $infos = $infos->where('sys_flag', '=', $con_arr['sys_flag']);
        }

        $infos = $infos->orderby('seq', 'desc')->orderby('id', 'desc');

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
     * 随机获取作品信息
     *
     * By TerryQi
     *
     * 2018-11-05
     *
     * $con_arr为数组
     * num：为获取的随机条数
     */

    public static function getListByRand($con_arr)
    {
        $num = Utils::PAGE_SIZE;
        if (array_key_exists('num', $con_arr) && is_numeric($con_arr['num'])) {
            $num = $con_arr['num'];
        }
        $infos = new Article();

        $infos = $infos->orderBy(\DB::raw('RAND()'))
            ->take($num)
            ->get();

        return $infos;
    }


    /*
     * 文章与当前用户的相关信息
     *
     * By TerryQi
     *
     */
    public static function setRel($user_id, $article)
    {
        $article->is_zan = ZanManager::is_zan($user_id, Utils::F_TABLB_ARTICLE, $article->id);
        $article->is_collect = CollectManager::is_collect($user_id, Utils::F_TABLB_ARTICLE, $article->id);
        $article->is_comment = CommentManager::is_comment($user_id, Utils::F_TABLB_ARTICLE, $article->id);
        return $article;
    }


    /*
     * 配置作品，由于多处需要进行新建作品操作，所以将新建作品放置在
     *
     * By TerryQi
     *
     * 2018-08-17
     *
     * @data为图片详情的格式，通过setArticle来进行图文的编辑和创建
     *
     * 要求传入信息为 data为基本的article信息，twSteps为步骤信息，应该保留图文Step信息
     *
     */
    public static function setArticle($data)
    {
        $article = new Article();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $article = self::getById($data['id']);
        }
        //2018年11月15日增加逻辑
        /*
         * 先期如果是ysb项目，则上传作品先统一为未审核状态，然后10分钟后进行审核
         *
         * By TerryQi
         *
         */
        if ($data['busi_name'] == 'ysb') {
            $data['audit_status'] = "0";      //待审核状态
        }
        //新建作品信息
        $article = self::setInfo($article, $data);
        $article->save();
        //删除当前作品的图文信息
        $con_arr = array(
            'f_id' => $article->id,
            'f_table' => 'article'
        );
        TWStepManager::setCon($con_arr)->delete();
        //编辑新增的图文信息
        foreach ($data['twSteps'] as $data) {
            $twStep = new TWStep();
            $twStep->f_id = $article->id;
            $twStep->f_table = 'article';
            $twStep = TWStepManager::setInfo($twStep, $data);
            $twStep->save();
        }
        return $article;
    }

    /*
     * 配置信息
     *
     * By TerryQi
     *
     * 2018-06-11
     */
    public static function setInfo($info, $data)
    {
        if (array_key_exists('user_id', $data)) {
            $info->user_id = array_get($data, 'user_id');
        }
        if (array_key_exists('name', $data)) {
            $info->name = array_get($data, 'name');
        }
        if (array_key_exists('author', $data)) {
            $info->author = array_get($data, 'author');
        }
        if (array_key_exists('busi_name', $data)) {
            $info->busi_name = array_get($data, 'busi_name');
        }
        if (array_key_exists('desc', $data)) {
            $info->desc = array_get($data, 'desc');
        }
        if (array_key_exists('img', $data)) {
            $info->img = array_get($data, 'img');
        }
        if (array_key_exists('from_ori', $data)) {
            $info->from_ori = array_get($data, 'from_ori');
        }
        if (array_key_exists('show_num', $data)) {
            $info->show_num = array_get($data, 'show_num');
        }
        if (array_key_exists('comm_num', $data)) {
            $info->comm_num = array_get($data, 'comm_num');
        }
        if (array_key_exists('zan_num', $data)) {
            $info->zan_num = array_get($data, 'zan_num');
        }
        if (array_key_exists('coll_num', $data)) {
            $info->coll_num = array_get($data, 'coll_num');
        }
        if (array_key_exists('trans_num', $data)) {
            $info->trans_num = array_get($data, 'trans_num');
        }
        if (array_key_exists('content_html', $data)) {
            $info->content_html = array_get($data, 'content_html');
        }
        if (array_key_exists('video', $data)) {
            $info->video = array_get($data, 'video');
        }
        if (array_key_exists('step_info', $data)) {
            $info->step_info = array_get($data, 'step_info');
        }
        if (array_key_exists('seq', $data)) {
            $info->seq = array_get($data, 'seq');
        }
        if (array_key_exists('status', $data)) {
            $info->status = array_get($data, 'status');
        }
        if (array_key_exists('article_type_id', $data)) {
            $info->article_type_id = array_get($data, 'article_type_id');
        }
        if (array_key_exists('pri_flag', $data)) {
            $info->pri_flag = array_get($data, 'pri_flag');
        }
        if (array_key_exists('allow_comment_flag', $data)) {
            $info->allow_comment_flag = array_get($data, 'allow_comment_flag');
        }
        if (array_key_exists('ori_flag', $data)) {
            $info->ori_flag = array_get($data, 'ori_flag');
        }
        if (array_key_exists('apply_recomm_flag', $data)) {
            $info->apply_recomm_flag = array_get($data, 'apply_recomm_flag');
        }
        if (array_key_exists('recomm_flag', $data)) {
            $info->recomm_flag = array_get($data, 'recomm_flag');
        }
        if (array_key_exists('audit_status', $data)) {
            $info->audit_status = array_get($data, 'audit_status');
        }
        if (array_key_exists('sys_flag', $data)) {
            $info->sys_flag = array_get($data, 'sys_flag');
        }
        return $info;
    }

    /*
     * 增加数据
     *
     * By TerryQi
     *
     * 2018-07-18
     *
     * 增加统计数据 item统计项目 show_num：展示数目 comm_num：评论数目 zan_num：赞数 coll_num：收藏数 trans_num：转发数
     */
    public static function addStatistics($article_id, $item, $num)
    {
        $article = self::getById($article_id);
        switch ($item) {
            case "show_num":
                $article->show_num = $article->show_num + $num;
                break;
            case "comm_num":
                $article->comm_num = $article->comm_num + $num;
                break;
            case "zan_num":
                $article->zan_num = $article->zan_num + $num;
                break;
            case "coll_num":
                $article->coll_num = $article->coll_num + $num;
                break;
            case "trans_num":
                $article->trans_num = $article->trans_num + $num;
                break;
        }
        $article->save();
    }


    /*
     * 自动审核
     *
     * By TerryQi
     *
     * 2018-11-17
     *
     */
    public static function auditSchedule($account_config)
    {
        $con_arr = array(
            'audit_status' => '0',        //待审核
            'status' => '1',          //有效
        );

        $articles = self::getListByCon($con_arr, false);
        foreach ($articles as $article) {
            //再次查询，避免任务重复
            $article = self::getById($article->id);
            if ($article->audit_status == "0") {
                self::singleAudit($account_config, $article->id);
            }
        }
    }


    /*
     * 单个审核审核sechdule
     *
     * By TerryQi
     *
     * 2018-11-17
     */
    public static function singleAudit($account_config, $article_id)
    {
        $app = $app = app($account_config);
        $article = self::getById($article_id);
        $article = self::getInfoByLevel($article, '0');
        //组装text
        $text = $article->name . " " . $article->desc;
        foreach ($article->twSteps as $twStep) {
            $text = $text . " " . $twStep->text;
        }
        $security_flag = XCXContentSecurityManager::checkText($app, $text);
        //重新获取article
        $article = ArticleManager::getById($article->id);
        //审核通过
        if ($security_flag == true) {
            $article->audit_status = '1';
        } else {
            $article->audit_status = '0';
        }
        $article->save();
        //发送审核结果
        YSBSendXCXTplMessageManager::sendAuditMessage($article, $article->audit_status);
    }

}