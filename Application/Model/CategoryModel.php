<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/30 0030
 * Time: 上午 11:19
 */
class CategoryModel extends Model
{
    /**
     * 获取排序好的分类数据
     */
    public function getList($parent_id = 0){
        //1.获取说有商品分类数据
            $rows = $this->getAll();
        //2.对数据进行排序
            return $this->getChildren($rows,$parent_id,0);
    }

    /**
     * 查找当前分类的儿子
     */
    private function getChildren(&$rows, $parent_id = 0 , $deep = 0)
    {
        static $children = [];//使用一个静态局部变量存放所有儿子的数组
        foreach ($rows as $child) {//循环数组，比对，找到需要的儿子
            if ($child['parent_id'] == $parent_id) {
                //将已经带有缩进的名称单独使用一个字段保存
                $child['name_txt'] = str_repeat("&emsp;",$deep*2).$child['name'];
                $children[] = $child;
                //继续查找到的儿子的儿子
                $this->getChildren($rows, $child['id'],$deep+1);
            }
        }
        return $children;
    }

    /**
     *父类上的删除方法不满足需求，需要重写
     * @param $pk  主键的值
     */
    public function deleteByPk($id)
    {
        //a.删除的节点下面不能存在子节点
        /**
         * 子节点的数量大于0，表明有子节点，不能删除
         */
        $count = $this->getCount("parent_id={$id}");
        if ($count>0) {
            $this->error = "当前节点下面存在子节点，不能删除！";
            return false;
        }
        parent::deleteByPk($id);
    }

    /**
     * 添加方法不满足需求，需要重写该方法，实现我们的逻辑
     */
    public function insertData($data)
    {
        //a.分类名称不能为空
        if (empty($data['name'])){
            $this->error = "分类名称不能为空！";
            return false;
        }
        //b.同级分类的分类名称不能重名
        /**
         * 如果统计$data['name']的数量，如果大于0,不满足要求
         */
        $count = $this->getCount("parent_id={$data['parent_id']} and name='{$data['name']}'");
        if ($count > 0) {
            $this->error = '同级分类下已经存在该分类';
            return false;
        }
        return parent::insertData($data);
    }

    /**
     * 父分类上的更新方法不满足需求，重写它
     */
    public function update($new_data)
    {
        //a.分类名称不能为空
            if(empty($new_data['name'])) {
                $this->error = "分类名称不能为空！";
                return false;
            }
        //b.修改后不能与同级分类的其他分类名称重复
            $count = $this->getCount("parent_id={$new_data['parent_id']} and name='{$new_data['name']}' and id <> {$new_data['id']}");
            if ($count > 0) {
                $this->error = "修改后不能与同级分类的其他分类名称重复";
                return false;
            }
        //c.不能修改到自己的子孙分类下面以及自己下面
            /**
             * 父分类id parent_id 不能等于子孙分类的id，和自己的id
             */
            //1,查询到所有子孙
            $children = $this->getList($new_data['id']);
            //2.自己的id

            //3.使用一个容器，存放自己的id和子孙的id,所有不满足要求的id
            $ids = array_column($children,"id");
            $ids[] = $new_data['id'];

            if (in_array($new_data['parent_id'],$ids)) {
                $this->error = "不能修改到自己的子孙分类下面以及自己下面";
                return false;
            }
            parent::updateData($new_data);
    }

}