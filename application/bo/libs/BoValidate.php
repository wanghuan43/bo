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

    protected function money($value,$rule,$data)
    {
        return $this->regex($value,'/^(([1-9][0-9]*)|(([0]\.\d{0,2}|[1-9][0-9]*\.\d{0,2})))$/');
    }

    protected function phone($vale,$rule,$data)
    {
        return $this->regex($vale,'/^((13[0-9])|(14[5|7])|(15([0-3]|[5-9]))|(18[0,5-9]))\\d{8}$/');
    }

}