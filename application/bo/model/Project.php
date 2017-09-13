<?php
namespace app\bo\model;

use app\bo\libs\BoModel;

class Project extends BoModel
{
    protected $pk = 'p_id';

    protected $searchable = array(
        "p_no" => array(
            "name" => "项目编号",
            "type" => "text",
            "operators" => array(
                "like" => "包含",
                "=" => "等于",
            ),
        ),
        "p_name" => array(
            "name" => "项目名称",
            "type" => "text",
            "operators" => array(
                "like" => "包含",
                "=" => "等于",
            ),
        ),
    );

    public function getList($search = array(), $limit = 20)
    {
        $member = $this->getCurrent();
        $db = $this->db();
        $searchModel = $db->table('__PROJECT__')
            ->alias('p')
            ->field("p.*")
            ->join('__CIRCULATION__ c', "p.p_id = c.ci_otid AND c.ci_type = 'project'");
        foreach ($search as $key => $value) {
            $searchModel->where('p.' . $value['field'], $value['opt'], $value['val']);
        }
        $searchModel->where("c.ci_mid", "=", $member->m_id);
        $list = $searchModel->paginate($limit, true);
        return $list;
    }
}