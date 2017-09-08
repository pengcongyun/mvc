<?php
class BrandController extends Controller{

    public function index(){
        $brand_model = new BrandModel();
        $rows = $brand_model->getAll();
        $this->assign("rows",$rows);
        $this->display("index");
    }
    public function remove(){
        $brand_model = new BrandModel();
        $id=$_GET['id'];

        $brand_model->deleteByPk($id);
        $this->redirect("index.php?p=Admin&c=Brand&a=index");
    }


    public function add(){
        if($_SERVER['REQUEST_METHOD']=="POST"){
            //将表单中的数据添加到数据库表中
            $brand_model = new BrandModel();
            $brand_model->insertData($_POST);
            $this->redirect("index.php?p=Admin&c=Brand&a=index");
        }else{
            $this->display('add');
        }
    }


    public function edit(){
        if($_SERVER['REQUEST_METHOD']=="POST"){
            $brand_model = new BrandModel();
            $brand_model->updateData($_POST);

            $this->redirect("index.php?p=Admin&c=Brand&a=index");
        }else{
            $id = $_GET['id'];
            $brand_model = new BrandModel();
            $row = $brand_model->getByPk($id);
            $this->assign($row);
            $this->display('edit');
        }
    }



}
