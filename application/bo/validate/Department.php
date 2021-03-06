<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/9/29
 * Time: 下午2:48
 */

namespace app\bo\validate;


use think\Validate;

class Department extends Validate
{

    protected $rule = [
        'd_name' => 'require|unique:\\app\\bo\\model\\Department',
        'd_code' => 'require|unique:\\app\\bo\\model\\Department|alphaDash'
    ];

    protected $message = [
        'd_name.require' => '部门名称不能为空',
        'd_name.unique' => '部门已存在',
        'd_code.require' => '成本中心编码不能为空',
        'd_code.unique' => '成本中心编码已存在',
        'd_code.alphaDash' => '成本中心编码格式不正确'
    ];

    protected $scene = [
        'import' => ['d_name','d_code']
    ];

}