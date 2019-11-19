<?php
//购物车控制器
namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Entity\CartItem;
use App\Models\M3Result;
use Illuminate\Http\Request;



class CartController extends Controller
{
    public function addCart(Request $request,$product_id)
    {
        $m3_result = new M3Result;
        $m3_result->status = 0;
        $m3_result->message = '添加成功';

        //如果当前已经登录
        $member = $request->session()->get('member','');
        if($member != ''){
          //$cart = Cart::where('member_id',$member->id)->first();
            $cart_items = CartItem::where('member_id',$member->id)->get();

            $exist = false;
            foreach ($cart_items as $cart_item){
                if($cart_item->product_id == $product_id){
                    $cart_item->count++;
                    $cart_item->save();
                    $exist = true;
                    break;
                }
            }
            if($exist ==false){
                $cart_item = new CartItem;
                $cart_item->product_id = $product_id;
                $cart_item->count = 1;
                $cart_item->member_id = $member->id;
                $cart_item->save();
            }
            return $m3_result->toJson();
        }

        /*
         * 自定义购物车存储数据方式
         1:3,2:1,3:2
        1:3(1代表产品id，3代表产品数量，中间以冒号为间隔)
         */
        //从cookie中获取购物车的值
        $bk_cart = $request->cookie('bk_cart');
        //return $bk_cart;
        $bk_cart_arr = $bk_cart != null ? explode(',',$bk_cart):array();//$bk_cart不为空的话就拆分为逗号隔开的数据存入数组中

        $count = 1;
        foreach($bk_cart_arr as &$value){//bk_cart_arr为基本数组类型不是对象数组，若想改变其中的元素，需要传引用
            $index = strpos($value,':');  //获取":"在$value这个字符串中的位置，然后截取
            if(substr($value,0,$index) == $product_id ){//从0开始到冒号结束截取，得到（product_id）即产品ID号
                //如果等于传过来的产品ID，就需要获取产品数量
                $count = ((int)substr($value,$index + 1)) + 1;//$index+1(表示":”的后一位开始截取)，然后将字符强制转换为int类型
                $value = $product_id .':' . $count;
                break;//找到了产品的信息，跳出循环
            }
        }
        //判断count
        if($count == 1){
            //如果$count为一的话，说明购物车中没有该产品，是一个新产品
            //需要将新产品加到数组中
            array_push($bk_cart_arr,$product_id . ':'. $count);
        }

        //将$bk_cart字符串重新写入cookie中去
        return response($m3_result->toJson())->withCookie('bk_cart',implode(',',$bk_cart_arr));
    }
    //清除购物车
    public function deleteCart(Request $request){
        $m3_result = new M3Result;
        $m3_result->status = 0;
        $m3_result->message = '删除成功';

        $product_ids = $request->input('product_ids','');
        if($product_ids == ''){
            $m3_result->status = 1;
            $m3_result->message = '书籍ID为空！';
            return $m3_result->toJson();
        }
        $product_ids_arr = explode(',',$product_ids);

        $member = $request->session()->get('member','');
        if($member != ''){
            //已经登录
            CartItem::where('product_id',$member->id)->delete();
            return $m3_result->toJson();
        }

        $product_ids = $request->input('product_ids','');
        if($product_ids == ''){
            $m3_result->status = 1;
            $m3_result->message = '书籍ID为空！';
            return $m3_result->toJson();
        }
        //未登录
        $bk_cart = $request->cookie('bk_cart');
        $bk_cart_arr = ($bk_cart != null ? explode(',',$bk_cart) : array());
        foreach ($bk_cart_arr as $key => $value )
        {
            $index = strpos($value,':');
            $product_id = substr($value,0,$index);
            //存在，删除
            if(in_array($product_id,$product_ids_arr)){
                array_splice($bk_cart_arr,$key,1);
                continue;
            }
        }
        return response($m3_result->toJson())->withCookie('bk_cart',implode(',',$bk_cart_arr));
    }
}
