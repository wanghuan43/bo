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
        'p_no' => 'require|unique:\\app\\bo\\model\\Project',
        'p_name' => 'require'
    ];

    protected $message = [
        'p_no' => '编号不能为空',
        'p_name' => '名称不能为空'
    ];

}