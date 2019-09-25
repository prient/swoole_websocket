<?php
$db['master'] = array(
    'type'       => Swoole\Database::TYPE_MYSQLi,
//    'host'       => "127.0.0.1",
    'host'    => strtoupper(substr(PHP_OS,0,6))==='DARWIN'?'127.0.0.1':'cdb-k88xysui.bj.tencentcdb.com',
    'port'       => 3306,
    'dbms'       => 'mysql',
    'engine'     => 'MyISAM',
    'user'       => "root",
    'passwd'     => strtoupper(substr(PHP_OS,0,6))==='DARWIN'?'123123':'HZYsWJQMONmZtYnP123',
    'name'       => "unmshop",
    'charset'    => "utf8",
    'setname'    => true,
    'persistent' => false, //MySQL长连接
    'use_proxy'  => false,  //启动读写分离Proxy
    'slaves'     => array(
        array('host' => '127.0.0.1', 'port' => '3307', 'weight' => 100,),
        array('host' => '127.0.0.1', 'port' => '3308', 'weight' => 99,),
        array('host' => '127.0.0.1', 'port' => '3309', 'weight' => 98,),
    ),
);

$db['slave'] = array(
    'type'       => Swoole\Database::TYPE_MYSQLi,
    'host'       => "127.0.0.1",
    'port'       => 3306,
    'dbms'       => 'mysql',
    'engine'     => 'MyISAM',
    'user'       => "root",
    'passwd'     => "root",
    'name'       => "live",
    'charset'    => "utf8",
    'setname'    => true,
    'persistent' => false, //MySQL长连接
);

return $db;