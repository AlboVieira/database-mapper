<?php

namespace DataMapper;


use Code\System\Entity\Customer;
use Code\System\Entity\Interfaces\EntityInterface;
use PDO;

class Connection
{

    /** @var  PDO */
    private $conn;

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

}