<?php
namespace app\bo\model;

use think\Model;

class Circulation extends Model
{
    public function getCirculationList($ot_id, $model="orders")
    {
        $db = $this->db();
        $list = $db->table('__CIRCULATION__')
            ->alias('kc')
            ->join('__MEMBER__ m', 'kc.ci_mid = m.m_id')
            ->where('kc.ci_otid', $ot_id)
            ->where('kc.ci_type', $model)
            ->select();
        return $list;
    }

    public function setCirculation($ot_id, $list, $model="orders"){
        $this->where("ci_otid", "=", $ot_id)->where("ci_type", "=", $model)->delete();
        $cl = array();
        foreach($list as $value){
            $cl[] = array("ci_otid"=>$ot_id, "ci_type"=>$model, "ci_mid"=>$value);
        }
        if(count($cl) > 0){
            $this->saveAll($cl);
        }
    }
}