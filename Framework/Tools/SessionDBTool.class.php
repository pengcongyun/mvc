<?php

/**
 * 将session数据保存到数据库中
 * 大前提:
 *   准备该表来保存session数据

    CREATE TABLE `session` (
        `sess_id` char(26) NOT NULL,
        `sess_data` text,
        `last_modified` int(11) DEFAULT NULL,
        PRIMARY KEY (`sess_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 */
class SessionDBTool
{
    //因为该属性需要在其他方法中使用
    private $db;

    public function __construct(){
        session_write_close(); //为了防止前面开启session将其关闭

        //告知PHP
        session_set_save_handler(
            array($this,"sess_open"),
            array($this,"sess_close"),
            array($this,"sess_read"),
            array($this,"sess_write"),
            array($this,"sess_destroy"),
            array($this,"sess_gc"));
        //开启session机制
        session_start();//为了创建完对象之后立马使用$_SESSION
    }


   public function sess_open($savePath,$sessionName){
        //完成数据库的连接
        $this->db = DB::getInstance($GLOBALS['config']['db']);
    }

    public  function sess_close(){
        //p("sess_close");
    }

    /**
     * @param $sessionId  可以根据sessionid读取对应的session数据
     */
    public  function sess_read($sessionId){
        //坑: 只是查询出sess_data作为session的数据
        $sql = "select sess_data  from session where  sess_id='{$sessionId}'";
        $sess_data = $this->db->fetchColumn($sql); //执行sql的都唯一的session数据
        if(empty($sess_data)){
            return '';//如果没有查询出sess数据,返回一个''
        }else {
            return $sess_data;  //如果有,返回一个$sess_data
        }
    }

    /**
     * 根据sessionid找到对应的session数据,将$data放进去从而覆盖它
     * @param $sessionId
     * @param $data
     */
    public  function sess_write($sessionId,$data){
        $sql = "insert into session values('{$sessionId}','{$data}',unix_timestamp()) on duplicate key update sess_data = '{$data}',last_modified=unix_timestamp()";
        $this->db->query($sql);
    }

    /**
     * 根据$sessionId删除对应session数据
     * @param $sessionId
     */
    public function sess_destroy($sessionId){
        $sql = "delete from session where sess_id = '{$sessionId}'";
        return $this->db->query($sql);
    }

    /**
     * 当PHP的垃圾回收机制启动的时候该函数执行.
     *
     * $lifetime: 最大的过期时间
     */
    public  function sess_gc($lifetime){
        $sql = "delete from session  where last_modified+{$lifetime} < unix_timestamp()";
        return $this->db->query($sql);
    }

}