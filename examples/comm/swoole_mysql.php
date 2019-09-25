<?php
/**
 * Created by PhpStorm.
 * User: whr_mac
 * Date: 2019/8/30
 * Time: 9:49 AM
 */
//namespace Swoole\Exception;
class swoole_mysqls{
    private $param;
    public $db;
    public function __construct() {
        $this->db = new swoole_mysql;
        $this->param = array(
            'host' => '127.0.0.1',
            'user' => 'root',
            'password' => '123123',
            'database' => 'unmshop',
            'charset' => 'utf8', //指定字符集
            'timeout' => 2
        );
    }
    public function exec($sql,$data) {
        $this->db->connect($this->param, function ($db, $result) use ($sql,$data) {
            if ($result === false) {
                echo "连接数据库失败 ： 错误代码：" . $db->connect_errno . PHP_EOL . $db->connect_error;
                return false;
            }
            $db->query($sql, function ($db, $res) {
                if ($res === false) {
                    // error属性获得错误信息，errno属性获得错误码
                    die("sql语句执行错误 : " . $db->error);
                } else if ($res === true) {
                    // 非查询语句  affected_rows属性获得影响的行数，insert_id属性获得Insert操作的自增ID
                    echo "sql语句执行成功，影响行数 : " . $db->affected_rows;

                } else {
                    //查询语句  $result为结果数组
                    var_dump($res);
                }
                $db->close();
            });
        });
    }
}