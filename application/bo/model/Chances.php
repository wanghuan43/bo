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
        if ($id != "") {
            $this->where("cs_id", "=", $id);
            $result = $this->find();
        } else {
            $this->order("cs_name", "ASC");
            $result = $this->select();
        }
        return $result;
    }

    public function getIdByName($name){
        $chance = $this->where('cs_name','=',$name)->find();
        if(empty($chance)){
            $data = ['cs_name'=>$name,'cs_isShow'=>1,'cs_createtime'=>time()];
            $id = $this->insertGetId($data);
        }else{
            $id = $chance->cs_id;
        }
        return $id;
    }

}