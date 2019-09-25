<?php
/**
 * Created by PhpStorm.
 * User: whr_mac
 * Date: 2019/8/28
 * Time: 4:08 PM
 */
namespace App\Controller;
use Swoole;
define('DEBUG', 'on');
define("WEBPATH", str_replace("\\","/", __DIR__));
require __DIR__ . '/../../libs/lib_config.php';

class Ws  extends Swoole\Controller {
    function __construct($swoole)
    {
        parent::__construct($swoole);
    }


}