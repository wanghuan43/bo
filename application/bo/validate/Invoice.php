<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/9/19
 * Time: 上午12:42
 */

namespace app\bo\validate;


use app\bo\libs\BoValidate;

class Invoice extends BoValidate
{
    protected $rule = [
        'i_no' => 'require|alphaDash|unique:invoice',
        'i_subject' => 'require',
        'i_coname' => 'require',
        'i_money' => 'require|money',
        'i_date' => 'require|date'
    ];

    protected $message = [
        'i_no.require' => '发票号不能为空',
        'i_no.alphaDash' => '发票号格式不正确',
        'i_no.unique' => '发票号已存在',
        'i_subject' => '主题不能为空',
        'i_coname' => '对方公司不能为空',
        'i_money.require' => '总金额不能为空',
        'i_money.money' => '总金额格式不正确',
        'i_date.require' => '开票日期不能为空',
        'i_date.date' => '开票日期格式不正确'
    ];

    protected $scene = [
        'import' => ['i_no'=>'require','i_money','i_date']
    ];

}