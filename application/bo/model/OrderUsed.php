<?php

namespace app\bo\model;

use app\bo\libs\BoModel;

class OrderUsed extends BoModel
{
    protected $pk = "ou_id";

    public function getOrderUesd($id)
    {
        $lists = $this->where("ou_oid", "=", $id)->select();
        $tmp = array();
        foreach ($lists as $value) {
            $tmp[$value['ou_type']][] = array(
                "ou_id" => $value->ou_id,
                "ou_oid" => $value->ou_oid,
                "ou_date" => $value->ou_date,
                "ou_used" => $value->ou_used,
                "ou_type" => $value->ou_type
            );
        }
        $tmp[1] = isset($tmp[1]) ? $tmp[1] : array();
        $tmp[2] = isset($tmp[2]) ? $tmp[2] : array();
        $tmp[3] = isset($tmp[3]) ? $tmp[3] : array();
        return $tmp;
    }

    public function setOrderUsed($id, $data)
    {
        $this->where("ou_oid", "=", $id)->delete();
        $array = array();
        $ka = [
            '1' => 'invoice',
            '2' => 'acceptance',
            '3' => 'received'
        ];
        foreach ($data as $key => $value) {
            $table = ucfirst($ka[$key]);
            foreach ($value['date'] as $k => $val) {
                $i = $table::get($id);
                if ($i->checkUsed($value['ot_id'][$k], $value['value'][$k])) {
                    $tmp['ou_oid'] = $id;
                    $tmp['ou_date'] = strtotime($val);
                    $tmp['ou_otid'] = $value['ot_id'][$k];
                    $tmp['ou_used'] = $value['value'][$k];
                    $tmp['ou_type'] = $key;
                    $array[] = $tmp;
                }
            }
        }
        $this->saveAll($array);
    }
}