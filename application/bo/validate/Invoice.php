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
        'i_mid' => 'require|number',
        'i_mname' => 'require',
        'i_coid' => 'require|number',
        'i_coname' => 'require',
        'i_money' => 'require|float',
        'i_date' => 'require|number'
    ];

    protected $message = [
        'i_no' => '验收单号不能为空',
        'i_mid.require' => '责任人ID不能为空',
        'i_mid.number' => '责任人ID必须为数字',
        'i_mname' => '责任人不能为空',
        'i_coid.require' => '对方公司ID不能为空',
        'i_coname.number' => '对方公司ID必须为数字',
        'i_money.require' => '总金额不能为空',
        'i_money.float' => '总金额必须是小数',
        'i_date' => '验收时间不能为空'
    ];
}