<?php
/**
 * Created by PhpStorm.
 * User: whr_mac
 * Date: 2018/12/14
 * Time: 4:02 PM
 */
namespace App\Controller;
use App\Model\Account;
use Swoole;
header("Access-Control-Allow-Origin:*");


class Demo extends Swoole\Controller
{
    function __construct($swoole)
    {
        parent::__construct($swoole);
    }

    function index()
    {
        $order_model = model('Unmorder');
        $info = $order_model->get(4);
        echo "<pre>";
        print_r($info);
        return $this->showTrace(true);

    }

    function test(){
        for ($i=0;$i<100000000;$i++){
            if(($i%10000000) == 0){
                echo $i;
            }
        }
        return $this->showTrace(true);
    }

    function tickdemo(){

    }

    function login(){
        empty($_GET['account'])?die(json_encode(["code"=>0,"msg"=>"账号不能为空"])):"";
        empty($_GET['password'])?die(json_encode(["code"=>0,"msg"=>"密码不能为空"])):"";
        $acc_model = model("Account");
        $res = $acc_model->get("{$_GET['account']}","openid")->getOriginalData();
        if (empty($res)){
            return $this->json($res,0,"ok");
        }else{
            return $this->json($res,1,"ok");
        }
    }

}