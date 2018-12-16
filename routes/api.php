<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


//通用接口
Route::group(['prefix' => '', 'middleware' => ['BeforeRequest', 'cors']], function () {

    //测试接口
    Route::get('test', 'API\TestController@test');
    Route::get('test/jiami', 'API\TestController@jiami');       //加密
    Route::get('test/jiemi', 'API\TestController@jiemi');       //解密
    Route::get('test/vote/cert', 'API\TestController@voteCert');       //生成证书
    Route::get('test/vote/sendCert', 'API\TestController@sendCert');       //发送证书
    Route::get('test/vote/sendCertSchedule', 'API\TestController@sendCertSchedule');       //发送证书-计划任务
    Route::post('/test/wxConfig', 'Test\TestController@wxConfig');        //获取微信公众号wxconfig信息

    //获取七牛token
    Route::get('user/getQiniuToken', 'API\UserController@getQiniuToken');

    //用户相关
    Route::get('user/getById', 'API\UserController@getById');          //根据id获取用户信息
    Route::get('user/getByIdWithToken', 'API\UserController@getByIdWithToken')->middleware('user.checkToken');   //根据id获取用户信息带token
    Route::post('user/updateById', 'API\UserController@updateById')->middleware('user.checkToken'); //更新用户信息

    //用户关系
    Route::post('userRel/add', 'API\UserRelController@add')->middleware('user.checkToken'); //建立用户关联关系

    //登录注册相关
    Route::post('user/login', 'API\LoginController@login'); //登录

    //关注相关
    Route::post('guanzhu/setGuanZhu', 'API\GuanZhuController@setGuanZhu')->middleware('user.checkToken'); //进行关注设置
    Route::get('guanzhu/getListByCon', 'API\GuanZhuController@getListByCon'); //根据条件获取关注列表
    Route::post('guanzhu/getRel', 'API\GuanZhuController@getRel')->middleware('user.checkToken'); //获取关注关系

    //广告轮播图
    Route::get('ad/getById', 'API\ADController@getById');
    Route::get('ad/getListByCon', 'API\ADController@getListByCon');

    //作品相关接口
    Route::post('article/edit', 'API\ArticleController@edit')->middleware('user.checkToken');
    Route::post('article/delete', 'API\ArticleController@delete')->middleware('user.checkToken');
    Route::get('article/getListByCon', 'API\ArticleController@getListByCon');
    Route::get('article/getListByRand', 'API\ArticleController@getListByRand');
    Route::get('article/getById', 'API\ArticleController@getById');

    //作品类别相关接口
    Route::get('articleType/getListByCon', 'API\ArticleTypeController@getListByCon');
    Route::get('articleType/getById', 'API\ArticleTypeController@getById');

    //商品类别相关接口
    Route::get('goodsType/getListByCon', 'API\GoodsTypeController@getListByCon');
    Route::get('goodsType/getById', 'API\GoodsTypeController@getById');

    //商品相关接口
    Route::get('goods/getListByCon', 'API\GoodsController@getListByCon');
    Route::get('goods/getById', 'API\GoodsController@getById');

    //点赞相关
    Route::post('zan/setZan', 'API\ZanController@setZan')->middleware('user.checkToken');       //点赞

    //收藏相关
    Route::post('collect/setCollect', 'API\CollectController@setCollect')->middleware('user.checkToken');           //收藏
    Route::post('collect/cancelCollect', 'API\CollectController@cancelCollect')->middleware('user.checkToken');     //取消收藏
    Route::get('collect/getListByCon', 'API\CollectController@getListByCon');     //根据条件获取收藏列表

    //评论相关
    Route::post('comment/setComment', 'API\CommentController@setComment')->middleware('user.checkToken');           //评论
    Route::post('comment/cancelComment', 'API\CommentController@cancelComment')->middleware('user.checkToken');         //取消评论

    //活动规则
    Route::get('rule/getById', 'API\RuleController@getById');   //根据id获取规则信息

    //反馈信息接口
    Route::post('feedback/commit', 'API\FeedBackController@commit');   //反馈信息接口

    //用户地址相关
    Route::post('address/edit', 'API\AddressController@edit')->middleware('user.checkToken');   //新建/编辑地址
    Route::get('address/getListByCon', 'API\AddressController@getListByCon')->middleware('user.checkToken');   //根据条件获取地址列表
    Route::get('address/getDefault', 'API\AddressController@getDefault')->middleware('user.checkToken');   //获取默认地址
    Route::post('address/setDefault', 'API\AddressController@setDefault')->middleware('user.checkToken');   //设置默认地址

    //百度地图相关
    Route::post('amap/geoconv', 'API\AMapController@geoconv');   //坐标系转换

});


//iasrt公众号接口
//通知和支付不能跨域，所以单独处理
Route::group(['prefix' => 'isartfwh', 'middleware' => ['BeforeRequest']], function () {
    //服务器接收消息
    Route::any('/wechat/serve', 'API\ISARTFWH\WeChatController@serve');        //公众号校验token
    Route::any('/vote/payNotify', 'API\ISARTFWH\WeChatController@votePayNotify');        //投票大赛支付结果通知
});

Route::group(['prefix' => 'isartfwh', 'middleware' => ['BeforeRequest', 'cors']], function () {
    //获取配置信息
    Route::post('wechat/wxConfig', 'API\ISARTFWH\WeChatController@wxConfig');   //获取微信配置信息

    //测试获取unionid的方法
    Route::post('test/getUnionid', 'API\ISARTFWH\TestController@getUnionid');   //获取微信配置信息
});

//投票大赛
Route::group(['prefix' => 'vote', 'middleware' => ['BeforeRequest']], function () {

    //测试调度任务接口，该接口平时需要封闭，只在本机测试业务时使用
    Route::post('schedule/test', 'API\Vote\VoteTestScheduleController@test');   //外部调用接口

});


//每日一画接口
//由于支付通知接口不能跨域，所以单独处理
Route::group(['prefix' => 'mryh', 'middleware' => ['BeforeRequest']], function () {
    Route::any('game/join/payNotify', 'API\MRYHXCX\MryhJoinOrderController@payNotify');   //支付结果通知
});

Route::group(['prefix' => 'mryh', 'middleware' => ['BeforeRequest', 'cors']], function () {

    //测试调度任务接口，该接口平时需要封闭，只在本机测试业务时使用
    Route::post('schedule/test', 'API\MRYHXCX\MryhTestScheduleController@test');   //外部调用接口

    //获取openid
    Route::post('/wechat/login', 'API\MRYHXCX\WeChatController@login');        //登录接口
    Route::post('/wechat/decryptData', 'API\MRYHXCX\WeChatController@decryptData');        //消息解密

    //绑定unionid
    Route::post('/wechat/bindUnionId', 'API\MRYHXCX\WeChatController@bindUnionId')->middleware('user.checkToken');         //绑定账户unionid
    //判断是否绑定unionid
    Route::post('/wechat/isBindUnionId', 'API\MRYHXCX\WeChatController@isBindUnionId')->middleware('user.checkToken');         //

    //页面整体封装
    Route::get('page/index', 'API\MRYHXCX\MryhPageController@index');           //首页接口
    Route::get('page/game/getById', 'API\MRYHXCX\MryhPageController@game');               //活动页面接口
    Route::get('page/join/getById', 'API\MRYHXCX\MryhPageController@join');               //活动参与页面接口
    Route::get('page/my', 'API\MRYHXCX\MryhPageController@my')->middleware('user.checkToken');               //我的页面
    Route::get('page/person', 'API\MRYHXCX\MryhPageController@person');               //用户页面

    //获取首页轮播图
    Route::get('ad/getById', 'API\MRYHXCX\MryhADController@getById');
    Route::get('ad/getListByCon', 'API\MRYHXCX\MryhADController@getListByCon');

    //业务配置
    Route::get('setting/getSetting', 'API\MRYHXCX\MryhSettingController@getSetting');       //获取配置信息

    //活动信息
    Route::get('game/getById', 'API\MRYHXCX\MryhGameController@getById');   //根据id获取活动信息
    Route::get('game/getListByCon', 'API\MRYHXCX\MryhGameController@getListByCon');   //根据条件获取列表
    Route::post('game/join/payOrder', 'API\MRYHXCX\MryhJoinOrderController@payOrder')->middleware('user.checkToken');   //参加互动下单下单
    Route::post('game/join/joinByCoupon', 'API\MRYHXCX\MryhJoinController@joinByCoupon')->middleware('user.checkToken');   //通过优惠券参加活动
    Route::get('game/isUserJoin', 'API\MRYHXCX\MryhGameController@isUserJoin');   //用户是否参与活动
    Route::get('game/getShareInfo', 'API\MRYHXCX\MryhGameController@getShareInfo');   //获取活动的分享信息

    Route::post('game/share', 'API\MRYHXCX\MryhGameController@share')->middleware('user.checkToken');   //分享活动

    //用户活动参与信息
    Route::get('join/getListByCon', 'API\MRYHXCX\MryhJoinController@getListByCon');   //根据条件获取活动参与信息
    Route::post('join/getCert', 'API\MRYHXCX\MryhJoinController@getCert');     //获取参赛证书

    //用户上传图文信息
    Route::post('joinArticle/upload', 'API\MRYHXCX\MryhJoinArticleController@upload')->middleware('user.checkToken');   //上传图文信息
    Route::get('joinArticle/getListByCon', 'API\MRYHXCX\MryhJoinArticleController@getListByCon');   //根据条件获取信息列表
    Route::get('joinArticle/getById', 'API\MRYHXCX\MryhJoinArticleController@getById');   //根据id获取参赛作品信息

    //获取朋友参赛信息
    Route::get('friend/getListByCon', 'API\MRYHXCX\MryhFriendController@getListByCon');   //获取朋友列表

    //优惠券
    Route::get('userCoupon/getListByCon', 'API\MRYHXCX\MryhUserCouponController@getListByCon')->middleware('user.checkToken');   //获取优惠券列表
    Route::get('userCoupon/isUserHasCoupon', 'API\MRYHXCX\MryhUserCouponController@isUserHasCoupon');   //用户是否有某个优惠券

    //提现申请接口
    Route::post('withdraw/apply', 'API\MRYHXCX\MryhWithdrawCashController@apply')->middleware('user.checkToken');   //提现申请
    Route::get('withdraw/getListByWaitingJieSuan', 'API\MRYHXCX\MryhWithdrawCashController@getListByWaitingJieSuan')->middleware('user.checkToken');   //获取待提现信息
    Route::get('withdraw/getListByCon', 'API\MRYHXCX\MryhWithdrawCashController@getListByCon')->middleware('user.checkToken');   //获取提现记录列表
    Route::get('withdraw/getById', 'API\MRYHXCX\MryhWithdrawCashController@getById')->middleware('user.checkToken');   //根据id获取提现信息详情

    //获取分享海报
    Route::post('pic/share', 'API\MRYHXCX\MryhPicController@share');   //获取分享海报
    Route::post('pic/shareGame', 'API\MRYHXCX\MryhPicController@shareGame');  //分享活动

    //生成证书-测试用
    Route::post('pic/cert', 'API\MRYHXCX\MryhPicController@cert');   //测试证书

});


//艺术榜接口
Route::group(['prefix' => 'ysbxcx', 'middleware' => ['BeforeRequest', 'cors']], function () {

    Route::post('/wechat/login', 'API\YSBXCX\WeChatController@login');        //登录接口

    Route::post('/test/sendTemplateMessage', 'API\YSBXCX\TestController@sendTemplateMessage');        //发送模板消息

    //获取openid
    Route::post('/wechat/login', 'API\YSBXCX\WeChatController@login');        //登录接口
    Route::post('/wechat/decryptData', 'API\YSBXCX\WeChatController@decryptData');        //消息解密

    //绑定unionid
    Route::post('/wechat/bindUnionId', 'API\YSBXCX\WeChatController@bindUnionId')->middleware('user.checkToken');         //绑定账户unionid

    //判断是否绑定unionid
    Route::post('/wechat/isBindUnionId', 'API\MRYHXCX\WeChatController@isBindUnionId')->middleware('user.checkToken');

    //获取首页轮播图
    Route::get('ad/getById', 'API\YSBXCX\YSBADController@getById');
    Route::get('ad/getListByCon', 'API\YSBXCX\YSBADController@getListByCon');

    //获取个人主页
    Route::get('page/person', 'API\YSBXCX\YSBUserController@person');

    //获取主页接口
    Route::get('page/index', 'API\YSBXCX\YSBPageController@index');

});


//小艺商城接口
Route::group(['prefix' => 'shop', 'middleware' => ['BeforeRequest', 'cors']], function () {

    //获取首页轮播图
    Route::get('ad/getById', 'API\Shop\ShopADController@getById');
    Route::get('ad/getListByCon', 'API\Shop\ShopADController@getListByCon');

    //下单接口
    Route::post('order/payOrder', 'API\Shop\ShopOrderController@payOrder');
});

Route::group(['prefix' => 'shop', 'middleware' => ['BeforeRequest']], function () {

    //支付结果通知接口
    Route::post('order/payNotify', 'API\Shop\ShopOrderController@payNotify');
});


//营销活动接口
Route::group(['prefix' => 'yxhd', 'middleware' => ['BeforeRequest', 'cors']], function () {

    //页面整合相关
    Route::get('page/index', 'API\Yxhd\YxhdPageController@index');     //大转盘首页信息

    //获取首页轮播图
    Route::post('activity/draw', 'API\Yxhd\YxhdOrderController@draw')->middleware('user.checkToken');
});


