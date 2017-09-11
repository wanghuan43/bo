<?php
namespace app\bo\model;

use think\Model;

class Acceptance extends Model
{

    protected $pk = "i_id";

    private $searchable = array(
        "a_date" => array(
            "name" => "验收日期",
            "type" => "date",
            "operators" => array(
                "between" => "介于",
                "=" => "等于",
                "<" => "大于",
                ">" => "小于",
            ),
        ),
        "a_money" => array(
            "name" => "金额",
            "type" => "price",
            "operators" => array(
                "between" => "介于",
                "=" => "等于",
                "<" => "大于",
                ">" => "小于",
            ),
        ),
    );

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

    public function getAcceptanceList($search, $limit)
    {
        $db = $this->db();
        $searchModel = $db->table('__ACCEPTANCE__');
        foreach ($search as $key => $value) {
            $searchModel->where($value['field'], $value['opt'], $value['val']);
        }
        $list = $searchModel->paginate($limit, true);
        return $list;
    }
}