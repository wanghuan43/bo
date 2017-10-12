<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/9/19
 * Time: 下午1:14
 */

namespace app\bo\validate;


use think\Validate;

class Contract extends Validate
{

    protected $rule = [
        ['c_no','require|unique:\\app\\bo\\model\\Contract','合同号不能为空|合同已存在'],
        ['c_name','require','合同名不能为空']
    ];

}