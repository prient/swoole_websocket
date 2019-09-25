<?php
/**
 * Created by PhpStorm.
 * User: whr_mac
 * Date: 2019/8/30
 * Time: 11:28 AM
 *
 * code: 10001/20001/30001
 * type: 0/1/2/3
 * msg_type: down/login/msg/msg_onetoone
 */

class swoole_msg
{
    static $data = [
        "code"=>"",
        "msg"=>"",
        "result"=>"",
        "type"=>0,
        "userlist"=>[],
        "to_token"=>[],
    ];

    public function __construct()
    {
//        $redis = new swoole_whr_redis();
//        $list = $redis->UserAll();
//        var_dump($list);
//        self::$data['userlist'] = $list;
    }



    //握手消息
    static function msg_pinpong($msg = "pong"){
        self::$data["code"]="10001";
        self::$data["msg"]=$msg;
        self::$data["result"]=["msg"=>$msg];;
        self::$data["type"]=1;
        return json_encode(self::$data,true);
    }

    //唯一登录ip、被迫下线
    static function msg_unionip_exit($msg = "从新登录,被迫下线~"){
        self::$data["code"]="20001";
        self::$data["msg"]=$msg;
        self::$data["result"]=self::msg_type("down","",$msg);
        self::$data["type"]=9;
        return json_encode(self::$data,true);
    }

    //登录成功消息
    static function msg_login($msg = "欢迎回来~",$list = []){
        self::$data["code"]="10011";
        self::$data["msg"]=$msg;
        self::$data["result"]=self::msg_type("login","",$msg);

        foreach ($list as $k=>$v){
            $res= json_decode($v,true);
            $userlist[$k] = ["token"=>$res['openid'],"nickname"=>$res['nickname'],"images"=>$res['headimgurl']];
        }
        self::$data["userlist"]= $userlist;
        return json_encode(self::$data,true);
    }

    //登录失败消息
    static function msg_login_err($msg = "用户鉴权失败~"){
        self::$data["code"]="20011";
        self::$data["msg"]=$msg;
        self::$data["result"]=self::msg_type("down","",$msg);
        return json_encode(self::$data,true);
    }

    //唯一登录ip、被迫下线
    static function msg_exit($msg = "退出~",$list = []){
        self::$data["code"]="20002";
        self::$data["msg"]=$msg;
        self::$data["result"]=self::msg_type("down","",$msg);
        self::$data["type"]=8;
        foreach ($list as $k=>$v){
            $res= json_decode($v,true);
            $userlist[$k] = ["token"=>$res['openid'],"nickname"=>$res['nickname'],"images"=>$res['headimgurl']];
        }
        self::$data["userlist"]= $userlist;
        return json_encode(self::$data,true);
    }

    //消息事件
    static function msg_msg($msg,$info,$type=2,$to_token=""){
        self::$data["code"]="30002";
        self::$data["msg"]=$msg;
        self::$data["type"]=$type;
        self::$data["to_token"]=$to_token;
        $info = json_decode($info,true);
        $user_info = ["id"=>$info['id'],"token"=>$info['openid'],"nickname"=>$info['nickname'],"images"=>$info['headimgurl']];
        $ms_type = ($type==2)?"msg":"msg_onetoone";
        self::$data["result"] = self::msg_type($ms_type,$user_info,$msg,$to_token);
        return json_encode(self::$data,true);
    }

    //消息结构
    static function msg_type($msg_type,$user_info="",$msg,$token=""){
        return [
            "msg_type"=>$msg_type,
            "msg"=>$msg,
            "createdate"=>date("Y-m-d H:i:s"),
            "user_info"=>$user_info,
            "to_token"=>$token,
        ];
    }
}
