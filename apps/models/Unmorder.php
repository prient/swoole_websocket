<?php
/**
 * Created by PhpStorm.
 * User: whr_mac
 * Date: 2018/12/14
 * Time: 4:01 PM
 */
namespace App\Model;
use Swoole;

class Unmorder extends Swoole\Model
{
    /**
     * 表名
     * @var string
     */
    public $table = 'fa_unmorder';
}