<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/9/27
 * Time: 上午11:45
 */

namespace app\bo\validate;


use think\Validate;

class Member extends Validate
{

    protected $rule = [
        ['m_email','email|unique:\\app\\bo\\model\\Member','Email错误|该Email用户已存在'],
        ['m_code','unique:member','该编码的用户已存在'],
        ['m_password','require','密码不能为空'],
        ['m_name','require','姓名不能为空'],
        ['m_did','require','部门ID不能为空']
    ];

}