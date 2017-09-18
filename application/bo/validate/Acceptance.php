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
        'a_mid' => 'require|number',
        'a_mname' => 'require',
        'a_coid' => 'require|number',
        'a_coname' => 'require',
        'a_money' => 'require|float',
        'a_date' => 'require|number'
    ];

    protected $message = [
        'a_no' => '验收单号不能为空',
        'a_mid.require' => '责任人ID不能为空',
        'a_mid.number' => '责任人ID必须为数字',
        'a_mname' => '责任人不能为空',
        'a_coid.require' => '对方公司ID不能为空',
        'a_coname.number' => '对方公司ID必须为数字',
        'a_money.require' => '总金额不能为空',
        'a_money.float' => '总金额必须是小数',
        'a_date' => '验收时间不能为空'
    ];

}