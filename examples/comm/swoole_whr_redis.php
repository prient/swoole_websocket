<?php
/**
 * Created by PhpStorm.
 * User: whr_mac
 * Date: 2019/8/30
 * Time: 11:10 AM
 */
require __DIR__ . '/swoole_msg.php';
class swoole_whr_redis
{

    private $conf = [
        "host" => "127.0.0.1",
    ];

    static $clients = "clients";//用户hash表
    private $redis;

    public function __construct()
    {
        $this->conf['host'] = strtoupper(substr(PHP_OS,0,6))==='DARWIN'?'127.0.0.1':'172.16.255.198';
        try{
            $this->redis = new Redis();
            $this->redis->connect($this->conf['host']);
        }catch (RedisException $e){
            echo "redis 链接错误".$e;
        }
        return $this->redis;
    }

    //断线重连、唯一ip登录
    function UnionClient($client_id,$res,$self){
        $token = $res['openid'];
        //处理单点登录和断线重新链接
        if(!empty($res['id'])){
            if($this->redis->hExists("clients",$token)){
                $client_info = json_decode($this->redis->hGet("clients",$token),true);
                $client_id_old = $client_info["client_id"];
                $self->closemsg($client_id_old,swoole_msg::msg_unionip_exit());
            }
            $res['client_id'] = $client_id;
            $this->redis->hSet("clients",$token,json_encode($res,true));
            $this->redis->set("key_".$client_id,$token);//做个记录用于反选
//            $self->send($client_id,swoole_msg::msg_login($res['nickname']."欢迎回来~"));

            //通知所有人
            $self->each_client(swoole_msg::msg_login("{$res['nickname']}用户上线了~",$this->UserAll()));
        }else{
            $self->closemsg($client_id,swoole_msg::msg_login_err());
        }
    }

    //获取当前房间所有的在线用户
    function UserAll(){
        return $this->redis->hGetAll("clients");
    }

    //用户ws退出时
    function RemoveUser($client_id,$ws){
        if($this->redis->exists("key_".$client_id)){
            $token = $this->redis->get("key_".$client_id);
            $user_info = json_decode($this->redis->hGet(self::$clients,$token),true);

            if ($this->redis->hDel(self::$clients,$token)){
                echo "用户token：{$token} 退出删除信息成功";
            }
            //退出成功装发信息推送所有人
            $ws->broadcast($client_id,swoole_msg::msg_exit("{$user_info['nickname']}用户下线~",$this->UserAll()));
        }
    }

    //获取用户信息
    function GetUserInfo($data,$ws){
        $info = $this->redis->hGet("clients",$data['token']);
        $ws->each_client(swoole_msg::msg_msg($data['msg'],$info));
    }

    //私聊
    function OnetoOne($data,$ws){
        if(!empty($data['token']) && !empty($data['to_token'])){
            $info_str = $this->redis->hGet("clients",$data['token']);
            $arr[0] = json_decode($info_str,true);
            $arr[1] = json_decode($this->redis->hGet("clients",$data['to_token']),true);
            foreach ($arr as $k=>$v){
                $ws->send($v['client_id'],swoole_msg::msg_msg($data['msg'],$info_str,3,$data['to_token']));
            }
        }
    }
}