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
            ->join('__MEMBER__ m', 'kc.ci_mid = m.m_id')
            ->where('kc.ci_otid', $ot_id)
            ->where('kc.ci_type', $model)
            ->select();
        return $list;
    }
}