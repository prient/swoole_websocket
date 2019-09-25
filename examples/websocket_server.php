<?php
define('DEBUG', 'on');
define("WEBPATH", str_replace("\\","/", __DIR__));
ini_set('date.timezone','Asia/Shanghai');
require __DIR__ . '/../libs/lib_config.php';
require __DIR__ . '/comm/swoole_pdos.php';
require __DIR__ . '/comm/swoole_whr_redis.php';

class WebSocket extends Swoole\Protocol\WebSocket
{
    protected $message;

    /**
     * @param     $serv swoole_server
     * @param int $worker_id
     */
    function onStart($serv, $worker_id = 0)
    {
        Swoole::$php->router(array($this, 'router'));
        parent::onStart($serv, $worker_id);
    }

    function router()
    {
        var_dump($this->message);
    }

    /**
     * 进入
     * @param $client_id
     */
    function onEnter($client_id,$requery)
    {
        $token = explode('=',$requery->meta['uri'])[1];
        $db = new swoole_pdos();
        $res = $db->query_sql("select * from fa_unmuser where openid='{$token}'");
//        var_dump($res[0]['id']);
        $redis = new swoole_whr_redis();
        $redis->UnionClient($client_id,$res[0],$this);



//        $client_all = $redis->hGetAll("clients");
//        var_dump($client_all);
    }

    /**
     * 下线时，通知所有人
     */
    function onExit($client_id)
    {

        $redis = new swoole_whr_redis();
        $redis->RemoveUser($client_id,$this);
        //将下线消息发送给所有人
        //$this->log("onOffline: " . $client_id);
        //$this->broadcast($client_id, "onOffline: " . $client_id);
    }

    function onMessage_mvc($client_id, $ws)
    {
        $this->log("onMessage: ".$client_id.' = '.$ws['message']);

        $this->message = $ws['message'];
        $response = Swoole::$php->runMVC();

        $this->send($client_id, $response);
        //$this->broadcast($client_id, $ws['message']);
    }

    /**
     * 接收到消息时
     * type = 0 ping
     * type = 2 群聊
     * type = 3 私聊
     *
     */
    function onMessage($client_id, $ws)
    {
        $this->log("onMessage: ".$client_id.' = '.$ws['message']);
        $data = json_decode($ws['message'],true);
        if($data['type'] == 0){
            $this->send($client_id, swoole_msg::msg_pinpong());
        }else{
            $redis = new swoole_whr_redis();
            if($data['type'] == 2){
                //群聊
                $redis->GetUserInfo($data,$this);
            }elseif ($data['type'] == 3){
                //私聊 todo
                $redis->OnetoOne($data,$this);
            }else{

            }
        }
    }

    function broadcast($client_id, $msg)
    {
        $redis = new swoole_whr_redis();
        $list = $redis->UserAll();
//        var_dump($list);
        foreach ($list as $clid => $info)
        {
            $info = json_decode($info,true);
            if ($client_id != $info['client_id'])
            {
                $this->send($info['client_id'], $msg);
            }
        }
    }

    function closemsg($client_id,$msg){
        $msg = json_decode($msg,true);
        $this->close($client_id,$msg['code'],$msg['msg']);
    }


    function each_client($msg)
    {
        $redis = new swoole_whr_redis();
        $list = $redis->UserAll();
//        var_dump($list);
        foreach ($list as $clid => $info)
        {
            $info = json_decode($info,true);
            $this->send($info['client_id'], $msg);
        }
    }


}

//require __DIR__'/phar://swoole.phar';
Swoole\Config::$debug = true;
Swoole\Error::$echo_html = false;

$AppSvr = new WebSocket();
$AppSvr->loadSetting(__DIR__."/swoole.ini"); //加载配置文件
$AppSvr->setLogger(new \Swoole\Log\EchoLog(true)); //Logger

/**
 * 如果你没有安装swoole扩展，这里还可选择
 * BlockTCP 阻塞的TCP，支持windows平台
 * SelectTCP 使用select做事件循环，支持windows平台
 * EventTCP 使用libevent，需要安装libevent扩展
 */
$enable_ssl = false;
$server = Swoole\Network\Server::autoCreate('0.0.0.0', 9443, $enable_ssl);
$server->setProtocol($AppSvr);
//$server->daemonize(); //作为守护进程
$server->run(array(
    'worker_num' => 1,
    'ssl_key_file' => __DIR__.'/ssl/ssl.key',
    'ssl_cert_file' => __DIR__.'/ssl/ssl.crt',
    //'max_request' => 1000,
    //'ipc_mode' => 2,
    //'heartbeat_check_interval' => 40,
    //'heartbeat_idle_time' => 60,
));
