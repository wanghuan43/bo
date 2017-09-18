<?php

namespace app\bo\model;

use app\bo\libs\BoModel;

class Company extends BoModel
{
    protected $pk = 'c_id';

    protected $searchable = array(
        "co_name" => array(
            "name" => "公司名称",
            "type" => "text",
            "operators" => array(
                "like" => "包含",
                "=" => "等于",
            ),
        ),
        "co_code" => array(
            "name" => "公司代码",
            "type" => "text",
            "operators" => array(
                "like" => "包含",
                "=" => "等于",
            ),
        )
    );

    public function getList($search, $limit)
    {
        $member = $this->getCurrent();
        $db = $this->db();
        $searchModel = $db->table('__COMPANY__')
            ->alias('co');
        if (!$member->m_isAdmin) {
            $searchModel->join('__CIRCULATION__ c', "co.co_id = c.ci_otid AND c.ci_type = 'company'");
            $searchModel->where("c.ci_mid", "=", $member->m_id);
        }
            $searchModel->field("co.*");
        foreach ($search as $key => $value) {
            $searchModel->where("co." . $value['field'], $value['opt'], $value['val']);
        }
        $list = $searchModel->paginate($limit, true);
        return $list;
    }
}