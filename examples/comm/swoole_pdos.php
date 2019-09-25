<?php
/**
 * Created by PhpStorm.
 * User: whr_mac
 * Date: 2019/8/30
 * Time: 10:31 AM
 */

class swoole_pdos
{
    private $dsn = "mysql:host=127.0.0.1;dbname=unmshop";
    private $account = "root";
    private $password = "123123";
    private $db;
    public function __construct()
    {
        try{
            $this->db = new PDO($this->dsn,$this->account,$this->password);
        }catch (PDOException $e){
            echo "Error!: " . $e->getMessage() . "<br/>";
        }
    }

    public function query_sql($sql){
        $data = [];
        foreach ($this->db->query($sql) as $k=>$v){
            $data[] = $v;
        }
        return $data;
    }
}