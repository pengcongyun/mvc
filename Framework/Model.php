<?php
class Model{

    protected $db;

    protected $error;
    //将属性放在父类上, 子类可以覆盖
    protected $table_name ='';

    private $fields = [];

    public function __construct(){
        $this->db = DB::getInstance($GLOBALS['config']['db']);
        $this->initField();
    }

    public function getError(){
        return $this->error;
    }

    /**
     * 获取当前表中所有的列名, 放在成员变量fields中
     *
     * $fields:
     *   [
            "pk"=>"id",  //主键
            "name",
            "age",
            ]
     */
    private function initField(){
        $sql = "desc {$this->table()}";
        $rows  = $this->db->fetchAll($sql);
        foreach($rows as $row){
            if($row['Key']=='PRI'){ //如果是主键的情况下使用pk作为索引
                $this->fields['pk'] =  $row['Field'];
            }else{
                $this->fields[] =  $row['Field'];
            }
        }
    }


    /**
     * 得到一个真实的表名
     * @return string
     */
    public function table(){
        if(empty($this->table_name)){
            //从类的名字上获取表名
            $class_name =  get_class($this);  //得到子类的名字
            $class_name =  substr($class_name,0,-5);  //去掉Model
            $this->table_name =  strtolower($class_name);  //变成小写
        }
        return '`'.$GLOBALS['config']['db']['prefix'] . $this->table_name."`";
    }


    /**
     * 根据给定的条件查询数据
     * @param $condition  查询条件     例如: 字段 like yyy  and 字段 like yyy
     * @return array|二维数组
     */
    public function getAll($condition = ''){
        $sql = "select * from {$this->table()}";

        if(!empty($condition)){  //如果有条件才加where语句
            $sql.=" where ".$condition;
        }

        $rows = $this->db->fetchAll($sql);
        return $rows;
    }



    /**
     * 根据主键的值删除一行数据
     * @param $pk
     * @param $condition  删除的条件
     */
    public function deleteByPk($pk){
        $sql="delete FROM {$this->table()} where {$this->fields['pk']} ={$pk}";
        $this->db->query($sql);
    }

    /**
     *根据条件进行删除指定的数据
     * @param $condition
     */
    public function deleteByCondition($condition){
        $sql="delete FROM {$this->table()} where  $condition";
        $this->db->query($sql);
    }


    /**
     * 根据主键的值得到一行数据
     * @param $pk
     * @return null|一维数组
     */
    public function getByPk($pk){
        $sql = "select * from {$this->table()} where {$this->fields['pk']} = {$pk}";
        return $this->db->fetchRow($sql);
    }


    /**
     * 大前提: 表单的名字必须和数据库中的列名一致.
     *
     *
     * 根据data数据进行动态拼装出insert语句并且保存数据保存到数据库中
     * @param $data
     * $data = ['name'=>'小米','intro'=>'很发烧!'];
     *
     *
     *
     * insert into brand set name = '小米',intro = '很发烧!'
     *
     *
     * @return 返回插入的数据库里面的id
     */
    public function insertData($data){
        //将data中不属于表中的数据删除
        $this->ignoreErrorField($data);

        $sql = "insert into {$this->table()} set ";
        $values = [];
        foreach($data as $k=>$v){
            $values[] = "{$k} = '{$v}'";  //name = '小米'  和  intro = '很发烧!'
        }

        $sql .= implode(",",$values);  //通过,连接起来  name = '小米',intro = '很发烧!'
        $this->db->query($sql);  //执行insert语句

        return $this->db->last_insert_id();
    }


    /**
     * 忽略不合法的数据
     * @param $data  一定要使用引用赋值
     */
    private function ignoreErrorField(&$data){
        foreach($data as $key=>$value){
            if(!in_array($key,$this->fields)){  //判断键是否是表中的字段
                unset($data[$key]); //删除key在data中的值
            }
        }
    }


    /**
     * 前提:  data必须有主键的值
     *
     * 根据data数据进行动态拼装出update语句并且保存数据保存到数据库中
     * @param $data
     * $data = ['name'=>'小米','intro'=>'很发烧!',"id"=>1];
     *
     *
     *
     * update brand set name = '小米',intro = '很发烧!' where id = 1
     * @param $data
     * @param $condition  根据条件更新数据
     */
    public function updateData($data,$condition = ''){

        $this->ignoreErrorField($data); //忽略掉不合法的数据

        $sql = "update {$this->table()} set ";

        $values = [];
        foreach($data as $k=>$v){
            $values[] = "{$k} = '{$v}'";  //name = '小米'  和  intro = '很发烧!'
        }

        $sql .= implode(",",$values);
        if(empty($condition)){
            //将主键作为条件
            $sql.=" where {$this->fields['pk']} = {$data[$this->fields['pk']]}";
        }else{
            $sql.= " where ".$condition;  //如果有条件就根据条件进行更新
        }
        $this->db->query($sql);
    }

    /**
     *  5. 根据条件来得到一行数据
     */
    public  function getRow($condition=''){
        $sql = "select * from {$this->table()}";

        if(!empty($condition)){  //如果有条件就根据条件来
            $sql.= " where {$condition}";
        }
        return $this->db->fetchRow($sql);
    }

    /**
     * 6. 根据条件来得到一行一列的数据
     */
    public function getColumn($column,$condition=''){
        $sql = "select {$column} from {$this->table()}";

        if(!empty($condition)){
            $sql.= " where {$condition}";
        }
        return $this->db->fetchColumn($sql);
    }

    /**
     * 根据条件来统计记录的条数
     */
    public function getCount($condition=''){
       return $this->getColumn("count(*)",$condition);
    }

    /**
     * 执行sql的
     * @param $sql
     * @return bool|mysqli_result
     */
    public function query($sql){
        return $this->db->query($sql);
    }
}
