<?php
namespace app\bo\validate;
use app\bo\libs\BoValidate;

/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/9/18
 * Time: 下午7:18
 */

class Acceptance extends BoValidate
{

    protected $rule = [
        'a_no' => 'require|alphaDash|unique:acceptance',
        'a_subject' => 'require',
        'a_coname' => 'require',
        'a_money' => 'require|money|moneyValidity',
        'a_date' => 'require|date',
        'a_accdate' => 'accounting'
    ];

    protected $message = [
        'a_no.require' => '验收单号不能为空',
        'a_no.alphaDash' => '验收单号格式不正确',
        'a_no.unique' => '验收单号已存在',
        'a_subject' => '主题不能为空',
        'a_coname' => '对方公司不能为空',
        'a_money.require' => '总金额不能为空',
        'a_money.money' => '总金额格式不正确',
        'a_money.moneyValidity' => '总金额和已对应订单金额冲突',
        'a_date.require' => '验收时间不能为空',
        'a_date.date' => '验收日期格式不正确',
        'a_accdate' => '记账月格式不正确'
    ];

    protected $scene = [
        'import' => ['a_no'=>'require']
    ];

    protected function moneyValidity($value,$rule,$data)
    {
        if(isset($data['a_used'])) {
            $value = floatval($value);
            $used = floatval($data['a_used']);
            if( $value >=0 ){
                $ret = $value >= $used ? true : false;
            }else{
                $ret = $value <= $used ? true : false;
            }
        }else{
            $ret =  true;
        }
        return $ret;
    }

}