<?php
class LoginController extends Controller{

    public function login(){

        $this->display("login");

    }

    public function check(){
        //开启session
        new SessionDBTool(); //告知PHP,要使用数据库保存session数据库


        //对验证码进行验证(用户录入的验证码和保存在session中的random_code)
            //>>1.1 接收用户录入的验证码
              $captcha = $_POST['captcha'];
              if(!CaptchaTool::check($captcha)){
                  $this->redirect("index.php?p=Admin&c=Login&a=login",3,"验证码输入错误!");
              }


        //接收用户传递的账号密码
        $username = $_POST['username'];
        $password = $_POST['password'];
        //数据已经接收
        //验证数据
        //1.声明一个AdminModel对象
        $AdminModel= new AdminModel();
        $result=$AdminModel->check($username,$password);
        if($result!==false){

            $_SESSION['USER_INFO']=$result;
            //接收用户传递记录登陆信息的标识
            $remember=$_POST['remember'];
            if($remember==1){
                //setcookie

                $password=md5($result['password']."xxx");

                //保存id到cookie
                setcookie("id",$result['id'],time()+60*60*24,"/");
                setcookie("password",$password,time()+60*60*24,"/");

            }
            $this->redirect("index.php?p=Admin&c=Index&a=index");

        }

        //没进上面if,没有查询到结果，就跳转到登录
        $this->redirect("index.php?p=Admin&c=Login&a=login",3,$AdminModel->getError());
    }


    public function logout(){
        new SessionDBTool();
        //清空session
        session_unset();
        session_destroy();
        //清空cookie
        setcookie("id","",-1,"/");
        setcookie("password","",-1,"/");
        $this->redirect("index.php?p=Admin&c=Login&a=login");
    }



}
