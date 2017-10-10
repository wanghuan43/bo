<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/9/19
 * Time: 下午1:14
 */

namespace app\bo\validate;


use think\Validate;

class Contract extends Validate
{

    protected $rule = [
        'c_no' => 'require',
        'c_name' => 'require',
        'c_pid' => 'require|number',
        'c_pname' => 'require',
        //'c_coid' => 'require|number',
        'c_coname' => 'require',
        'c_type' => 'require',
        //'c_mid' => 'require|number',
        'c_mname' => 'require',
        'c_money' => 'require|float',
        //'c_date' => 'require|number'
    ];

    protected $message = [
        'c_no' => '合同号不能为空',
        'c_name' => '合同名不能为空',
        'c_mid.require' => '责任人ID不能为空',
        'c_mid.number' => '责任人ID必须为数字',
        'c_mname' => '责任人不能为空',
        'c_coid.require' => '对方公司ID不能为空',
        'c_coname.number' => '对方公司ID必须为数字',
        'c_money.require' => '总金额不能为空',
        'c_money.float' => '总金额必须是小数',
        'c_date' => '日期不能为空'
    ];

}