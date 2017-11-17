<?php
namespace app\bo\validate;
use app\bo\libs\BoValidate;

/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/9/18
 * Time: 下午7:18
 */

class Acceptance extends BoValidate
{

    protected $rule = [
        'a_no' => 'require|alphaDash|unique:acceptance',
        'a_coname' => 'require',
        'a_money' => 'require|money',
        'a_date' => 'require|date'
    ];

    protected $message = [
        'a_no.require' => '验收单号不能为空',
        'a_no.alphaDash' => '验收单号格式不正确',
        'a_no.unique' => '验收单号已存在',
        'a_coname' => '对方公司不能为空',
        'a_money.require' => '总金额不能为空',
        'a_money.money' => '总金额格式不正确',
        'a_date.require' => '验收时间不能为空',
        'a_date.date' => '验收日期格式不正确'
    ];

    protected $scene = [
        'import' => ['a_no'=>'require','a_money','a_date']
    ];

}