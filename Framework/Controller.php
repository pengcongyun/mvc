<?php
class Controller{
    private $data=[];

    public function display($template){
        //第一种解决方法
        // $rows=$GLOBALS['rows'];
        //第二种
//        $rows=$this->data['rows'];
//        $name= $this->data['name'];
//        $sex=$this->data['sex'];
//        $age=$this->data['age'];
        extract($this->data);//将datas中的数据解析成变量.  变量名就是键的名字
        require CURRENT_VIEW_PATH.$template.".html";
    }

    public function assign($key,$value=""){

        if(is_array($key)){
            //$student=["name"=>"xiaohong","age"=>12,"sex"=>"woman"];
            $this->data=array_merge($this->data,$key);
            //讲解
            //$key-->$value
            //"name"=>"xiaohong", extract后的实际效果  $name = "xiaohong"
            //"age"=>12,
            //"sex"=>"woman"
        }else {
            $this->data[$key] = $value;
        }

    }

    //跳转方法
    public function redirect($url,$time=0,$msg=''){
        //前面执行了flush（）,headers_sent()返回true，未执行返回false
        if(headers_sent()){
            //用js 跳转，因为header 被发送
            //是否延时跳转
            if($time==0){
                //立即跳转
                echo <<<js
                <script type="text/javascript">
                    location.href("{$url}");
                </script>
js;
            }else{
                $time=$time*1000;
                echo $msg;
                echo <<<js
                <script type="text/javascript">
                    window.setTimeout(function(){
                    location.href("{$url}");
                    },$time);

                </script>
js;

            }

        }else{
            //是否延时跳转

            if($time==0){
                //立即跳转
                header("Location: {$url}");}
            else{
                echo $msg;
                //延时跳转
                header("Refresh: {$time};{$url}");
            }
        }

        exit();
    }

}
