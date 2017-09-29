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
        ['d_name','require','名称不能为空'],
        ['d_code','require','编码不能为空']
    ];

}