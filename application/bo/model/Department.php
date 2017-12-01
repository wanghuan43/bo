<?php

namespace app\bo\model;

use app\bo\libs\BoModel;
use think\Request;

class Department extends BoModel
{
    protected $pk = 'd_id';

    protected $searchable = [
        "d_name" => array(
            "name" => "科室名称",
            "type" => "text",
            "operators" => array(
                "like" => "包含"
            ),
        ),
        "d_code" => array(
            "name" => "科室编码",
            "type" => "text",
            "operators" => array(
                "like" => "包含"
            ),
        )
    ];

    public function getList($search = array(), $limit = 20)
    {
        $member = $this->getCurrent();
        $this->alias('d');
        /*if (!$member->m_isAdmin) {
            $this->join('__CIRCULATION__ c', "d.d_id = c.ci_otid AND c.ci_type = 'department'");
            $this->where("c.ci_mid", "=", $member->m_id);
        }*/
        $this->field("d.*");
        foreach ($search as $key => $value) {
            $this->where('d.' . $value['field'], $value['opt'], $value['val']);
        }
        if($limit===false){
            $list = $this->select();
        }else {
            $list = $this->paginate($limit);
        }
        return $list;
    }

    public function getDepartmentIdByName($dName)
    {
        $res = $this->where('d_name','=',$dName)->find();
        if($res) {
            $ret = $res->d_id;
        }else{
            $ret = false;
        }
        return $ret;
    }

}