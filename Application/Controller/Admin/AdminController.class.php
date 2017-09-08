<?php

class AdminController extends PlatformController{

    public function index(){

        $admin_model = new AdminModel();
        $rows = $admin_model->getAll();

        //第二种
        $student=["name"=>"xiaohong","age"=>12,"sex"=>"woman"];

        $this->assign($student);

        //
        $this->assign('rows',$rows);





        //require CURRENT_VIEW_PATH."index.html";


        $this->display("index");
    }
    public function remove(){


        $admin_model=new AdminModel();
        $id=$_GET['id'];

        $admin_model->remove($id);

        $this->redirect("index.php?p=Admin&c=Admin&a=index",2,"提示信息");


    }




}
