<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/9/19
 * Time: 上午10:31
 */

namespace app\bo\validate;


use think\Validate;

class Project extends Validate
{

    protected $rule = [
        'p_no' => 'require|alphaDash|unique:project',
        'p_name' => 'require',
    ];

    protected $message = [
        'p_no.require' => '项目号不能为空',
        'p_no.alphaDash' => '项目号格式不正确',
        'p_no.unique' => '项目号已存在',
        'p_name' => '项目名称不能为空'
    ];

    protected $scene = [
        'import' => ['p_no' => 'require', 'p_name']
    ];

}