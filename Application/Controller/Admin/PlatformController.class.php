<?php
class PlatformController extends Controller{

    public function __construct(){
        $result=$this->checkLogin();

        if($result==false){
            $this->redirect("index.php?p=Admin&c=Login&a=login",3,"请登录");
        }

    }

    public function checkLogin(){
        //开启session
        new SessionDBTool();  //告知PHP,session将保存在数据库中

        //判断$_SESSION['isLogin']是否存在 或者 isLogin!="yes"
        //如果不存在或不等于
        if(!isset($_SESSION['USER_INFO'])){
            //自动登陆,判断cookie 是否有值
            if(isset($_COOKIE['id'])&&isset($_COOKIE['password'])){
                //有值进入
                //接收id和password
                $id=$_COOKIE['id'];
                $password=$_COOKIE['password'];
                //从数据库里面根据id拿出数据
                //实例化AdminModel对象
                $AdminModel=new AdminModel();
                //调用一个方法checkByCookie //查询对比数据
                $result=$AdminModel->checkByCookie($id,$password);
                //判断result
                if($result!==false){
                    //不等于false，进入
                    //业务逻辑 id，password 都正确
                    //因为id，password都正确所以给session yes
                    $_SESSION['USER_INFO']=$result;
                    //$this->redirect("index.php?p=Admin&c=Index&a=index");
                    return true;
                }

            }
            //$this->redirect("index.php?p=Admin&c=Login&a=login",3,"请登录");
            return false;
        }
        return true;
    }


}
