<?php

namespace app\bo\model;

use app\bo\libs\BoModel;

class Taglib extends BoModel
{
    protected $pk = 'tl_id';

    public function getList($search = array(), $limit = 20)
    {
        $member = $this->getCurrent();
        $this->alias('t');
        if (!$member->m_isAdmin) {
            $this->join('__CIRCULATION__ c', "t.tl_id = c.ci_otid AND c.ci_type = 'taglib'");
            $this->where("c.ci_mid", "=", $member->m_id);
        }
        $this->field("t.*");
        foreach ($search as $key => $value) {
            $this->where("t." . $value['field'], $value['opt'], $value['val']);
        }
        $list = $this->paginate($limit);
        return $list;
    }
}