<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//公众号  js安全域名校验文件/////////////////////////////////////////////////////////////////////////////////////////////////
Route::get('/MP_verify_u8o0o6vDsLXCjpty.txt', function () {
    return response()->download(realpath(base_path('app')) . '/files/MP_verify_u8o0o6vDsLXCjpty.txt', 'MP_verify_u8o0o6vDsLXCjpty.txt');
});

Route::get('/MP_verify_u8o0o6vDsLXCjpty.txt', function () {
    return response()->download(realpath(base_path('app')) . '/files/MP_verify_u8o0o6vDsLXCjpty.txt', 'MP_verify_u8o0o6vDsLXCjpty.txt');
});

//官网index
Route::any('/slb/test', function () {
    return "for slb test";
});        //重定向到官网在2018-12-21部署


//管理后台 start/////////////////////////////////////////////////////////////////////////////////////////////////

//登录
Route::get('/admin/login', 'Admin\LoginController@login');        //登录
Route::post('/admin/login', 'Admin\LoginController@loginPost');   //post登录请求
Route::get('/admin/loginout', 'Admin\LoginController@loginout');  //注销

Route::group(['prefix' => 'admin', 'middleware' => ['BeforeRequest', 'admin.checkLogin']], function () {

    //首页
    Route::get('/', 'Admin\IndexController@index');       //首页
    Route::get('/index', 'Admin\IndexController@index');  //首页

    //错误页面
    Route::get('/error/500', ['as' => 'error', 'uses' => 'Admin\IndexController@error']);  //错误页面

    //概览页面
    Route::get('/overview/index', ['as' => 'overview.index', 'uses' => 'Admin\OverviewController@index']);       //业务概览
    Route::get('/overview/activityStatus', ['as' => 'overview.activityStatus', 'uses' => 'Admin\OverviewController@activityStatus']);       //场次状态
    Route::get('/overview/income', ['as' => 'overview.income', 'uses' => 'Admin\OverviewController@income']);       //收入状态
    Route::get('/overview/newActivity', ['as' => 'overview.newActivity', 'uses' => 'Admin\OverviewController@newActivity']);       //新增场次状态
    Route::get('/overview/order', ['as' => 'overview.order', 'uses' => 'Admin\OverviewController@order']);       //订单数
    Route::get('/overview/vote', ['as' => 'overview.vote', 'uses' => 'Admin\OverviewController@vote']);       //投票数

    //管理员管理
    Route::any('/admin/index', 'Admin\AdminController@index');  //管理员管理首页
    Route::get('/admin/setStatus/{id}', 'Admin\AdminController@setStatus');  //设置管理员状态
    Route::get('/admin/edit', 'Admin\AdminController@edit');  //新建或编辑管理员
    Route::post('/admin/edit', 'Admin\AdminController@editPost');  //新建或编辑管理员
    Route::get('/admin/editMySelf', ['as' => 'editMySelf', 'uses' => 'Admin\AdminController@editMySelf']);  //修改个人资料get
    Route::post('/admin/editMySelf', 'Admin\AdminController@editMySelfPost');  //修改个人资料post

    //用户管理
    Route::any('/user/index', 'Admin\UserController@index');  //用户管理
    Route::get('/user/setStatus/{id}', 'Admin\UserController@setStatus');  //设置用户状态
    Route::get('/user/setType/{id}', 'Admin\UserController@setType');  //设置用户类型
    Route::get('/user/info', 'Admin\UserController@info');  //用户详情
    Route::any('/user/edit', 'Admin\UserController@edit');  //编辑用户信息

    //用户关系明细
    Route::any('/userRel/index', 'Admin\UserRelController@index');  //用户关系明细

    //用户关注
    Route::any('/guanzhu/index', 'Admin\GuanZhuController@index');  //用户管理

    //轮播图管理
    Route::any('/ad/index', 'Admin\ADController@index');  //轮播图管理
    Route::get('/ad/edit', 'Admin\ADController@edit');  //轮播图管理添加、编辑-get
    Route::post('/ad/edit', 'Admin\ADController@editPost');  //轮播图管理添加、编辑-post
    Route::get('/ad/setStatus/{id}', 'Admin\ADController@setStatus');  //设置轮播图状态
    Route::get('/ad/del/{id}', 'Admin\ADController@del');  //删除广告图

    //作品管理
    Route::any('/article/index', 'Admin\ArticleController@index');  //作品管理首页
    Route::get('/article/setStatus/{id}', 'Admin\ArticleController@setStatus');  //设置作品状态
    Route::get('/article/edit', 'Admin\ArticleController@edit');  //作品管理添加、编辑-get
    Route::post('/article/edit', 'Admin\ArticleController@editPost');  //作品管理添加、编辑-post

    //作品类型
    Route::any('/articleType/index', ['as' => 'articleType.index', 'uses' => 'Admin\ArticleTypeController@index']);  //作品类型管理首页
    Route::any('/articleType/edit', ['as' => 'articleType.edit', 'uses' => 'Admin\ArticleTypeController@edit']);  //作品类型
    Route::get('/articleType/setStatus/{id}', 'Admin\ArticleTypeController@setStatus');  //设置作品标签状态

    //商品
    Route::any('/goods/index', ['as' => 'goods.index', 'uses' => 'Admin\GoodsController@index']);  //商品管理首页
    Route::get('/goods/setStatus/{id}', 'Admin\GoodsController@setStatus');  //设置商品标签状态
    Route::get('/goods/edit', ['as' => 'goods.edit', 'uses' => 'Admin\GoodsController@edit']);  //商品
    Route::post('/goods/edit', 'Admin\GoodsController@editPost');  //商品管理添加、编辑-post

    //商品价格
    Route::any('/goodsPrice/index', ['as' => 'goodsPrice.index', 'uses' => 'Admin\GoodsPriceController@index']);  //商品价格管理首页
    Route::get('/goodsPrice/setStatus/{id}', 'Admin\GoodsPriceController@setStatus');  //设置商品价格标签状态
    Route::get('/goodsPrice/edit', ['as' => 'goodsPrice.edit', 'uses' => 'Admin\GoodsPriceController@edit']);  //商品价格
    Route::post('/goodsPrice/edit', 'Admin\GoodsPriceController@editPost');  //商品价格管理添加、编辑-post

    //物流价格
    Route::any('/logisticsSetting/index', ['as' => 'logisticsSetting.index', 'uses' => 'Admin\LogisticsSettingController@index']);  //物流价格管理首页
    Route::get('/logisticsSetting/setStatus/{id}', 'Admin\LogisticsSettingController@setStatus');  //设置物流价格标签状态
    Route::get('/logisticsSetting/edit', ['as' => 'logisticsSetting.edit', 'uses' => 'Admin\LogisticsSettingController@edit']);  //物流价格
    Route::post('/logisticsSetting/edit', 'Admin\LogisticsSettingController@editPost');  //物流价格管理添加、编辑-post


    //商品类型
    Route::any('/goodsType/index', ['as' => 'goodsType.index', 'uses' => 'Admin\GoodsTypeController@index']);  //商品类型管理首页
    Route::any('/goodsType/edit', ['as' => 'goodsType.edit', 'uses' => 'Admin\GoodsTypeController@edit']);  //商品类型
    Route::get('/goodsType/setStatus/{id}', 'Admin\GoodsTypeController@setStatus');  //设置商品标签状态

    //操作记录
    Route::any('/optRecord/edit', ['as' => 'optRecord.edit', 'uses' => 'Admin\OptRecordController@edit']);  //操作记录

    //操作动作
    Route::any('/optInfo/index', ['as' => 'optInfo.index', 'uses' => 'Admin\OptInfoController@index']);  //操作动作管理首页
    Route::any('/optInfo/edit', ['as' => 'optInfo.edit', 'uses' => 'Admin\OptInfoController@edit']);  //操作动作

    //点赞、评论、收藏
    Route::any('/zan/index', 'Admin\ZanController@index');  //点赞首页
    Route::any('/comment/index', 'Admin\CommentController@index');  //评论首页
    Route::get('/comment/setStatus/{id}', 'Admin\CommentController@setStatus');  //设置评论状态
    Route::any('/collect/index', 'Admin\CollectController@index');  //收藏首页

    //意见反馈
    Route::any('/feedback/index', 'Admin\FeedBackController@index');  //意见反馈

    //规则管理
    Route::any('/rule/index', 'Admin\RuleController@index');  //规则管理
    Route::get('/rule/edit', 'Admin\RuleController@edit');  //规则管理添加、编辑-get
    Route::post('/rule/edit', 'Admin\RuleController@editPost');  //规则管理添加、编辑-post
    Route::get('/rule/setStatus/{id}', 'Admin\RuleController@setStatus');  //设置规则状态

    // 2B业务
    Route::prefix('b')->group(function () {
        // 消息通知
        Route::prefix('message')->group(function () {
            // 首页
            Route::any('index', 'Admin\B\BMessageController@index')->name('admin.b.message.index');
            // 编辑
            Route::any('edit', 'Admin\B\BMessageController@edit')->name('admin.b.message.edit');
            //设置状态
            Route::get('setStatus', 'Admin\B\BMessageController@setStatus')->name('admin.b.message.setStatus');
        });
    });


    /******公众号相关****************/

    Route::group(['prefix' => 'gzh', 'middleware' => []], function () {

        //公众号素材管理
        Route::any('/material/index', 'Admin\GZH\MaterialController@index');  //公众号素材管理
        Route::get('/material/edit', 'Admin\GZH\MaterialController@edit');  //公众号素材管理添加、编辑-get
        Route::post('/material/edit', 'Admin\GZH\MaterialController@editPost');  //公众号素材管理添加、编辑-post
        Route::get('/material/del/{id}', 'Admin\GZH\MaterialController@del');  //删除公众号素材

        //公众号自动回复管理
        Route::any('/reply/index', 'Admin\GZH\ReplyController@index');  //公众号自动回复管理
        Route::get('/reply/edit', 'Admin\GZH\ReplyController@edit');  //公众号自动回复管理添加、编辑-get
        Route::post('/reply/edit', 'Admin\GZH\ReplyController@editPost');  //公众号自动回复管理添加、编辑-post
        Route::get('/reply/del/{id}', 'Admin\GZH\ReplyController@del');  //删除公众号自动回复

        //公众号业务话术管理
        Route::any('/busiWord/index', 'Admin\GZH\BusiWordController@index');  //公众号业务话术管理
        Route::get('/busiWord/edit', 'Admin\GZH\BusiWordController@edit');  //公众号业务话术管理添加、编辑-get
        Route::post('/busiWord/edit', 'Admin\GZH\BusiWordController@editPost');  //公众号业务话术管理添加、编辑-post
        Route::get('/busiWord/del/{id}', 'Admin\GZH\BusiWordController@del');  //删除公众号业务话术

        //公众号定向消息管理
        Route::any('/directMessage/index', 'Admin\GZH\DirectMessageController@index');  //公众号定向消息管理
        Route::get('/directMessage/edit', 'Admin\GZH\DirectMessageController@edit');  //公众号定向消息管理添加、编辑-get
        Route::post('/directMessage/edit', 'Admin\GZH\DirectMessageController@editPost');  //公众号定向消息管理添加、编辑-post

        //公众号菜单管理
        Route::any('/menu/index', 'Admin\GZH\MenuController@index');  //公众号菜单管理
        Route::get('/menu/edit', 'Admin\GZH\MenuController@edit');  //公众号菜单管理添加、编辑-get
        Route::post('/menu/edit', 'Admin\GZH\MenuController@editPost');  //公众号菜单管理添加、编辑-post
        Route::get('/menu/del/{id}', 'Admin\GZH\MenuController@del');  //删除公众号菜单
        Route::get('/menu/create', 'Admin\GZH\MenuController@create');  //创建公众号菜单

        //配置信息项目
        Route::get('/base/isart/info', 'Admin\ISART\ISARTController@info');  //ISART公众号配置信息

    });

    /******投票活动管理****************/

    Route::group(['prefix' => 'vote', 'middleware' => []], function () {
        //投票礼品管理
        Route::any('/voteGift/index', 'Admin\Vote\VoteGiftController@index');  //投票礼品管理
        Route::get('/voteGift/edit', 'Admin\Vote\VoteGiftController@edit');  //投票礼品管理添加、编辑-get
        Route::post('/voteGift/edit', 'Admin\Vote\VoteGiftController@editPost');  //投票礼品管理添加、编辑-post
        Route::get('/voteGift/setStatus/{id}', 'Admin\Vote\VoteGiftController@setStatus');  //设置投票礼品状态

        //投票活动管理
        Route::any('/voteActivity/index', ['as' => 'voteActivity.index', 'uses' => 'Admin\Vote\VoteActivityController@index']);  //投票活动管理
        Route::get('/voteActivity/edit', 'Admin\Vote\VoteActivityController@edit');  //投票活动管理添加、编辑-get
        Route::post('/voteActivity/edit', 'Admin\Vote\VoteActivityController@editPost');  //投票活动管理添加、编辑-post
        Route::get('/voteActivity/setStatus/{id}', 'Admin\Vote\VoteActivityController@setStatus');  //设置投票活动状态
        Route::get('/voteActivity/copy', 'Admin\Vote\VoteActivityController@copy');  //复制活动
        Route::get('/voteActivity/settle', ['as' => 'voteActivity.settle', 'uses' => 'Admin\Vote\VoteActivityController@settle']);  //地推团队结算
        Route::get('/voteActivity/prizeStatements/', ['as' => 'voteActivity.prizeStatements', 'uses' => 'Admin\Vote\VoteActivityController@prizeStatements']);  //地推团队结算

        Route::get('/voteActivity/importVoteUser', 'Admin\Vote\VoteUserController@importVoteUser');  //导入选手-get
        Route::post('/voteActivity/importVoteUser', 'Admin\Vote\VoteUserController@importVoteUserPost');  //导入选手-post

        Route::get('/voteActivity/importVoteUserVideo', 'Admin\Vote\VoteUserController@importVoteUserVideo');  //导入选手-get
        Route::post('/voteActivity/importVoteUserVideoPost', 'Admin\Vote\VoteUserController@importVoteUserVideoPost');  //导入选手-post
        Route::get('/voteActivity/qrcode/{id}', ['as' => 'voteActivity.qrcode', 'uses' => 'Admin\Vote\VoteActivityController@qrcode']);  //查看大赛二维码

        Route::get('/voteActivity/info', 'Admin\Vote\VoteActivityController@info');  //投票活动综合统计
        Route::any('/voteActivity/info/vote_money', 'Admin\Vote\VoteActivityController@vote_money');  //活动明细-投票收入趋势
        Route::any('/voteActivity/info/order', 'Admin\Vote\VoteActivityController@order');  //活动明细-总订单、支付成功订单

        //投票广告管理
        Route::any('/voteAD/index', 'Admin\Vote\VoteADController@index');  //投票广告管理
        Route::get('/voteAD/edit', 'Admin\Vote\VoteADController@edit');  //投票广告管理添加、编辑-get
        Route::post('/voteAD/edit', 'Admin\Vote\VoteADController@editPost');  //投票广告管理添加、编辑-post
        Route::get('/voteAD/setStatus/{id}', 'Admin\Vote\VoteADController@setStatus');  //设置投票广告状态

        //地推团队管理
        Route::any('/voteTeam/index', 'Admin\Vote\VoteTeamController@index');  //地推团队管理
        Route::get('/voteTeam/edit', 'Admin\Vote\VoteTeamController@edit');  //地推团队管理添加、编辑-get
        Route::post('/voteTeam/edit', 'Admin\Vote\VoteTeamController@editPost');  //地推团队管理添加、编辑-post
        Route::get('/voteTeam/setStatus/{id}', 'Admin\Vote\VoteTeamController@setStatus');  //设置地推团队状态
        Route::get('/voteTeam/qrcode/{id}', ['as' => 'team.qrcode', 'uses' => 'Admin\Vote\VoteTeamController@qrcode']);  //查看地推团队二维码
        Route::any('/voteTeam/info', 'Admin\Vote\VoteTeamController@info');  //地推团队详情

        //参赛选手管理
        Route::any('/voteUser/index', 'Admin\Vote\VoteUserController@index');  //参赛选手管理
        Route::get('/voteUser/edit', 'Admin\Vote\VoteUserController@edit');  //参赛选手管理添加、编辑-get
        Route::post('/voteUser/edit', 'Admin\Vote\VoteUserController@editPost');  //参赛选手管理添加、编辑-post
        Route::get('/voteUser/setStatus/{id}', 'Admin\Vote\VoteUserController@setStatus');  //设置参赛选手状态
        Route::get('/voteUser/setAuditStatus/{id}', 'Admin\Vote\VoteUserController@setAuditStatus');  //审核参赛选手
        Route::get('/voteUser/info', 'Admin\Vote\VoteUserController@info');  //参赛选手信息
        Route::get('/voteUser/qrcode/{id}', ['as' => 'voteUser.qrcode', 'uses' => 'Admin\Vote\VoteUserController@qrcode']);  //查看选手二维码


        //投诉管理
        Route::any('/voteComplain/index', 'Admin\Vote\VoteComplainController@index');  //投诉管理
        Route::get('/voteComplain/setStatus/{id}', 'Admin\Vote\VoteComplainController@setStatus');  //设置投诉状态

        //证书下发
        Route::any('/voteCertSend/index', 'Admin\Vote\VoteCertSendController@index');  //证书下发

        //投票明细管理
        Route::any('/voteRecord/index', 'Admin\Vote\VoteRecordController@index');  //投票明细管理

        //订单明细
        Route::any('/voteOrder/index', 'Admin\Vote\VoteOrderController@index');  //订单明细管理
        Route::get('/voteOrder/setStatus/{id}', 'Admin\Vote\VoteOrderController@setStatus');  //设置订单状态

        //分享明细管理
        Route::any('/voteShareRecord/index', 'Admin\Vote\VoteShareRecordController@index');  //分享明细管理

        //关注明细管理
        Route::any('/voteGuanZhu/index', 'Admin\Vote\VoteGuanZhuController@index');  //关注明细管理

        //大赛规则管理
        Route::any('/voteRule/index', 'Admin\Vote\VoteRuleController@index');  //大赛规则管理
        Route::get('/voteRule/edit', 'Admin\Vote\VoteRuleController@edit');  //大赛规则管理添加、编辑-get
        Route::post('/voteRule/edit', 'Admin\Vote\VoteRuleController@editPost');  //大赛规则管理添加、编辑-post
        Route::get('/voteRule/setStatus/{id}', 'Admin\Vote\VoteRuleController@setStatus');  //设置大赛规则状态

        //综合管理
        Route::any('/voteStmt/daily', 'Admin\Vote\VoteStmtController@daily');  //日报

    });


    /******每日一画管理****************/

    Route::group(['prefix' => 'mryh', 'middleware' => []], function () {

        //业务概览
        Route::any('/mryhOverview/index', 'Admin\Mryh\MryhOverviewController@index');  //业务概览
        Route::any('/mryhOverview/user', 'Admin\Mryh\MryhOverviewController@user');  //用户趋势图
        Route::any('/mryhOverview/join_article', 'Admin\Mryh\MryhOverviewController@join_article');  //参赛和作品趋势图
        Route::any('/mryhOverview/withdraw_failed', 'Admin\Mryh\MryhOverviewController@withdraw_failed');  //提现成功及失败
        Route::any('/mryhOverview/new_refund_joinOrder', 'Admin\Mryh\MryhOverviewController@new_refund_joinOrder');  //参赛押金及退款金额

        //广告管理
        Route::any('/mryhAD/index', 'Admin\Mryh\MryhADController@index');  //广告管理
        Route::get('/mryhAD/edit', 'Admin\Mryh\MryhADController@edit');  //广告管理添加、编辑-get
        Route::post('/mryhAD/edit', 'Admin\Mryh\MryhADController@editPost');  //广告管理添加、编辑-post
        Route::get('/mryhAD/setStatus/{id}', 'Admin\Mryh\MryhADController@setStatus');  //设置广告状态

        //配置管理
        Route::any('/mryhSetting/index', 'Admin\Mryh\MryhSettingController@index');  //配置管理
        Route::get('/mryhSetting/edit', 'Admin\Mryh\MryhSettingController@edit');  //配置管理添加、编辑-get
        Route::post('/mryhSetting/edit', 'Admin\Mryh\MryhSettingController@editPost');  //配置管理添加、编辑-post
        Route::get('/mryhSetting/setStatus/{id}', 'Admin\Mryh\MryhSettingController@setStatus');  //设置配置状态

        //用户管理
        Route::any('/mryhUser/index', 'Admin\Mryh\MryhUserController@index');  //用户列表

        //活动管理
        Route::any('/mryhGame/index', 'Admin\Mryh\MryhGameController@index');  //活动管理
        Route::get('/mryhGame/edit', 'Admin\Mryh\MryhGameController@edit');  //活动管理添加、编辑-get
        Route::post('/mryhGame/edit', 'Admin\Mryh\MryhGameController@editPost');  //活动管理添加、编辑-post
        Route::get('/mryhGame/setStatus/{id}', 'Admin\Mryh\MryhGameController@setStatus');  //设置活动状态
        Route::get('/mryhGame/copy', 'Admin\Mryh\MryhGameController@copy');  //复制活动

        //优惠券管理
        Route::any('/mryhCoupon/index', 'Admin\Mryh\MryhCouponController@index');  //优惠券管理
        Route::get('/mryhCoupon/edit', 'Admin\Mryh\MryhCouponController@edit');  //优惠券管理添加、编辑-get
        Route::post('/mryhCoupon/edit', 'Admin\Mryh\MryhCouponController@editPost');  //优惠券管理添加、编辑-post
        Route::get('/mryhCoupon/setStatus/{id}', 'Admin\Mryh\MryhCouponController@setStatus');  //设置优惠券状态

        //优惠券派发明细
        Route::any('/mryhUserCoupon/index', 'Admin\Mryh\MryhUserCouponController@index');  //订单明细管理

        //订单明细
        Route::any('/mryhJoinOrder/index', 'Admin\Mryh\MryhJoinOrderController@index');  //订单明细管理

        //参赛明细
        Route::any('/mryhJoin/index', 'Admin\Mryh\MryhJoinController@index');  //参赛明细

        //上传作品明细
        Route::any('/mryhJoinArticle/index', 'Admin\Mryh\MryhJoinArticleController@index');  //参赛明细

        //提现明细
        Route::any('/mryhWithdrawCash/index', 'Admin\Mryh\MryhWithdrawCashController@index');  //提现明细
        Route::get('/mryhWithdrawCash/info', 'Admin\Mryh\MryhWithdrawCashController@info');  //提现详情

        //清分明细
        Route::any('/mryhComputePrize/index', 'Admin\Mryh\MryhComputePrizeController@index');

    });

    /******艺术榜管理****************/

    Route::group(['prefix' => 'ysb', 'middleware' => []], function () {

        //广告管理
        Route::any('/ysbAD/index', 'Admin\YSB\YSBADController@index');  //广告管理
        Route::get('/ysbAD/edit', 'Admin\YSB\YSBADController@edit');  //广告管理添加、编辑-get
        Route::post('/ysbAD/edit', 'Admin\YSB\YSBADController@editPost');  //广告管理添加、编辑-post
        Route::get('/ysbAD/setStatus/{id}', 'Admin\YSB\YSBADController@setStatus');  //设置广告状态

        //用户管理
        Route::any('/ysbUser/index', 'Admin\YSB\YSBUserController@index');  //用户管理

    });

    /******小艺商城管理****************/

    Route::group(['prefix' => 'shop', 'middleware' => []], function () {

        //广告管理
        Route::any('/shopAD/index', 'Admin\Shop\ShopADController@index');  //广告管理
        Route::get('/shopAD/edit', 'Admin\Shop\ShopADController@edit');  //广告管理添加、编辑-get
        Route::post('/shopAD/edit', 'Admin\Shop\ShopADController@editPost');  //广告管理添加、编辑-post
        Route::get('/shopAD/setStatus/{id}', 'Admin\Shop\ShopADController@setStatus');  //设置广告状态

        //用户管理
        Route::any('/shopUser/index', 'Admin\Shop\ShopUserController@index');  //用户管理

        //订单明细
        Route::any('/shopOrder/index', 'Admin\Shop\ShopOrderController@index');  //订单明细管理
        Route::get('/shopOrder/info', 'Admin\Shop\ShopOrderController@info');  //订单详情

    });

    /******营销活动管理****************/
    Route::group(['prefix' => 'yxhd', 'middleware' => []], function () {

        //活动管理
        Route::any('/yxhdActivity/index', 'Admin\Yxhd\YxhdActivityController@index');  //营销活动首页
        Route::get('/yxhdActivity/edit', 'Admin\Yxhd\YxhdActivityController@edit');  //活动管理添加、编辑-get
        Route::post('/yxhdActivity/edit', 'Admin\Yxhd\YxhdActivityController@editPost');  //活动管理添加、编辑-post
        Route::get('/yxhdActivity/setStatus/{id}', 'Admin\Yxhd\YxhdActivityController@setStatus');  //设置活动状态

        //奖品管理
        Route::any('/yxhdPrize/index', 'Admin\Yxhd\YxhdPrizeController@index');  //营销奖品首页
        Route::get('/yxhdPrize/edit', 'Admin\Yxhd\YxhdPrizeController@edit');  //奖品管理添加、编辑-get
        Route::post('/yxhdPrize/edit', 'Admin\Yxhd\YxhdPrizeController@editPost');  //奖品管理添加、编辑-post
        Route::get('/yxhdPrize/setStatus/{id}', 'Admin\Yxhd\YxhdPrizeController@setStatus');  //设置奖品状态

        //奖品配置
        Route::get('/yxhdPrizeSetting/edit', 'Admin\Yxhd\YxhdPrizeSettingController@edit');  //奖品管理添加、编辑-get
        Route::post('/yxhdPrizeSetting/edit', 'Admin\Yxhd\YxhdPrizeSettingController@editPost');  //奖品管理添加、编辑-post
        Route::get('/yxhdPrizeSetting/del/{id}', 'Admin\Yxhd\YxhdPrizeSettingController@del');  //删除奖品


        //订单记录信息
        Route::any('/yxhdOrder/index', 'Admin\Yxhd\YxhdOrderController@index');  //营销订单信息


    });

});

// 地推团队
Route::prefix('team')->middleware(['BeforeRequest'])->group(function () {
    // 登陆
    Route::any('login', 'Vote\Team\LoginController@login')->name('team.login');
    // 注销
    Route::get('loginout', 'Vote\Team\LoginController@loginout')->name('team.loginout');

    Route::prefix('error')->group(function () {
        Route::get('500', 'Vote\Team\IndexController@error500')->name('team.error.500');
    });

    Route::middleware(['team.checkLogin'])->group(function () {
        // 首页
        Route::get('/', 'Vote\Team\IndexController@index')->name('team');
        // 首页
        Route::get('index', 'Vote\Team\IndexController@index')->name('team.index');

        // 业务概览
        Route::prefix('overview')->group(function () {
            // 首页
            Route::get('index', 'Vote\Team\OverviewController@index')->name('team.overview.index');
            Route::any('vote_money', 'Vote\Team\OverviewController@vote_money');  //活动明细-投票收入趋势
            Route::any('order', 'Vote\Team\OverviewController@order');  //活动明细-总订单、支付成功订单
            Route::any('activity_trend', 'Vote\Team\OverviewController@activity_trend');  //活动趋势-新增、结束

        });

        // 消息管理
        Route::prefix('message')->group(function () {
            // 首页
            Route::any('index', 'Vote\Team\MessageController@index')->name('team.message.index');
            Route::any('info', 'Vote\Team\MessageController@info')->name('team.message.info');

        });
        // 投票大赛
        Route::prefix('activity')->group(function () {
            // 首页
            Route::get('index', 'Vote\Team\ActivityController@index')->name('team.activity.index');
            // 编辑
            Route::any('edit', 'Vote\Team\ActivityController@edit')->name('team.activity.edit');
            // 设置状态
            Route::get('setActivityStatus', 'Vote\Team\ActivityController@setActivityStatus')->name('team.activity.setActivityStatus');
            // 复制
            Route::get('copy', 'Vote\Team\ActivityController@copy')->name('team.activity.copy');
            // 参赛选手管理添加、编辑
            Route::any('editVoteUser', 'Vote\Team\ActivityController@editVoteUser')->name('team.activity.editVoteUser');
            // 导入选手照片
            Route::any('importVoteUser', 'Vote\Team\ActivityController@importVoteUser')->name('team.activity.importVoteUser');
            // 导入选手视频
            Route::any('importVoteUserVideo', 'Vote\Team\ActivityController@importVoteUserVideo')->name('team.activity.importVoteUserVideo');
            // 设置参赛选手状态
            Route::get('setVoteUserStatus', 'Vote\Team\ActivityController@setVoteUserStatus')->name('team.activity.setVoteUserStatus');
            // 审核参赛选手
            Route::get('setVoteUserAuditStatus', 'Vote\Team\ActivityController@setVoteUserAuditStatus')->name('team.activity.setVoteUserAuditStatus');
            // 参赛选手信息
            Route::get('voteUserInfo', 'Vote\Team\ActivityController@voteUserInfo')->name('team.activity.voteUserInfo');
            // 投票明细
            Route::any('voteRecord', 'Vote\Team\ActivityController@voteRecord')->name('team.activity.voteRecord');
            // 分享明细
            Route::any('voteShareRecord', 'Vote\Team\ActivityController@voteShareRecord')->name('team.activity.voteShareRecord');
            // 关注明细
            Route::any('voteFollowRecord', 'Vote\Team\ActivityController@voteFollowRecord')->name('team.activity.voteFollowRecord');
            // 获奖名单
            Route::any('prizeStatements', 'Vote\Team\ActivityController@prizeStatements')->name('team.activity.prizeStatements');
        });
        // 选手审核
        Route::prefix('voteUser')->group(function () {
            // 首页
            Route::any('index', 'Vote\Team\VoteUserController@index')->name('team.voteUser.index');

        });
    });
});

//管理后台 end/////////////////////////////////////////////////////////////////////////////////////////////////

//投票前端页面 start////////////////////////////////////////////////////////////////////////////////////////////////
//本地测试不加路由'wechat.oauth'    'wechat.oauth',
Route::group(['prefix' => 'vote', 'middleware' => ['BeforeRequest', 'wechat.oauth']], function () {

    Route::get('/error', 'Vote\Html5\IndexController@error');        //投票大赛错误页面

    //页面跳转类
    Route::get('/index', 'Vote\Html5\IndexController@index');        //投票大赛首页
    Route::get('/zone', 'Vote\Html5\IndexController@zone');        //赛区列表页面
    Route::get('/person', 'Vote\Html5\IndexController@person');        //个人主页
    Route::get('/guanzhu', 'Vote\Html5\IndexController@guanzhu');        //关注成功
    Route::get('/sendCert', 'Vote\Html5\IndexController@sendCert');        //获取证书页面
    Route::get('/present', 'Vote\Html5\IndexController@present');        //奖品页面
    Route::get('/intro', 'Vote\Html5\IndexController@intro');        //大赛说明
    Route::get('/list', 'Vote\Html5\IndexController@list');        //排名列表页面
    Route::get('/complain', 'Vote\Html5\IndexController@complain');        //举报投诉页面
    Route::get('/apply', 'Vote\Html5\IndexController@apply');        //报名页面
    Route::get('/team', ['as' => 'vote.team', 'uses' => 'Vote\Html5\IndexController@team']);  //查看地推团队场次

    //post表单提交类
    Route::any('/api/complain', 'Vote\Html5\APIController@complain');        //举报接口
    Route::any('/api/vote', 'Vote\Html5\APIController@vote');        //投票接口
    Route::any('/api/shareVoteUser', 'Vote\Html5\APIController@shareVoteUser');        //分享参赛选手计数
    Route::any('/api/apply', 'Vote\Html5\APIController@apply');        //报名信息
    Route::any('/api/voteUser/guanzhu', 'Vote\Html5\APIController@guanzhu');        //关注信息
    Route::any('/api/vote/payOrder', 'Vote\Html5\APIController@payOrder');        //礼品下单
    Route::any('/api/vote/teamActivity', ['as' => 'vote.team.activity', 'uses' => 'Vote\Html5\APIController@getTeamActivityByType']);        //礼品下单
});

//投票大赛不过中间件
//
//
//Route::group(['prefix' => 'vote', 'middleware' => []], function () {
//
//    Route::get('/personShare', 'Vote\Html5\IndexController@personShare');        //分享用个人主页
//
//
//});
//投票前端页面 end////////////////////////////////////////////////////////////////////////////////////////////


//小艺商城/////////////////////////////////////////////////////////////////////////////////////////////
//本地测试不加路由'wechat.oauth'    'wechat.oauth',
Route::group(['prefix' => 'shop', 'middleware' => ['BeforeRequest', 'wechat.oauth']], function () {

    Route::get('/index', 'IsartShop\Html5\IndexController@index');        //小艺商城的首页

});
//////////////////////////////////////////////////////////////////////////////////////////////////////

//简笔画板 start////////////////////////////////////////////////////////////////////////////////////////////////
Route::group(['prefix' => 'draw', 'middleware' => ['BeforeRequest']], function () {

    Route::get('/canvas', 'Draw\Html5\IndexController@canvas');        //简笔画板

});


//简笔画板 end////////////////////////////////////////////////////////////////////////////////////////////


//测试用 start////////////////////////////////////////////////////////////////////////////////////////////////
Route::group(['prefix' => 'test', 'middleware' => ['BeforeRequest']], function () {
    Route::get('/xcx/map', 'Test\TestController@map');        //小程序map
});

//测试用 end////////////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////////////////////////////////////









