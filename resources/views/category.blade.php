{{-- 产品类别--}}
@extends('master')<!-- 继承母版-->

@section('title', '书籍类别')

@section('content')
<div class="weui_cells_title">选择书籍类别</div>
    <div class="weui_cells weui_cells_split">
        <div class="weui_cells weui_cell_select" style="margin-top: 0px;">
            <div class="weui_cell_bd weui_cell_primary">
                <select name="category" class="weui_select" >
                    @foreach($categorys as $category)
                        <option value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
</div>
    {{-- 二级类别--}}
<div class="weui_cells weui_cells_access">
    <a class="weui_cell" href="javascript:;">
        <div class="weui_cell_bd weui_cell_primary">
            <p></p>
        </div>
        <div class="weui_cell_ft"></div>
    </a>
    <a class="weui_cell" href="javascript:;">
        <div class="weui_cell_bd weui_cell_primary">
            <p></p>
        </div>
        <div class="weui_cell_ft"></div>
    </a>
</div>


@endsection

@section('my-js')
    <script type="text/javascript">
        //一进入书籍类别页面就需要获取父类ID,调用自定义方法
        _getCategory();
        //监听选择列表，一旦改变就ajax异步获取
       $('.weui_select').change(function (event) {
           _getCategory();
       });
       function _getCategory() {
           var parent_id =  $('.weui_select option:selected').val();

               $.ajax({
                   type: 'GET',
                   url: '/service/category/parent_id/' + parent_id,//获取父类i号，显示二级菜单
                   dataType: 'json',
                   cache: false,
                   success: function (data) {
                       //返回成功时的数据，打印日志便于调试
                       console.log('获取类别数据：');
                       console.log(data);
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
                        //清空内容
                       $('.weui_cells_access').html('');
                       //遍历显示
                       for(var i = 0;i < data.categorys.length; i++){
                           var next = '/product/category_id/' + data.categorys[i].id;//跳转路径（产品列表）
                            var node = '<a class="weui_cell" href="'+ next+'">'+
                                            '<div class="weui_cell_bd weui_cell_primary">'+
                                                '<p>'+data.categorys[i].name+'</p>'+
                                            '</div>'+
                                            '<div class="weui_cell_ft"></div>'+
                                        '</a>';
                           $('.weui_cells_access').append(node);//追加的方式添加，循环不会覆盖前面的
                       }
                   },
                   //打印错误日志
                   error: function (xhr, status, error) {
                       console.log(xhr);
                       console.log(status);
                       console.log(error);
                   }
               })
       }


    </script>
@endsection