<?php

namespace app\bo\model;

use app\bo\libs\BoModel;

class Chances extends BoModel
{
    protected $pk = 'cs_id';

    public function getChanges($id = "")
    {
        $current = $this->getCurrent();
        if(!$current->m_isAdmin){
            $this->where("cs_mid", "=", $current->m_id);
        }
        if (!empty($id)) {
            $this->where("cs_id", "=", $id);
            $result = $this->find();
        } else {
            $this->order("cs_name", "ASC");
            $result = $this->select();
        }
        return $result;
    }
}