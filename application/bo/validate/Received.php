<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/9/19
 * Time: 上午12:07
 */

namespace app\bo\validate;


use app\bo\libs\BoValidate;

class Received extends BoValidate
{

    protected $rule = [
        'r_no' => 'require|alphaDash|unique:received',
        'r_subject' => 'require',
        'r_coname' => 'require',
        'r_money' => 'require|money|moneyValidity',
        'r_date' => 'require|date',
        'r_accdate' => 'accounting'
    ];

    protected $message = [
        'r_no.require' => '付款单号不能为空',
        'r_no.alphaDash' => '付款单号格式不正确',
        'r_no.unique' => '付款单号已存在',
        'r_subject' => '主题不能为空',
        'r_coname' => '对方公司不能为空',
        'r_money.require' => '总金额不能为空',
        'r_money.money' => '总金额格式不正确',
        'r_money.moneyValidity' => '总金额和已对应订单金额冲突',
        'r_date.require' => '发生时间不能为空',
        'r_date.date' => '发生时间格式不正确',
        'r_accdate' => '记账月格式不正确'
    ];

    protected $scene = [
        'import' => ['r_no'=>'require|alphaDash','r_money','r_date']
    ];

    protected function moneyValidity($value,$rule,$data)
    {
        if(isset($data['r_used'])) {
            $value = floatval($value);
            $used = floatval($data['r_used']);
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