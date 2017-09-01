<?php
namespace app\bo\model;

use think\Model;

class Circulation extends Model
{
    public function getCirculationList($ot_id, $model)
    {
        $db = $this->db();
        $list = $db->table('__CIRCULATION__')
            ->alias('kc')
            ->join('__MEMBER__ m', 'kj.ci_mid = m.m_id')
            ->where('kc.ot_id', $ot_id)
            ->where('kc.model', $model)
            ->select();
        return $list;
    }
}