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

}