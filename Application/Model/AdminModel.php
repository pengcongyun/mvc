<?php
class AdminModel extends Model{

    public function getAll(){
        //1.准备获取管理员数据的sql
        $sql = "select * from admin";
        //2.执行sql
        $rows = $this->db->fetchAll($sql);
        return $rows;
    }

    public function remove($id){
        $sql="delete FROM admin where id ={$id}";
        $this->db->query($sql);
    }

    public function check($username,$password){
        //数据库存储密文，这里需要加密
        $password = md5($password);

        //构造sql语句
        $sql = "select * from admin where username='{$username}' and password='{$password}' limit 1";

        //执行sql
        $result=$this->db->fetchRow($sql);

        //判断
        if(empty($result)){//为空意味着没有找到
            $this->error="账号密码错误!";
            return false;
        }else{
            return $result;
        }

    }

    //根据cookie内容查询用户信息

    public function checkByCookie($id,$password){
        //  1.取出数据

            //构建sql语句
            $sql="select * from admin where id={$id}";
            $result=$this->db->fetchRow($sql);
        //  2.对比数据
        if(empty($result)){
            $this->error="账号密码错误!";
            return false;

        }else {
            $password_in_db = md5($result['password'] . "xxx");
            //  3.返回对比的结果
            if ($password == $password_in_db) {
                return $result;
            } else {
                $this->error = "账号密码错误!";
                return false;
            }
        }

    }
}
