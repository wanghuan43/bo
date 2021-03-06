<?php
namespace app\bo\model;

use app\bo\libs\BoModel;

class OrderProject extends BoModel
{
    protected $pk = "op_id";

    public function getOrderProject($op_id)
    {
        $lists = $this->where("op_oid", "=", $op_id)->select();
        $tmp = array();
        foreach ($lists as $value) {
            $tmp[$value['op_type']][] = array(
                "op_id" => $value->op_id,
                "op_oid" => $value->op_oid,
                "op_date" => $value->op_date,
                "op_month" => $value->op_month,
                "op_percent" => $value->op_percent,
                "op_used" => $value->op_used,
                "op_type" => $value->op_type
            );
        }
        $tmp[1] = isset($tmp[1]) ? $tmp[1] : array();
        $tmp[2] = isset($tmp[2]) ? $tmp[2] : array();
        $tmp[3] = isset($tmp[3]) ? $tmp[3] : array();
        return $tmp;
    }

    public function setOrderProject($op_id, $date, $list)
    {
        $this->where("op_oid", "=", $op_id)->delete();
        $array = array();
        if(!is_array($list)){
            return false;
        }
        foreach ($list as $key => $value) {
            foreach ($value['month'] as $k => $val) {
                $tmp['op_oid'] = $op_id;
                $tmp['op_date'] = strtotime($val);
                $tmp['op_month'] = $val;
                $tmp['op_percent'] = $value['percent'][$k];
                $tmp['op_used'] = $value['value'][$k];
                $tmp['op_type'] = $key;
                $array[] = $tmp;
            }
        }
        $this->saveAll($array);
    }
}