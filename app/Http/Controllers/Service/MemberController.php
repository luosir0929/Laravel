<?php
//接口控制器，注册
namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\M3Result;
use App\Entity\Member;
use App\Entity\TempPhone;



class MemberController extends Controller
{
    public function register(Request $request)
    {
        $phone = $request->input('phone', '');
        $password = $request->input('password', '');
        $confirm = $request->input('confirm', '');
        $phone_code = $request->input('phone_code', '');

        $m3_result = new M3Result;

        if ($phone == '') {
            $m3_result->status = 1;
            $m3_result->message = '手机号不能为空';
            return $m3_result->toJson();
        }
        if ($password == '' || strlen($password) < 6) {
            $m3_result->status = 2;
            $m3_result->message = '密码不少于6位';
            return $m3_result->toJson();
        }
        if ($confirm == '' || strlen($confirm) < 6) {
            $m3_result->status = 3;
            $m3_result->message = '确认密码不少于6位';
            return $m3_result->toJson();
        }
        if ($password != $confirm) {
            $m3_result->status = 4;
            $m3_result->message = '两次密码不相同';
            return $m3_result->toJson();
        }

        // 手机号注册校验
        if ($phone != '') {
            if ($phone_code == '' || strlen($phone_code) != 6) {
                $m3_result->status = 5;
                $m3_result->message = '手机验证码为6位';
                return $m3_result->toJson();
            }

            $tempPhone = TempPhone::where('phone', $phone)->first();
            if ($tempPhone->code == $phone_code) {
                if (time() > strtotime($tempPhone->deadline)) {
                    $m3_result->status = 7;
                    $m3_result->message = '手机验证码不正确';
                    return $m3_result->toJson();
                }

                $member = new Member;
                $member->phone = $phone;
                $member->password = md5($password);
                $member->save();

                $m3_result->status = 0;
                $m3_result->message = '注册成功';
                return $m3_result->toJson();
            } else {
                $m3_result->status = 7;
                $m3_result->message = '手机验证码不正确';
                return $m3_result->toJson();
            }

        }
    }
    //登录校验
    public function login(Request $request)
    {
        //获取表单数据
        $phone = $request->get('phone', '');
        $password = $request->get('password', '');
        $validate_code = $request->get('validate_code', '');

        $m3_result = new M3Result();
        //校验
        if ($phone == '') {
            $m3_result->status = 1;
            $m3_result->message = '手机号不能为空';
            return $m3_result->toJson();
        }
        if ($password == '' || strlen($password) < 6) {
            $m3_result->status = 2;
            $m3_result->message = '密码不少于6位';
            return $m3_result->toJson();
        }
        if ($validate_code == '' || strlen($validate_code) != 4 ) {
            $m3_result->status = 3;
            $m3_result->message = '验证码错误';
            return $m3_result->toJson();
        }

        //判断
        $validate_code_session = $request->session()->get('validate_code');
        if($validate_code != $validate_code_session){
            $m3_result->status = 1;
            $m3_result->message = '验证码不正确！';
            return $m3_result->toJson();
        }

        $member = Member::where('phone',$phone)->first();
        if($member->phone== null){
            $m3_result->status = 2;
            $m3_result->message = "该用户不存在";
            return $m3_result->toJson();
        }else{
            if(md5($password) != $member->password){
                $m3_result->status = 3;
                $m3_result->message = "密码不正确！";
                return $m3_result->toJson();
            }
        }
        //校验通过，将信息保存到session中
        $request->session()->put('member',$member);

        $m3_result->status = 0;
        $m3_result->message = "登录成功！";
        return $m3_result->toJson();
    }
}
