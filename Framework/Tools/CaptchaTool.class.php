<?php

/**
 *
 */
class CaptchaTool
{

    /*
     * 生成一个指定长度的字符串
     */
    private static function makeCode($length){
        //>>3.1 生成一个包含 0-9  a-z的数组
        $strs = array_merge(range(0,9),range('A','Z'));
        //                  $strs = "1234567890SDFSDFSF";
        //>>3.2 将数组变成字符串
        $strs = implode("",$strs);
        //>>3.3 将字符串打乱
        $strs = str_shuffle($strs);
        //>>4.4 截取前几位
        $random_code = substr($strs,0,$length);

        return $random_code;
    }

    /**
     * 验证码
     */
    public static  function generate($length = 6){
        header('Content-Type: image/jpeg;charset=utf-8');
        //>>1. 背景随机变化
        $imagefile = _TOOLS_."captcha/captcha_bg".mt_rand(1,5).".jpg";
        $imagesizes  = getimagesize($imagefile);
        list($width,$height) = $imagesizes;

        $image = imagecreatefromjpeg($imagefile);
        //>>2. 在图片上加上白色边框
        //>>2.1 分配白色
        $white = imagecolorallocate($image,255,255,255);
        //>>2.2 在图片上画一个矩形
        imagerectangle($image,0,0,$width-1,$height-1,$white);
        //>>3. 生成一个随机的字符串
        $random_code =  self::makeCode($length);


        //>>4.将文字写在图片上
        $black = imagecolorallocate($image,0,0,0);
        imagestring($image,5,$width/3,$height/8,$random_code,mt_rand(0,1)?$white:$black);


        //>>5. 向图片上添加点或者线干扰视线
        /*for($i=0;$i<50;++$i){
            $color = imagecolorallocate($image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imagesetpixel($image , mt_rand(0,$width) , mt_rand(0,$height) ,$color);
        }

        for($i=0;$i<2;++$i){
            $color = imagecolorallocate($image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imageline($image,mt_rand(0,$width) , mt_rand(0,$height),mt_rand(0,$width) , mt_rand(0,$height),$color);
        }*/







        //同时也将随机字符串保存到session中
        new SessionDBTool();
        $_SESSION["random_code"] = $random_code;

        imagejpeg($image);
        imagedestroy($image);
    }


    /**
     * 对用户录入的验证码进行验证
     */
    public static   function check($captcha){
        new SessionDBTool();
        $random_code = $_SESSION['random_code'];
        return  strtolower($captcha)==strtolower($random_code);
    }
}