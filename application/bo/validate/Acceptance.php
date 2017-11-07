<?php
namespace app\bo\validate;
use think\Validate;

/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/9/18
 * Time: 下午7:18
 */

class Acceptance extends Validate
{

    protected $rule = [
        'a_no' => 'require',
        'a_money' => 'require|float',
        'a_date' => 'require'
    ];

    protected $message = [
        'a_no' => '验收单号不能为空',
        'a_money.require' => '总金额不能为空',
        'a_money.float' => '总金额必须是小数',
        'a_date' => '验收时间不能为空'
    ];

}