<?php
namespace app\bo\model;

use app\bo\libs\BoModel;

class Project extends BoModel
{
    protected $pk = 'p_id';

    private $searchable = array(
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

    public function getList($search, $limit){
        $db = $this->db();
        $searchModel = $db->table('__PROJECT__');
        foreach($search as $key=>$value){
            $searchModel->where($value['field'], $value['opt'], $value['val']);
        }
        $list = $searchModel->paginate($limit, true);
        return $list;
    }

    /**
     * @return array
     */
    public function getSearchable()
    {
        $fields = array();
        $operators = array();
        foreach ($this->searchable as $key => $value) {
            $fields[$key] = array("name" => $value['name'], "type" => $value['type']);
            $operators[$key] = $value['operators'];
        }
        return array("fields" => $fields, "operators" => $operators, "length" => count($fields));
    }
}