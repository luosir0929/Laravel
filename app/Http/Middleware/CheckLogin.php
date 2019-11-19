<?php
//中间件
// 验证是否登录
namespace App\Http\Middleware;

use Closure;

class CheckLogin
{
    public function handle($request,Closure $next)
    {
        //获取上一次访问的地址
        $http_referer = $_SERVER['HTTP_REFERER'];

        $member = $request->session()->get('member',urlencode($http_referer));
        if($member == ''){
            return redirect('/login');//session中没找到，则重定向到登录页面
        }
            return $next($request);
    }

}
