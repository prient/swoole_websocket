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
        $this->dsn = strtoupper(substr(PHP_OS,0,6))==='DARWIN'?"mysql:host=127.0.0.1;dbname=unmshop":"mysql:host=cdb-k88xysui.bj.tencentcdb.com;port=10025;dbname=unmshop";
        $this->password = strtoupper(substr(PHP_OS,0,6))==='DARWIN'?'123123':'HZYsWJQMONmZtYnP123';
        echo $this->dsn;
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