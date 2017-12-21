<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/9/19
 * Time: 上午10:31
 */

namespace app\bo\validate;


use app\bo\libs\BoValidate;

class Project extends BoValidate
{

    protected $rule = [
        'p_no' => 'require|alphaDash|unique:project',
        'p_name' => 'require',
        'p_dname' => 'require',
        'p_did' => 'require',
        'p_income' => 'money',
        'p_pay' => 'money',
    ];

    protected $message = [
        'p_no.require' => '项目号不能为空',
        'p_no.alphaDash' => '项目号格式不正确',
        'p_no.unique' => '项目号已存在',
        'p_name' => '项目名称不能为空',
        'p_dname' => '立项部门不能为空',
        'p_did' => '立项部门不能为空',
        'p_income' => '总收入格式不正确',
        'p_pay' => '总支出格式不正确'
    ];

    protected $scene = [
        'import' => ['p_no' =>'require|alphaDash', 'p_name','p_income','p_pay']
    ];

}