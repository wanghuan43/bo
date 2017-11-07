<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/9/19
 * Time: 上午12:07
 */

namespace app\bo\validate;


use think\Validate;

class Received extends Validate
{

    protected $rule = [
        'r_no' => 'require',
        'r_money' => 'require|float',
        'r_date' => 'require|number'
    ];

    protected $message = [
        'r_no' => '付款单号不能为空',
        'r_money.require' => '总金额不能为空',
        'r_money.float' => '总金额必须是小数',
        'r_date' => '发生时间不能为空'
    ];


}