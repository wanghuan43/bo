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
        'r_mid' => 'require|number',
        'r_mname' => 'require',
        'r_coid' => 'require|number',
        'r_coname' => 'require',
        'r_money' => 'require|float',
        'r_date' => 'require|number'
    ];

    protected $message = [
        'r_no' => '付款单号不能为空',
        'r_mid.require' => '责任人ID不能为空',
        'r_mid.number' => '责任人ID必须为数字',
        'r_mname' => '责任人不能为空',
        'r_coid.require' => '对方公司ID不能为空',
        'r_coname.number' => '对方公司ID必须为数字',
        'r_money.require' => '总金额不能为空',
        'r_money.float' => '总金额必须是小数',
        'r_date' => '发生时间不能为空'
    ];


}