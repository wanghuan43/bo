<?php
namespace app\bo\model;

use app\bo\libs\BoModel;

class Acceptance extends BoModel
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

    public function getList($search, $limit)
    {
        $member = $this->getCurrent();
        $db = $this->db();
        $searchModel = $db->table('__ACCEPTANCE__')
            ->alias('a')
            ->field("a.*")
            ->join('__CIRCULATION__ c', "a.a_id = c.ci_otid AND c.ci_type = 'acceptance'");
        foreach ($search as $key => $value) {
            $searchModel->where('a.' . $value['field'], $value['opt'], $value['val']);
        }
        $searchModel->where("c.ci_mid", "=", $member->m_id);
        $list = $searchModel->paginate($limit, true);
        return $list;
    }
}