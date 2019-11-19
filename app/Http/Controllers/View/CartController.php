<?php
//购物车
namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use App\Entity\Product;
use Illuminate\Http\Request;
use App\Entity\CartItem;


class CartController extends Controller
{
    public function toCart(Request $request)
    {

        $cart_items = array();

        $bk_cart = $request->cookie('bk_cart');
        $bk_cart_arr = $bk_cart != null ? explode(',', $bk_cart) : array();//$bk_cart不为空的话就拆分为逗号隔开的数据存入数组中

        //判断用户是否登录
        $member = $request->session()->get('member', '');
        if ($member != '') {
            $cart_items = $this->syncCart($member->id, $bk_cart_arr);
            return response()->view('cart', ['cart_items' => $cart_items])->withCookie('bk_cart', 'null');
        }

        foreach ($bk_cart_arr as $key => $value) {
            $index = strpos($value, ':');

            $cart_item = new CartItem;
            $cart_item->id = $key;
            $cart_item->product_id = substr($value, 0, $index);
            $cart_item->count = substr($value, $index + 1);
            $cart_item->product = Product::find($cart_item->product_id);
            if ($cart_item->product != null) {
                array_push($cart_items, $cart_item);
            }
        }
        return view('cart')->with('cart_items', $cart_items);
    }

    //购物车同步方法
    private function syncCart($member_id, $bk_cart_arr)
    {
        //获取数据库中的数据
        $cart_items = CartItem::where('member_id', $member_id)->get();
        //当本地与服务器上都有购物车数据，同步数据时以数量多的为准
        $cart_items_arr = array();
        foreach ($bk_cart_arr as $value) {
            $index = strpos($value, ':');
            $product_id = substr($value, 0, $index);
            $count = (int)substr($value, $index + 1);

            // 判断离线购物车中product_id 是否存在 数据库中
            $exist = false;
            foreach ($cart_items as $temp) {
                if ($temp->product_id == $product_id) {
                    if ($temp->count < $count) {
                        $temp->count = $count;
                        $temp->save();
                    }
                    $exist = true;
                    break;
                }
            }

               // 不存在则存储进来
//               if($exist == false) {
//                   $cart_item = new CartItem;
//                   $cart_item->member_id = $member_id;
//                   $cart_item->product_id = $product_id;
//                   $cart_item->count = $count;
//                   $cart_item->save();
//                   $cart_item->product = Product::find($cart_item->product_id);
//                   array_push($cart_items_arr, $cart_item);
//               }
           }

           // 为每个对象附加产品对象便于显示
           foreach ($cart_items as $cart_item) {
               $cart_item->product = Product::find($cart_item->product_id);
               array_push($cart_items_arr, $cart_item);
           }

            return $cart_items_arr;


        }

}

