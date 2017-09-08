<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/16 0016
 * Time: 上午 11:44
 */
class ImageTool
{
    private $error;


    private   $create_funs = [
        'image/jpeg'=>'imagecreatefromjpeg',
        'image/gif'=>'imagecreatefromgif',
        'image/png'=>'imagecreatefrompng',
    ];


    private  $out_funs = [
        'image/jpeg'=>'imagejpeg',
        'image/gif'=>'imagegif',
        'image/png'=>'imagepng',
    ];




    /**
     * 缩略图
     * @param $src_filename  原图片的路径
     * @param $max_width  缩略图宽
     * @param $max_height  缩略图高
     * @type 处理方式  1: 补白  2: 裁剪  3.xxxx
     * @return bool|string;  失败返回false, 成功返回 字符串
     */
    public function thumb($src_filename,$max_width,$max_height,$type=1){
        //根据大图片的路径生成一个小图片的路径
        $pathinfo = pathinfo($src_filename);
        $small_path = $pathinfo['dirname'].'/'.$pathinfo['filename'].'_50x50.'.$pathinfo['extension'];


        $src_filename = _UPLOADS_.$src_filename;  //因为传递过来的是相对路径,将其变为绝对路径
        //>>1.判断该文件是否存在
        if(!is_file($src_filename)){
            $this->error = "原图片不存在!";
            return false;
        }

        //>>2. 准备原图片和目标图片对象
        $imagesize = getimagesize($src_filename);
        list($src_width,$src_height) = $imagesize; //将数组的0,1 索引上的值赋值给前面的两个变量
        $mime_type = $imagesize['mime'];  //原图片类型

        $create_fun = $this->create_funs[$mime_type];  //可变函数
        $src = $create_fun($src_filename);  //原图片对象


        $thumb = imagecreatetruecolor($max_width,$max_height);


        //>>3. 等比例缩放
           //>>3.1 补白(将目标图片的北京变成白色)
            if($type==1){
                $white = imagecolorallocate($thumb,255,255,255);
                imagefill($thumb,0,0,$white);
            }elseif($type==2){
                //裁剪..
            }


           //>>3.2 计算缩放大小
             $scale = max($src_width/$max_width,$src_height/$max_height);
             $width = $src_width/$scale;
             $height = $src_height/$scale;

           //>>3.3 进行缩放
          imagecopyresampled($thumb,$src,($max_width-$width)/2,($max_height-$height)/2,0,0,$width,$height,$src_width,$src_height);



        //>>4.保存目标图片
        $out_fun = $this->out_funs[$mime_type];

        $out_fun($thumb,_UPLOADS_.$small_path);  //以绝对路径的形式输出

        return $small_path; //返回相对路径
    }


    public function getError(){
        return $this->error;
    }

}