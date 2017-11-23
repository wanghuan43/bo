<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/11/17
 * Time: 下午4:12
 */

namespace app\bo\libs;


use think\Validate;

class BoValidate extends Validate
{
    //金额验证
    protected function money($value,$rule,$data)
    {
        return $this->regex($value,'/^\-?(([1-9][0-9]*)|(([0]\.\d{0,2}|[1-9][0-9]*\.\d{0,2})))$/');
    }

    //电话号码验证
    protected function phone($value,$rule,$data)
    {
        return $this->regex($value,'/^((13[0-9])|(14[5|7])|(15([0-3]|[5-9]))|(18[0,5-9]))\\d{8}$/');
    }

    //记账月验证,格式:yymm,例:1711
    protected function accounting($value,$rule,$data)
    {
        return $this->regex($value,'/^[0-9][0-9](0[1-9]|1[0-2])$/');
    }

}