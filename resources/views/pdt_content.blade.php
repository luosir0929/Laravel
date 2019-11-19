{{-- 产品详情--}}
@extends('master')

@section('title',$product->name)

@section('content')
    {{--轮播图--}}
    <link rel="stylesheet" href="/css/swipe.css">
    <div class="page bk_content" style="top: 0;">
    <div class="addWrap">
        <div class="swipe" id="mySwipe">
            <div class="swipe-wrap">
                {{--轮播图图片地址--}}
                @foreach($pdt_images as $pdt_image)
                    <div>
                    <a href="javascript:;"><img class="img-responsive" src="{{$pdt_image->image_path}}" /></a>
                    </div>
                @endforeach
            </div>
        </div>
        <ul id="position">
            {{-- 轮播图的点状导航--}}
            @foreach($pdt_images as $index => $pdt_image)
            <li class={{$index == 0 ? 'cur' : ''}}></li>
            @endforeach
        </ul>
    </div>

    <div class="weui_cells_title">
        <span class="bk_title">{{$product->name}}</span>
        <span class="bk_price" >￥{{$product->price}}</span>
    </div>
    <div class="weui_cells">
        <div class="weui_cell">
             <p class="bk_summary">{{$product->summary}}</p>
        </div>
    </div>
    {{-- 详细介绍--}}
    <div class="weui_cells_title">详细介绍</div>
    <div class="weui_cells">
        <div class="weui_cell">
            @if($pdt_content->content != null)
                {!! $pdt_content->content !!}
            @else
            @endif


            {!! $pdt_content->content !!}{{--不转义html标签--}}
{{--            <p>--}}
{{--$pdt_content->content--}}{{----}}{{--blade模版自动解析html标签--}}
{{--            </p>--}}


        </div>
    </div>
    </div>
    {{-- 底部按钮--}}
    <div class="bk_fix_bottom">
        <div class="bk_half_area">
            <button class="weui_btn weui_btn_primary" onclick="_addCart();">加入购物车</button>
        </div>
        <div class="bk_half_area">
            <button class="weui_btn weui_btn_default" onclick="_toCart();">查看购物车(<span id="cart_num" class="m3_price">{{$count}}</span>)</button>
        </div>
    </div>
@endsection

@section('my-js')
    <script src="/js/swipe.min.js"></script>
    <script type="text/javascript">
        var bullets = document.getElementById('position').getElementsByTagName('li');
        Swipe(document.getElementById('mySwipe'),{
            auto:3000,
            continuous : true,
            disableScroll :false,
            callback: function(pos){//回调函数
                var i = bullets.length;
                while(i--){
                    bullets[i].className = '';
                }
                bullets[pos].className = 'cur';
            }
        });

        //添加购物车
        function _addCart() {
            var product_id = "{{$product->id}}";
            $.ajax({
                type: 'GET',
                url: '/service/cart/add/' + product_id,
                dataType: 'json',
                cache: false,
                success: function (data) {
                    if (data == null) {
                        $('.bk_toptips').show();
                        $('.bk_toptips span').html('服务端错误');
                        setTimeout(function () {
                            $('.bk_toptips').hide();
                        }, 2000);
                        return;
                    }
                    if (data.status != 0) {
                        $('.bk_toptips').show();
                        $('.bk_toptips span').html(data.message);
                        setTimeout(function () {
                            $('.bk_toptips').hide();
                        }, 2000);
                        return;
                    }
                    //添加购物车成功后，获取结算里的值+1
                    var num = $('#cart_num').html();
                    if(num == '') num = 0;
                    $('#cart_num').html(Number(num) + 1);
                },
                error: function (xhr, status, error) {
                    console.log(xhr);
                    console.log(status);
                    console.log(error);
                }
            })
        }

        //结算
        function _toCart() {
            location.href = '/cart';
        }
    </script>
@endsection