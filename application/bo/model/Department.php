<?php

namespace app\bo\model;

use app\bo\libs\BoModel;
use think\Request;

class Department extends BoModel
{
    protected $pk = 'd_id';

    protected $searchable = [
        "d_name" => array(
            "name" => "部门名称",
            "type" => "text",
            "operators" => array(
                "like" => "包含",
                "=" => "等于",
            ),
        ),
        "d_code" => array(
            "name" => "部门编码",
            "type" => "text",
            "operators" => array(
                "like" => "包含",
                "=" => "等于",
            ),
        )
    ];

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
        $list = $this->paginate($limit);
        return $list;
    }
}