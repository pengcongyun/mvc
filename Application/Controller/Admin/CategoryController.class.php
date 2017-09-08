<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/30 0030
 * Time: 上午 11:17
 */
class CategoryController extends PlatformController
{
    /**
     * 展示商品分类列表
     */
    public function index()
    {
        //1.接收请求数据

        //2.处理数据
        //调用模型，期望模型上有个一个方法可以查询所有数据
        $categoryModel = new CategoryModel();
        $rows = $categoryModel->getList();
        //2.显示页面
        $this->assign("rows", $rows);
        $this->display('index');
    }

    /**
     * 展示添加  保存添加数据
     */
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            //1.接收请求数据
            $data = $_POST;
            //2.处理数据
            //将提交数据保存到数据库
            $categoryModel = new CategoryModel();
            $result = $categoryModel->insertData($data);
            if ($result === false) {
                $this->redirect("index.php?p=Admin&c=Category&a=add","添加分类失败！错误原因：".$categoryModel->getError(),3);
            }else{
                //2.显示页面
                $this->redirect("index.php?p=Admin&c=Category&a=index");
            }

        } else {
            //1.接收请求数据
            //2.处理数据
            //获取所有商品分类
            $categoryModel = new CategoryModel();
            $rows = $categoryModel->getList();
            //2.显示页面
            $this->assign("rows", $rows);
            $this->display('add');
        }
    }

    /**
     * 商品分类的删除
     */
    public function delete()
    {
        //1.接收请求数据
            $id = $_GET['id'];
        //2.处理数据
            $categoryModel = new CategoryModel();
            $result = $categoryModel->deleteByPk($id);
            if ($result === false) {
                $this->redirect("index.php?p=Admin&c=Category&a=index","删除失败！错误原因：".$categoryModel->getError(),3);
            }else{
                //2.显示页面
                $this->redirect("index.php?p=Admin&c=Category&a=index");
            }
    }

    /**
     * 编辑回显 保存编辑数据
     */
    public function edit(){
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            //1.接收请求数据
                $data = $_POST;
            //2.处理数据
                $categoryModel = new CategoryModel();
                $result = $categoryModel->update($data);
                if ($result === false) {
                    $this->redirect("index.php?p=Admin&c=Category&a=edit&id=".$data['id'],"编辑失败！错误原因：".$categoryModel->getError(),3);
                }else{
                    //2.显示页面
                    $this->redirect("index.php?p=Admin&c=Category&a=index");
                }
        }else{
            //1.接收请求数据
                $id = $_GET['id'];
            //2.处理数据
                //1,根据id查询出一条数据
                $categoryModel = new CategoryModel();
                $row = $categoryModel->getByPk($id);
                //2,展示下拉列表，并且默认选择上级分类
                $rows = $categoryModel->getList();
            //2.显示页面
                //分配是当前id对应的数据
                $this->assign($row);
                //所有分类数据
                $this->assign("rows",$rows);
                $this->display('edit');
        }
    }
}