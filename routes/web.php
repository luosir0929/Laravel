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
|@路由控制
*/
//前台视图控制器路由
Route::get('/', 'View\MemberController@toLogin');
Route::get('/welcome', 'View\MemberController@welcome');
Route::get('/login', 'View\MemberController@toLogin');
Route::get('/register', 'View\MemberController@toRegister');
Route::get('/category', 'View\BookController@toCategory');
Route::get('/product/category_id/{category_id}', 'View\BookController@toProduct');
Route::get('/product/{product_id}', 'View\BookController@toPdtContent');
Route::get('/cart','View\CartController@toCart');


/*------------------------------------------------------------- */
//前台service控制器路由组
Route::group(['prefix' => 'service'],function(){
    Route::get('validate_code/create', 'Service\ValidateController@create');
    Route::post('validate_phone/send', 'Service\ValidateController@sendSMS');
    Route::post('register', 'Service\MemberController@register');
    Route::post('login', 'Service\MemberController@login');
    Route::get('category/parent_id/{parent_id}','Service\BookController@getCategoryByParentId');
    Route::get('cart/add/{product_id}','Service\CartController@addCart');
    Route::get('cart/delete','Service\CartController@deleteCart');
    Route::post('upload/{type}', 'Service\UploadController@uploadFile');

});

//后台路由组
Route::group(['prefix' => 'admin'],function(){
    Route::get('index', 'Admin\IndexController@toIndex');
    Route::get('login', 'Admin\IndexController@toLogin');
    Route::get('exit', 'Admin\IndexController@toExit');
    Route::post('service/login', 'Admin\IndexController@login');

    //产品类别
    Route::get('category', 'Admin\CategoryController@toCategory');//视图路由
    Route::get('category_add', 'Admin\CategoryController@toCategoryAdd');
    Route::get('category_edit', 'Admin\CategoryController@toCategoryEdit');

    //产品
    Route::get('product', 'Admin\ProductController@toProduct');
    Route::get('product_info', 'Admin\ProductController@toProductInfo');
    Route::get('product_add', 'Admin\ProductController@toProductAdd');
    Route::get('product_edit', 'Admin\ProductController@toProductEdit');

    //会员
    Route::get('member', 'Admin\MemberController@toMember');
    Route::get('member_edit', 'Admin\MemberController@toMemberEdit');

    //订单列表

    Route::get('order', 'Admin\OrderController@toOrder');
    Route::get('order_edit', 'Admin\OrderController@toOrderEdit');






    Route::group(['prefix' => 'service'],function (){

        Route::post('category/add', 'Admin\CategoryController@CategoryAdd');
        Route::post('category/del', 'Admin\CategoryController@CategoryDel');
        Route::post('category/edit', 'Admin\CategoryController@CategoryEdit');

        Route::post('product/add', 'Admin\ProductController@productAdd');
        Route::post('product/del', 'Admin\ProductController@productDel');
        Route::post('product/edit', 'Admin\ProductController@productEdit');

        Route::post('member/edit', 'Admin\MemberController@memberEdit');

        Route::post('order/edit', 'Admin\OrderController@orderEdit');

    });

});



//中间件路由组
Route::group(['middleware' => 'check.login'],function (){
    //Route::get('/cart','View\CartController@toCart');
});

