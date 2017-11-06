<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/9/19
 * Time: 上午12:42
 */

namespace app\bo\validate;


use think\Validate;

class Invoice extends Validate
{
    protected $rule = [
        'i_no' => 'require',
        'i_money' => 'require|float',
        'i_date' => 'require|number'
    ];

    protected $message = [
        'i_no' => '发票号不能为空',
        'i_money.require' => '总金额不能为空',
        'i_money.float' => '总金额必须是小数',
        'i_date' => '开票时间不能为空'
    ];
}