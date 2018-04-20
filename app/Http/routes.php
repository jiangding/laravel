<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::any('/wechat', 'WechatController@serve'); /*微信接口*/
Route::any('/applet', 'WechatController@applet'); /*小程序接口*/
Route::any('/pay/callback','PayController@callback'); /*支付回调*/

Route::group(['namespace' => 'Frontend','as' => 'frontend::','middleware' => ['web', 'wechat.oauth']], function (){
    Route::any('/scan','ScanController@index');
    /*
     * 用户部分
     */
    Route::any('/user','UsersController@index');
    Route::any('/user/index','UsersController@index');
    Route::any('/user/info','UsersController@info');
    Route::any('/user/register','UsersController@register');
    Route::any('/user/profile','UsersController@profile');
    Route::any('/user/sms','UsersController@sms');

    // 提交反馈
    Route::any('/user/remind','UsersController@remind');
    /*
     *  意见反馈
     */
    Route::any('/feedback','FeedbackController@index');
    Route::any('/feedback/index','FeedbackController@index');

    /*
     *  用户地址
     */
    Route::any('/address','AddressController@index');
    Route::any('/address/index','AddressController@index');
    Route::any('/address/create','AddressController@create');
    Route::any('/address/update/{id}','AddressController@update');
    Route::any('/address/delete','AddressController@delete');

    /*
     *  用户扫码足迹
     */
    Route::any('/record/{id}','RecordController@index');
    Route::any('/record/remind','RecordController@remind');
    Route::any('/record/update/{record_id}','RecordController@update');
    Route::any('/record/update/remind/{id}/{date}','RecordController@setRemindDate');
    Route::any('/record/update/updateRemind/{value}/{id}','RecordController@updateRemind');
    Route::any('/record/delete/{id}','RecordController@delete');
    /*
     *  用户库存
     */
    Route::any('/stock','StockController@index');
    Route::any('/stock/index','StockController@index');
    // 扫码结束处理
    Route::any('/stock/barcode/{number}/{barcode}','StockController@scan');
    Route::any('/stock/create/{barcode}','StockController@create');
    Route::any('/stock/update/{pid}','StockController@update');
    Route::any('/stock/new/{pid}','StockController@new');
    Route::any('/stock/delete/{sid}','StockController@delete');
    // 客服发送消息
    Route::any('/stock/sendMessage', "StockController@sendMessage");
    Route::any('/stock/receiveMessage', "StockController@receiveMessage");
    Route::any('/stock/a', "StockController@a");
    Route::any('/stock/b', "StockController@b");
    Route::any('/stock/c', "StockController@c");
    Route::any('/stock/d', "StockController@d");


    //立刻购买
    Route::any('/shopping/scan', "ShoppingController@scan");
    Route::any('/shopping/shoppingCart/{barcode}', "ShoppingController@shoppingCart");
    Route::any('/shopping/delete', "ShoppingController@delete");



    /*
     *  清单
     */
    Route::any('/detail/create','DetailController@create');
    //  改价清单页
    Route::any('/detail/price/{uuid}','DetailController@price');
    // 提交清单
    Route::any('/detail/submit','DetailController@submit');

    /*
     *  发票
    Route::any('/invoice/create','InvoicesController@create');
    Route::any('/invoice/update','InvoicesController@update');
    Route::any('/invoice/delete','InvoicesController@delete');
    */

    /*
     * 用户打赏
     */
    Route::any('/pay/reward/{uuid}','PayController@reward');
    /*
     * 订单
     */
    Route::any('/order','OrderController@index');
    Route::any('/order/index','OrderController@index');
    Route::any('/pay/order/{uuid}','OrderController@order');
    Route::any('/pay/unpay','OrderController@unpay');
    Route::post('/order/cancelOrder', 'OrderController@cancelOrder');
    Route::post('/order/applyRefund', 'OrderController@applyRefund');


});


// 登录
Route::get('admin/login', 'Backend\HomeController@login');
Route::post('admin/doLogin', 'Backend\HomeController@doLogin');
Route::get('admin/logout', 'Backend\HomeController@logout');
// 后台组
Route::group(['prefix' => 'admin', 'namespace' => 'Backend','as' => 'backend::','middleware' => ['web','auth']], function (){

    Route::get('/', 'HomeController@index');
    Route::get('/index', 'HomeController@index');

    // 用户列表
    Route::get('/user', 'UserController@index');
    Route::get('/user/edit/{id}', 'UserController@edit');
    Route::post('/user/update', 'UserController@update');
    Route::get('/user/stockList/{userid}', 'UserController@stockList');
    Route::get('/user/addressList/{userid}', 'UserController@addressList');

    // 意见反馈
    Route::get('/feedback', 'FeedbackController@index');

    // 产品
    Route::get('/product', 'ProductController@index');
    Route::get('/product/edit/{id}/{did}', 'ProductController@edit');
    Route::any('/product/toUser', 'ProductController@toUser');
    Route::any('/product/getUser', 'ProductController@getUser');
    Route::post('/product/update', 'ProductController@update');
    Route::any('/product/import', 'ProductController@import');
    Route::any('/product/export', 'ProductController@export');
    Route::get('/product/updating', 'ProductController@updating');
    Route::post('/product/spider', 'ProductController@spider');
    // 品类
    Route::get('/catalog', 'CatalogController@index');
    Route::get('/catalog/edit/{id}', 'CatalogController@edit');
    Route::any('/catalog/update', 'CatalogController@update');
    Route::get('/catalog/add', 'CatalogController@add');
    Route::get('/catalog/export', 'CatalogController@export');

    // 清单
    Route::get('/detail', 'DetailController@index');
    Route::get('/detail/detail/{id}', 'DetailController@detail');
    Route::get('/detail/add/{openid}', 'DetailController@add');
    Route::post('/detail/productAdd', 'DetailController@productAdd');
    Route::post('/detail/productDelete', 'DetailController@productDelete');
    Route::post('/detail/update', 'DetailController@update');

    // 爬取
    Route::post('/detail/spider', 'DetailController@spider');
    // 修改邮费
    Route::any('/detail/postage_edit/{id}', 'DetailController@postageEdit');
    Route::post('/detail/postage_update', 'DetailController@postageUpdate');
    // 修改价格
    Route::any('/detail/price_edit/{id}', 'DetailController@priceEdit');
    Route::post('/detail/price_update', 'DetailController@priceUpdate');

    Route::get('/detail/edit/{id}', 'DetailController@edit');
    // 推送比价结果
    Route::post('/detail/toUser', 'DetailController@toUser');

    // 订单
    Route::any('/order','OrderController@index');
    Route::any('/order/index','OrderController@index');
    Route::get('/order/detail/{oid}','OrderController@detail');
    Route::any('/order/refund','OrderController@refund');
    Route::any('/order/unusual','OrderController@unusual');
    Route::any('/order/toOrder','OrderController@toOrder');

    // 上传
    Route::any('/upload', 'UploadController@upload');

    // 客服聊天记录
    Route::any('/customService', 'CsController@index');
    Route::any('/cservice/chatlogs/{adminid}', 'CsController@getLogs');
    Route::any('/csupload', 'UploadController@csupload');

    //物流信息
    Route::any('/logistics','LogisticsController@index');
    Route::any('/spiderLogisticByAll','LogisticsController@spiderLogisticByAll');
    Route::any('/addChildrenId/{logisticsid}','LogisticsController@addChildrenId');
    //更新cookies
    Route::any('/logistics/updateCookies','LogisticsController@updateCookies');


});

Route::group(['prefix' => 'push'], function () {
    Route::any('/websocket','PushController@websocket');
    Route::any('/register','PushController@register');
    Route::any('/ping','PushController@ping');
    Route::any('/status','PushController@status');
    Route::any('/sendMessage','PushController@sendMessage');
    Route::any('/newMessage','PushController@newMessage');
});


// 小程序api
Route::group(['prefix' => 'api','namespace' => 'Api','as' => 'api::'], function () {
    Route::any('/index','IndexController@index');
    Route::any('/things','IndexController@things');
    Route::any('/theme','IndexController@theme');
    Route::any('/ha_detail','IndexController@ha_detail');
    Route::any('/morebefore','IndexController@morebefore');
    Route::any('/shortComments','IndexController@shortComments');
});

// test
Route::any('/admin/test', 'testController@test');

