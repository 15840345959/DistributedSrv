<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API\YSBXCX;


use App\Components\ArticleManager;
use App\Components\RuleManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Components\YSB\YSBADManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\YSB\YSBUserManager;
use App\Http\Controllers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

//页面相关
class YSBPageController
{
    /*
     * 艺术榜页面相关
     *
     * By mtt
     *
     * 2018-09-27
     */
    public function index(Request $request)
    {
        $data = $request->all();
        //访问用户id
        $user_id = null;
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }
        //广告图信息
        $con_arr = array(
            'status' => '1',
        );
        $ysbADs = YSBADManager::getListByCon($con_arr, false);
        foreach ($ysbADs as $ysbAD) {
            unset($ysbAD->content_html);
        }
        $index_page['ads'] = $ysbADs;
        Utils::processLog(__METHOD__, '', " " . "ads");
        //推荐作品信息
        $recomm_articles = ArticleManager::getListByCon(['busi_name' => 'ysb', 'recomm_flag' => '1', 'status' => '1', 'audit_status' => '1'], true);
        foreach ($recomm_articles as $recomm_article) {
//            $recomm_article = ArticleManager::setRel($user_id, $recomm_article);
            $recomm_article = ArticleManager::getInfoByLevel($recomm_article, '');
        }
        Utils::processLog(__METHOD__, '', " " . "recomm_articles");
        $index_page['articles_r'] = $recomm_articles;
        //全部作品信息
        $all_articles = ArticleManager::getListByCon(['busi_name' => 'ysb', 'status' => '1', 'audit_status' => '1'], true);
        foreach ($all_articles as $all_article) {
//            $all_article = ArticleManager::setRel($user_id, $all_article);
            $all_article = ArticleManager::getInfoByLevel($all_article, '');
        }
        Utils::processLog(__METHOD__, '', " " . "all_articles");
        $index_page['articles_a'] = $all_articles;
        //规则信息
        $rule = RuleManager::getListByCon(['busi_name' => 'ysb', 'position' => '1', 'status' => '1'], false)->first();
        Utils::processLog(__METHOD__, '', " " . "rule");
        $index_page['rule'] = $rule;

        return ApiResponse::makeResponse(true, $index_page, ApiResponse::SUCCESS_CODE);
    }

}





