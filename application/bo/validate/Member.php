<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/9/27
 * Time: 上午11:45
 */

namespace app\bo\validate;


use app\bo\libs\BoValidate;

class Member extends BoValidate
{

    protected $rule = [
        'm_email' => 'require|email|unique:\\app\\bo\\model\\Member',
        'm_code' => 'require|unique:\\app\\bo\\model\\Member',
        'm_name' => 'require',
        'm_phone' => 'require|phone',
        'm_department' => 'require'
    ];

    protected $message = [
        'm_email.require' => 'Email不能为空',
        'm_email.email' => 'Email错误',
        'm_email.unique' => '该Email用户已存在',
        'm_code.require' => '编码不能为空',
        'm_code.unique' => '该编码的用户已存在',
        'm_name' => '姓名不能为空',
        'm_phone.require' => '电话号码不能为空',
        'm_phone.phone' => '电话号码不正确',
        'm_department' => '科室不能为空'
    ];

    protected $scene = [
        'import' => ['m_email'=>'require|email','m_code'=>'require','m_name']
    ];


}