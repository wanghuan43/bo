<?php
namespace app\bo\model;

use app\bo\libs\BoModel;

class Circulation extends BoModel
{
    protected $pk = 'ci_id';

    public function getList($ot_id, $model = "orders", $isArray = false)
    {
        $list = $this->alias('kc')
            ->join('__MEMBER__ m', 'kc.ci_mid = m.m_id', "left")
            ->where('kc.ci_otid', $ot_id)
            ->where('kc.ci_type', $model)
            ->select();
        if($isArray){
            foreach($list as $key=>$value){
                $list[$key] = $value->toArray();
            }
        }
        return $list;
    }

    public function setCirculation($ot_id, $list, $model = "orders")
    {
        if(count($list) == 0){
            $this->where("ci_otid", "=", $ot_id)->where("ci_type", "=", $model)->delete();
        }
        $cl = array();
        foreach ($list as $value) {
            $cl[] = array("ci_otid" => $ot_id, "ci_type" => $model, "ci_mid" => $value);
        }
        if (count($cl) > 0) {
            //$this->saveAll($cl);
            $this->insertDuplicate($cl);
        }
    }
}