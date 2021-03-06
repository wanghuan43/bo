<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/9/19
 * Time: 下午1:14
 */

namespace app\bo\validate;


use app\bo\libs\BoValidate;

class Contract extends BoValidate
{

    protected $rule = [
        'c_no' => 'require|alphaDash|unique:\\app\\bo\\model\\Contract',
        'c_name' => 'require',
        'c_coid' => 'require',
        'c_coname' => 'require',
        'c_money' => 'require|money',
        'c_date' => 'require|date',
        'c_accdate' => 'accounting'
    ];

    protected $message = [
        'c_no.require' => '合同号不能为空',
        'c_no.alphaDash' => '合同号格式不正确',
        'c_no.unique' => '合同号已存在',
        'c_name' => '合同名不能为空',
        'c_coname' => '对方公司不能为空',
        'c_coid' => '对方公司不能为空',
        'c_money.require' => '合同金额不能为空',
        'c_money.money' => '合同金额不正确',
        'c_date.require' => '签约日期不能为空',
        'c_date.date' => '签约日期格式不正确',
        'c_accdate' => '记账月格式不正确',
        'c_mname' => '责任人不能为空',
        'c_dname' => '部门不能为空'
    ];

    protected $scene = [
        'import' => ['c_no'=>'require','c_name','c_coname','c_money','c_date','c_mname'=>'require','c_dname'=>'require']
    ];

}