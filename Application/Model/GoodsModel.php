<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/15 0015
 * Time: 上午 10:54
 */
class GoodsModel extends Model
{
    /**
     * 将数据保存到goods中
     * @param $data
     */
    public function add($data){
        //>>1.对数据进一步处理
            //>>1.1 准备添加时间和修改时间
            $data["add_time"] = time();
            $data["update_time"] = time();


            //>>2.2 处理商品状态
            /**
             * 使用一个二进制数的每个位来表示一种状态
             *
             * 初始化商品的状态:0   二进制: 000
             *
             * 第一位表示精品:
             *  000 | 001 = 001         001 ===>   1
             *
             * 第二位表示新品
             * 000 | 010 = 010         010 ===>   2
             *
             * 第三位表示热品
             *  000 | 100 = 100        100 ===>   4
             */
             $status = 0;
             if(isset($data['status'])){  //当用户选择了  商品推荐 情况下
                 foreach($data['status'] as $v){
                     $status = $status | $v;
                 }
             }
         $data['status'] = $status;  //放在data中最终保存到数据库中


        //>>2.在将处理后的数据保存到数据库中
        return parent::insertData($data);
    }


    /**
     * 根据指定的页码查询出当前页的数据
     * @param $page
     * @return array|二维数组
     *
     *  返回一个包含以下内容的数组:
     * [
     *  "rows"=>当前页的列表数据,
     *  "count"=>总记录数,
     *  "pageSize"=>每页多少条
     *  "page"=>当前页码
     *]
     */
    public function getPageResult($page)
    {
        //>>1.从数据库中查询出数据 (准备当前页的列表数据)
        /**
         * select * from 表名 where 1=1 limit 从第几条开始查询,查询出多少条
         */
        $pageSize = 2; //指定要查询出多少条
        $start = ($page-1)*$pageSize;
        $rows = parent::getAll(" 1=1 limit {$start},{$pageSize}");
        //>>2. 对数据处理
        foreach ($rows as &$row) {  //使用引用传值的话,修改row中的数据会直接影响中rows的数据
            $row['is_best'] = ($row['status'] & 1) ? 1 : 0;
            $row['is_new'] = ($row['status'] & 2) ? 1 : 0;
            $row['is_hot'] = ($row['status'] & 4) ? 1 : 0;
            //为什么要使用0和1 赋值给数组, 因为0和1在页面上对应的有图片名字
        }

        //>>3. 查询出数据库中的总条数
            $count = parent::getCount();
        //>>4. 准备分页工具条的html
        $pageHtml = PageTool::show("index.php?p=Admin&c=Goods&a=index",$count,$page,$pageSize);

        return ['rows'=>$rows,"pageHtml"=>$pageHtml];
    }
}