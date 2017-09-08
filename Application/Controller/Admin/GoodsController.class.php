<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/15 0015
 * Time: 上午 10:21
 */
class GoodsController extends PlatformController
{

    public function add(){
        if($_SERVER['REQUEST_METHOD']=='POST'){
            //>> 1. 接收请求数据
                 //接收上传文件的代码
                $uploadTool = new UploadTool();
                $result = $uploadTool->uploadOne($_FILES['logo'],"/goods");
                if($result===false){
                    //如果出错, 从$uploadTool中得到错误信息
                    $this->redirect("index.php?p=Admin&c=Goods&a=add",3,$uploadTool->getError());
                }
                $_POST['logo'] = $result; //将上传后的路径保存$_POST,因为$_POST中的数据要被保存在数据库中


                //根据上传上来的图片生成缩略图
                /**
                 * 需求:
                 *  大图片: Uploads/goods/20161019/sdlfsldflsdf.jpg
                 *  缩略图: Uploads/goods/20161019/sdlfsldflsdf_50x50.jpg
                 */

                $imageTool = new ImageTool();
                $thumb_filename = $imageTool->thumb($result,50,50);  //将大图片路径传入就生成缩略图
                if($thumb_filename===false){
                    $this->redirect("index.php?p=Admin&c=Goods&a=add",3,$imageTool->getError());
                }
                $_POST['thumb_logo'] = $thumb_filename;  //为了将缩略图的路径保存到数据库中



            //>> 2. 处理数据
                $goodsModel = new GoodsModel();
                $goods_id = $goodsModel->add($_POST);  //将商品数据保存到goods表中


            //>>4.处理商品相册
                //>>4.1 上传相册图片
                  $result = $uploadTool->uploadMore($_FILES['path'],"/gallery");
                  if($result===false){  //如果返回false, 上传失败!
                      $this->redirect("index.php?p=Admin&c=Goods&a=add",3,$uploadTool->getError());
                  }

                // var_dump($result);//包含多个文件上传后的路径,(以数组的形式存放这些路径)
                //>>4.2 把图片相信保存到gallery表中
                 $galleryModel = new GalleryModel();
                 foreach($result as $k=>$path){
                     $gallery_intro = $_POST['gallery_intro'][$k];  //获取对应的图片描述
                     $url = $_POST['url'][$k];  //获取对应图片的url地址

                     $gallery = ['gallery_intro'=>$gallery_intro,'path'=>$path,'url'=>$url,'goods_id'=>$goods_id];
                     $galleryModel->insertData($gallery);
                 }

            //>> 3. 显示页面或跳转
                $this->redirect("index.php?p=Admin&c=Goods&a=index");
        }else{
            //>> 1. 接收请求数据
            //>> 2. 处理数据
            //>>2.1 准备商品分类数据
            $categoryModel = new CategoryModel();
            $categorys = $categoryModel->getList();
            $this->assign("categorys",$categorys);
            //>>2.2 准备品牌数据
            $brandModel = new BrandModel();
            $brands = $brandModel->getAll();
            $this->assign("brands",$brands);
            //>> 3. 显示页面或跳转
            $this->display("add");
        }

    }


    public function index(){
         //>>1. 接收请求数据
            $page = isset($_GET['page'])?$_GET['page']:1;//默认为第一页
         //>>2. 处理数据
            $goodsModel = new GoodsModel();
            /**
             * 返回一个包含以下内容的数组:
             * [
             *  "rows"=>当前页的列表数据,
             *  "pageHtml"=>"分页工具条的html代码",
             *]
             */
            $pageResult = $goodsModel->getPageResult($page);

            $this->assign($pageResult);  //分配到页面上

         //>>3. 显示页面或跳转
            $this->display("index");
    }
}