<?php
namespace app\bo\model;

use app\bo\libs\BoModel;

class Taglib extends BoModel
{
    protected $pk = 'tl_id';

    public function getList($search = array(), $limit = 20)
    {
        $member = $this->getCurrent();
        $db = $this->db();
        $searchModel = $db->table('__TAGLIB__')
            ->alias('t')
            ->field("t.*")
            ->join('__CIRCULATION__ c', "t.tl_id = c.ci_otid AND c.ci_type = 'taglib'");
        foreach ($search as $key => $value) {
            $searchModel->where("t." . $value['field'], $value['opt'], $value['val']);
        }
        $searchModel->where("c.ci_mid", "=", $member->m_id);
        $list = $searchModel->paginate($limit, true);
        return $list;
    }
}