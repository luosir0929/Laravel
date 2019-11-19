<?php
//登录，注册视图控制器
namespace App\Http\Controllers\View;

use App\Entity\CartItem;
use App\Http\Controllers\Controller;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\PdtContent;
use App\Entity\PdtImages;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function toCategory($value=''){
        $categorys = Category::whereNull('parent_id')->get();
        return view('category')->with('categorys',$categorys);
    }
    public function toProduct($category_id){
        $products = Product::where('category_id',$category_id)->get();
        return view('product')->with('products',$products);
    }
    public function toPdtContent(Request $request ,$product_id){
        $product = Product::find($product_id);
        $pdt_content = PdtContent::where('product_id',$product_id)->first();
        $pdt_images = PdtImages::where('product_id',$product_id)->get();

        $bk_cart = $request->cookie('bk_cart');
        //return $bk_cart;
        $bk_cart_arr = $bk_cart != null ? explode(',',$bk_cart):array();//$bk_cart不为空的话就拆分为逗号隔开的数据存入数组中

         $count = 0;

         $member = $request->session()->get('member','');
         if($member != ''){
             //$cart = Cart::where('member_id',$member->id)->first();
             $cart_items = CartItem::where('member_id',$member->id)->get();

             foreach ($cart_items as $cart_item) {
                 if ($cart_item->product_id == $product_id) {
                     $count = $cart_item->count;
                     break;
                 }
             }
         }else {
             $bk_cart = $request->cookie('bk_cart');
             $bk_cart_arr = ($bk_cart != null ? explode(',', $bk_cart) : array());

             foreach ($bk_cart_arr as $value) {//bk_cart_arr为基本数组类型不是对象数组，若想改变其中的元素，需要传引用
                 $index = strpos($value, ':');  //获取":"在$value这个字符串中的位置，然后截取
                 if (substr($value, 0, $index) == $product_id) {//从0开始到冒号结束截取，得到（product_id）即产品ID号
                     //如果等于传过来的产品ID，就需要获取产品数量
                     $count = ((int)substr($value, $index + 1));//$index+1(表示":”的后一位开始截取)，然后将字符强制转换为int类型
                     break;//找到了产品的信息，跳出循环
                 }
             }
         }
        return view('pdt_content')->with('product',$product)
                                        ->with('pdt_content',$pdt_content)
                                        ->with('pdt_images',$pdt_images)
                                        ->with('count',$count);
    }
}