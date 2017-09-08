<?php
class IndexController extends PlatformController{



    public function index(){

        $this->display("index");

    }

    //输出top页面
    public function top(){

        //assign 方法给name赋值
        //1.通过id查出username //数据
        //2.cookie
        //3.session
        $this->assign("name", $_SESSION['USER_INFO']['username']);
        $this->display("top");
    }

    public function menu(){
//        @session_start();
//        if(!isset($_SESSION['isLogin'])||$_SESSION['isLogin']!="yes"){
//            $this->redirect("index.php?p=Admin&c=Login&a=login",3,"请登录！");
//
//
//        }
        $this->display("menu");
    }
    public function main(){
//        @session_start();
//        if(!isset($_SESSION['isLogin'])||$_SESSION['isLogin']!="yes"){
//            $this->redirect("index.php?p=Admin&c=Login&a=login",3,"请登录！");
//
//
//        }
        $this->display("main");
    }


}