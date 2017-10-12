<?php
namespace app\bo\validate;
use think\Validate;

/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/9/25
 * Time: 下午12:00
 */

class Company extends Validate
{

    protected $rule = [
        ['co_code','require|unique:\\app\\bo\\model\\Company','编码不能为空|编码已存在'],
        ['co_name','require','名称不能为空']
    ];

}