<?php

namespace app\bo\model;

use app\bo\libs\BoModel;

class OrderUsed extends BoModel
{
    protected $pk = "ou_id";

    protected $ka = [
        '1' => '\app\bo\model\Invoice',
        '2' => '\app\bo\model\Acceptance',
        '3' => '\app\bo\model\Received'
    ];

    public function getOrderUesd($id)
    {
        $lists = $this->where("ou_oid", "=", $id)->select();
        $tmp = array();
        foreach ($lists as $value) {
            $tmp[$value['ou_type']][] = array(
                "ou_id" => $value->ou_id,
                "ou_oid" => $value->ou_oid,
                "ou_otid" => $value->ou_otid,
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

    public function resetOrderUsed($id)
    {
        $list = $this->where("ou_oid", "=", $id)->select();
        foreach ($list as $value) {
            $table = $this->ka[$value['ou_type']];
            $i = new $table();
            $i->checkUsed($value['ou_otid'], $value['ou_used'], "+");
        }
    }

    public function setOrderUsed($id, $data)
    {
        $this->where("ou_oid", "=", $id)->delete();
        $array = array();
        foreach ($data as $key => $value) {
            $table = $this->ka[$key];
            foreach ($value['date'] as $k => $val) {
                $i = new $table();
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