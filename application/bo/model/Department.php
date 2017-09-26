<?php

namespace app\bo\model;

use app\bo\libs\BoModel;

class Department extends BoModel
{
    protected $pk = 'd_id';

    public function getList($search = array(), $limit = 20)
    {
        $member = $this->getCurrent();
        $this->alias('d');
        if (!$member->m_isAdmin) {
            $this->join('__CIRCULATION__ c', "d.d_id = c.ci_otid AND c.ci_type = 'project'");
            $this->where("c.ci_mid", "=", $member->m_id);
        }
        $this->field("d.*");
        foreach ($search as $key => $value) {
            $this->where('d.' . $value['field'], $value['opt'], $value['val']);
        }
        $list = $this->paginate($limit, true);
        return $list;
    }
}