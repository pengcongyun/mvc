<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/25 0025
 * Time: 10:05
 */
class TestController extends Controller
{
    //导入数据
    public function inputdata(){

        $md=new MdModel();
//        $data=$md->getAll();
//        $archive=new Archive();
//        $archivedes=new ArchiveDescrip();
//        $archiveresu=new ArchiveResume();
        $count=$md->getCount();
        var_dump($count);exit;
    }
}