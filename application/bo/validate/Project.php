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
        'p_no' => 'require',
        'p_name' => 'require'
    ];

    protected $message = [
        'p_no.require' => '编号不能为空',
        //'p_no.unique' => '编号已存在',
        'p_name' => '名称不能为空'
    ];

}