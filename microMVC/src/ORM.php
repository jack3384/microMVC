<?php

namespace jikai\microMVC;

class ORM
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $keyValue;
    protected $fields = array();
    protected $dbName='database';

    /**
     * Model constructor.
     * @param $table
     * @param null $id
     * @param array $option
     * option 为设置 primaryKey,dbName等
     */
    public function __construct($table, $id = null,array $option=array())
    {
        foreach($option as $key=>$value){
            if(isset($this->$key))$this->$key=$value;
        }
        $this->connect();
        $this->table = $table;
        if (!empty($id)) {
            $this->keyValue = $id;
            $this->find($id);
        }

    }

    protected function connect()
    {
        $this->db = Factory::DB($this->dbName);
    }

    //无数据返回值是false
    public function find($id)
    {
        $selectStatement = $this->db->select()
            ->from($this->table)
            ->where($this->primaryKey, '=', $id);
        $stmt = $selectStatement->execute();
        $data = $stmt->fetch();
        $this->fields = $data;
        $this->keyValue = $id;
        return $data;
    }

    public function  findAll($offset = 0, $number = 1000)
    {
        $selectStatement = $this->db->select()
            ->from($this->table)
            ->limit($number,$offset);
        $stmt = $selectStatement->execute();
        $data = $stmt->fetchAll();
        return $data;
    }

    public function  listAll(array $fields, $offset = 0, $number = 1000)
    {
        $selectStatement = $this->db->select($fields)
            ->from($this->table)
            ->limit($number,$offset);
        $stmt = $selectStatement->execute();
        $data = $stmt->fetchAll();
        return $data;
    }

    /**
     * @param $fields
     * @return mixed
     * 使用方法：['username'=>'value','password'=>'1233']这样形式传数组
     * $getALL=true 表示返回所有，否则只返回一条
     * 无数据$getALL=false返回的是false,有数据一维数组
     * 加上$getALL=true无数据返回空数组，有数据二维数组
     */
    public function findByFields($fields,$getALL=false)
    {
        $selectStatement = $this->db->select()
            ->from($this->table);
            foreach($fields as $key=>$val){
                $selectStatement=$selectStatement->where($key, '=', $val);
            }
        $stmt = $selectStatement->execute();
        if($getALL===true){
            $data = $stmt->fetchAll();
        }else{
            $data = $stmt->fetch();
        }
        return $data;
    }

    /**
     * @param array $array
     * @param bool|false $getALL
     * @return mixed
     */
    public function findByWhere(array $array,$getALL=false)
    {
        $selectStatement = $this->db->select()
            ->from($this->table);
         foreach($array as $val){
              $selectStatement=$selectStatement->where($val[0], $val[1], $val[2]);
         }
        $stmt = $selectStatement->execute();
        if($getALL===true){
            $data = $stmt->fetchAll();
        }else{
            $data = $stmt->fetch();
        }
        return $data;
    }

    public function setTable($table)
    {
        $this->table = $table;
    }

    public function __set($name, $value)
    {
        $this->fields[$name] = $value;
    }

    public function __get($name)
    {
        return $this->fields[$name];
    }

    protected function update()
    {
        // UPDATE users SET pwd = ? WHERE id = ?
        $updateStatement = $this->db->update($this->fields)
            ->table($this->table)
            ->where($this->primaryKey, '=', $this->keyValue);
        return $affectedRows = $updateStatement->execute();
    }

    protected function insert()
    {
        // INSERT INTO users ( id , usr , pwd ) VALUES ( ? , ? , ? )
        $insertStatement = $this->db->insert(array_keys($this->fields))
            ->into($this->table)
            ->values(array_values($this->fields));
        return $insertId = $insertStatement->execute();
    }

    public function delete()
    {
        if (empty($this->keyValue)) {
            return false;
        }
        // DELETE FROM users WHERE id = ?
        $deleteStatement = $this->db->delete()
            ->from($this->table)
            ->where($this->primaryKey, '=', $this->keyValue);
        $affectedRows = $deleteStatement->execute();
        return $affectedRows;
    }

    public function save()
    {
        if (empty($this->keyValue)) {
            return $this->insert();
        } else {
            return $this->update();
        }
    }

    public function store(array $info)
    {
        foreach ($info as $name=>$value){
            $this->fields[$name] = $value;
        }
        if (empty($this->keyValue)) {
            return $this->insert();
        } else {
            return $this->update();
        }
    }

    public function getFields()
    {
        return $this->fields;
    }
}