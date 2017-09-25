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
        $this->alias('p');
        if (!$member->m_isAdmin) {
            $this->join('__CIRCULATION__ c', "p.p_id = c.ci_otid AND c.ci_type = 'project'");
            $this->where("c.ci_mid", "=", $member->m_id);
        }
        $this->field("p.*");
        foreach ($search as $key => $value) {
            $this->where('p.' . $value['field'], $value['opt'], $value['val']);
        }
        $list = $this->paginate($limit, true);
        return $list;
    }


    /**
     * @param $id
     * @return array
     */
    public function getCodeAndNameById($id )
    {
        $data = $this->getDataById($id);
        return ['code'=>$data['p_no'],'name'=>$data['p_name']];
    }

}