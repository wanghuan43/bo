<?php

namespace app\bo\model;

use app\bo\libs\BoModel;

class Department extends BoModel
{
    protected $pk = 'd_id';

    public function getList($search = array(), $limit = 20)
    {
        $member = $this->getCurrent();
        $db = $this->db();
        $searchModel = $db->table('__DEPARTMENT__')
            ->alias('d');
        if (!$member->m_isAdmin) {
            $searchModel->join('__CIRCULATION__ c', "d.d_id = c.ci_otid AND c.ci_type = 'project'");
            $searchModel->where("c.ci_mid", "=", $member->m_id);
        }
        $searchModel->field("d.*");
        foreach ($search as $key => $value) {
            $searchModel->where('d.' . $value['field'], $value['opt'], $value['val']);
        }
        $list = $searchModel->paginate($limit, true);
        return $list;
    }
}