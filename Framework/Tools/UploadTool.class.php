<?php

/**
    上传功能
 */
class UploadTool
{

    /**
     * 文件大小
     * @var
     */
    private $max_size;

    /**
     * 允许上传的类型
     * @var
     */
    private $allow_types;

    /**
     * 保存错误信息
     * @var
     */
    private $error;

    /**
     * 设计构造函数
     * @param $max_size
     * @param $allow_types
     */
    public function __construct($max_size='', $allow_types='')
    {
         //如果用户没有指定就从配置文件中获取
        $this->max_size = empty($max_size)?$GLOBALS['config']['upload']['max_size']:$max_size;
        $this->allow_types = empty($allow_types)?$GLOBALS['config']['upload']['allow_types']:$allow_types;
    }


    /**
     * 将根据文件信息$fileinfo将文件上传到$path指定目录下
     * @param $fileinfo
     * @param $path
     */
    public function uploadOne($fileinfo,$path){
        //>>1. 判断文件上传成功是否上传成功
        if($fileinfo['error']!==0){
            $this->error = '文件上传失败!';
            return false;
        }

        //>>2. 判断文件的大小
        if($fileinfo['size'] > $this->max_size){
            $this->error = '文件超过2mb!';
            return false;
        }

        //>>3. 判断文件的类型
        if(!in_array($fileinfo['type'],$this->allow_types)){
            $this->error =  '文件类型不合法!';
            return false;
        }

        //>>4. 为了防止图片覆盖,为上传文件重新命名一个唯一的名字
        /**
         *  规则:  ymdhis+随机数.'.源文件后缀'
         *  例如: 20161015172301随机字符串.jpg
         *
         *   后缀可以从源文件中获取
         *
         */

        $srcfilename  = $fileinfo['name'];
        $extension = strrchr($srcfilename,'.');
        $filename = uniqid().$extension;  //生成一个新的文件名字


        //保证 文件目录  一定是存在的.
        $dir = _UPLOADS_.$path.'/'.date('Ymd');
        if(!is_dir($dir)){  //如果不是一个目录
            mkdir($dir,0777,true); //true:: 递归创建目录
        }

        $filepath = $path.'/'.date('Ymd')."/".$filename;   //相对路径

        //>>5. $_FILES中的文件信息是否是通过浏览器上传的
        if(!is_uploaded_file($fileinfo['tmp_name'])){
            $this->error  =   '该文件不是通过浏览器上传的!';
           return false;
        }

        if(!move_uploaded_file($fileinfo['tmp_name'],_UPLOADS_.$filepath)){
            $this->error =  '临时文件移动失败!';
            return false;
        }

        return  $filepath;
    }


    /**
     * 上传同名的多个文件
     * @param $fileinfos
     * @param $path
     */
    public function uploadMore($fileinfos,$path){

        $filepaths = [];//保存上传后的路径

        foreach($fileinfos['error'] as $k=>$error){
            if($error!=0){
                continue;
            }
            //构建出每个上传文件信息
            $fileinfo = [];
            $fileinfo['name'] =  $fileinfos['name'][$k];
            $fileinfo['type'] =  $fileinfos['type'][$k];
            $fileinfo['tmp_name'] =  $fileinfos['tmp_name'][$k];
            $fileinfo['size'] =  $fileinfos['size'][$k];
            $fileinfo['error'] = $error;

            //将文件信息传递给$fileinfo
            $result = $this->uploadOne($fileinfo,$path);
            if($result!==false){  //上传成功之后就是 路径
                $filepaths[] = $result;
            }

        }

        return $filepaths;

    }

    /**
     * 获取错误信息
     */
    public function getError(){
         return  $this->error;
    }

}