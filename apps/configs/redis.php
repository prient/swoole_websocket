<?php
$redis['master'] = array(
//    'host'    => "127.0.0.1",
    'host'    => strtoupper(substr(PHP_OS,0,6))==='DARWIN'?'127.0.0.1':'172.16.255.198',
    'port'    => 6379,
    'password' => '',
    'timeout' => 0.25,
    'pconnect' => false,
//    'database' => 1,
);
return $redis;
