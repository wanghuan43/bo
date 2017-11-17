<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/11/17
 * Time: 下午2:17
 */

namespace app\bo\validate;


use think\Validate;

class Chances extends Validate
{

    protected $rule = [
        'cs_name' => 'require|unique:chances'
    ];

    protected $message = [
        'cs_name.require' => '名称不能为空',
        'cs_name.unique' => '名称已存在'
    ];

}