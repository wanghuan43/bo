<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/9/19
 * Time: 上午12:07
 */

namespace app\bo\validate;


use app\bo\libs\BoValidate;

class Received extends BoValidate
{

    protected $rule = [
        'r_no' => 'require|alphaDash|unique:received',
        'r_coname' => 'require',
        'r_money' => 'require|money',
        'r_date' => 'require|date'
    ];

    protected $message = [
        'r_no.require' => '付款单号不能为空',
        'r_no.alphaDash' => '付款单号格式不正确',
        'r_no.unique' => '付款单号已存在',
        'r_coname' => '对方公司不能为空',
        'r_money.require' => '总金额不能为空',
        'r_money.money' => '总金额格式不正确',
        'r_date.require' => '发生时间不能为空',
        'r_date.date' => '发生时间格式不正确'
    ];

    protected $scene = [
        'import' => ['r_no'=>'require|alphaDash','r_money','r_date']
    ];

}