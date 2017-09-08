<?php
class Framework{


    //统一调用
    public static function run(){
        //自定义类自动加载函数
        //加载方法的三种方式
        //spl_autoload_register("Framework::userAutoload");
        spl_autoload_register(array("Framework","userAutoLoad"));
        //spl_autoload_register(array(self,"userAutoload"));

        //保持顺序绝对不能改变
        self::initPath();
        self::initConfig();
        self::initRequestParams();
        self::initClassMap();//在dispatch之前定义所需要使用的框架类全局变量
        self::dispatch();



    }

    //初始化定义路径常量
    public static function initPath(){
        defined("DS") or define("DS",DIRECTORY_SEPARATOR);
        defined("_ROOTPATH_") or define("_ROOTPATH_",dirname($_SERVER['SCRIPT_FILENAME']).DS);//根目录路径
        defined("_APPLICATION_") or define("_APPLICATION_",_ROOTPATH_."Application".DS); //Application路径
        defined("_CONFIG_") or define("_CONFIG_",_APPLICATION_."Config".DS); //配置文件路径
        defined("_CONTROLLER_") or define("_CONTROLLER_",_APPLICATION_."Controller".DS); //控制器路径
        defined("_MODEL_") or define("_MODEL_",_APPLICATION_."Model".DS); //model 路径
        defined("_VIEW_") or define("_VIEW_",_APPLICATION_."View".DS); //view路径

        defined("_FRAMEWORK_") or define("_FRAMEWORK_",_ROOTPATH_."Framework".DS);
        defined("_TOOLS_") or define("_TOOLS_",_FRAMEWORK_."Tools".DS);
        defined("_UPLOADS_") or define("_UPLOADS_",_ROOTPATH_.'Uploads'.DS);
    }

    //加载配置文件
    public static function initConfig(){
        $GLOBALS['config'] = require _CONFIG_."application.config.php";

    }

    //初始化请求参数

    public static function initRequestParams(){
        $p=isset($_GET['p'])?$_GET['p']:$GLOBALS['config']['default']['plantform'];
        $c=isset($_GET['c'])?$_GET['c']:$GLOBALS['config']['default']['controller'];
        $a=isset($_GET['a'])?$_GET['a']:$GLOBALS['config']['default']['action'];

        defined("CURRENT_CONTROLLER_PATH") or define("CURRENT_CONTROLLER_PATH",_CONTROLLER_.$p.DS);
        defined("CURRENT_VIEW_PATH") or  define("CURRENT_VIEW_PATH",_VIEW_.$p.DS.$c.DS);

        defined("PLANTFORM_NAME")or define("PLANTFORM_NAME",$p);
        defined("CONTROOLER_NAME")or define("CONTROOLER_NAME",$c);
       defined("ACTION_NAME")or  define("ACTION_NAME",$a);
    }



    //分发请求

    public static function dispatch(){
        $controller_name = CONTROOLER_NAME."Controller";


//echo $controller_name;
        $controller = new $controller_name();
        $a=ACTION_NAME;
        $controller->$a();

    }


    //映射框架类
    public static function  initClassMap(){
        $GLOBALS['class_mapping']=[
            "DB"=>_TOOLS_."DB.class.php",
            "Model"=>_FRAMEWORK_."Model.php",
            "Controller"=>_FRAMEWORK_."Controller.php"

        ];
    }

    //类自动加载
    public static function userAutoLoad($class_name){
        //类的自动加载


        //第三步无任何输出，估计判断条件错误，所以判断后的代码永不执行
        //第三步打印判断条件
        //var_dump($GLOBALS['class_mapping'][$class_name]);
        //die();

        if(isset($GLOBALS['class_mapping'][$class_name])){
            //因为第二步判断条件正确，所以估计错误在第一个判断当中
            //第三步打印 全局变量
            //var_dump($GLOBALS['class_mapping'][$class_name]);
            //die();
            require $GLOBALS['class_mapping'][$class_name];

        }elseif(substr($class_name,-10)=="Controller"){
           //因为第一步正确
           //所以打印判断条件
           // 第二步打印echo $class_name;
            //echo "<br>";
            //报错在这里
            //打印报错行
            //第一步先打印：echo CURRENT_CONTROLLER_PATH .$class_name.".class.php";
            //die();
            require CURRENT_CONTROLLER_PATH .$class_name.".class.php";//粘贴AdminController.class.php代码
        }elseif(substr($class_name,-5)=="Model"){
            require _MODEL_.$class_name.".php";//粘贴AdminModel
        }elseif(substr($class_name,-4)=="Tool"){
            require _TOOLS_.$class_name.".class.php";//加载以AdminModel
        }
    }

}
