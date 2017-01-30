<?php

namespace DataMapper;


use PDO;

class DataMapper
{

    /** @var  PDO */
    protected $conn;
    protected $sql;
    protected $table;


    public function __construct()
    {
        if(!$this->conn){
            $db = "test";
            $servername = "172.17.0.2";
            $username = "homestead";
            $password = "secret";

            try {
                $conn = new PDO("mysql:host=$servername;dbname=$db;port=3306", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn = $conn;
            }
            catch(\PDOException $e)
            {
                echo "Connection failed: " . $e->getMessage();die;
            }
        }
    }

    public function getConn(){return $this->conn;}


    public function persist(array $fields, array $values){

        $fieldsStr = implode(',',$fields);
        $valuesStr = implode(',',$values);
        $sql = "INSERT INTO {$this->table}
                    ({$fieldsStr})
                    VALUES
                    ({$valuesStr})";
        $this->sql = $sql;
    }

    public function merge(array $fieldValues,array $id){

        $keys = array_keys($id);
        $keyId = reset($keys);
        $valueId = reset($id);

        $values = '';
        $count = 1;
        foreach($fieldValues as $key=>$item){
            if(count($fieldValues) == $count){
                $values .= " {$key} = $item ";
                continue;
            }
            ++$count;
            $values .= " {$key} = $item ,";
        };
        $sql = "UPDATE {$this->table}
                    SET
                    {$values}
                    WHERE {$keyId} = {$valueId};
                    ";
        $this->sql = $sql;
    }

    public function delete($id){
        $this->sql = "DELETE FROM {$this->table} WHERE id={$id}";
        return $this->flush();
    }

    public function flush(){
        /** @var \PDOStatement $stmt */
        $stmt = $this->conn->prepare($this->sql);
        return $stmt->execute();
    }

    public function findAll(){
        $consulta = $this->conn->query("SELECT * FROM {$this->table}");
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findBy(array $fields){

        $where = $this->addWhere($fields);
        $consulta = $this->conn->query("SELECT * FROM {$this->table} $where");
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function addWhere(array $fields){
        $where = 'WHERE ';
        $isFirst = true;
        foreach($fields as $chave=>$value){
            if(!$isFirst)
                $where .= ' AND ';

            $where .= " {$chave} = '{$value}' ";
            $isFirst = false;
        }
        return $where;
    }

}