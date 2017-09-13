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
}