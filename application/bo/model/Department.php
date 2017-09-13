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
            ->alias('d')
            ->field("d.*")
            ->join('__CIRCULATION__ C', "d.d_id = c.ci_otid AND c.ci_type = 'project'");
        foreach ($search as $key => $value) {
            $searchModel->where('d.' . $value['field'], $value['opt'], $value['val']);
        }
        $searchModel->where("c.ci_mid", "=", $member->m_id);
        $list = $searchModel->paginate($limit, true);
        return $list;
    }
}