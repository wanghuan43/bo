<?php
namespace app\bo\model;

use app\bo\libs\BoModel;

class Received extends BoModel
{

    protected $pk = "i_id";

    private $searchable = array(
        "r_date" => array(
            "name" => "付款日期",
            "type" => "date",
            "operators" => array(
                "between" => "介于",
                "=" => "等于",
                "<" => "大于",
                ">" => "小于",
            ),
        ),
        "r_money" => array(
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

    public function getList($search = array(), $limit = 20)
    {
        $member = $this->getCurrent();
        $db = $this->db();
        $searchModel = $db->table('__RECEIVED__')
            ->alias('r')
            ->field("r.*")
            ->join('__CIRCULATION__ c', "r.r_id = c.ci_otid AND c.ci_type = 'received'");
        foreach ($search as $key => $value) {
            $searchModel->where("r." . $value['field'], $value['opt'], $value['val']);
        }
        $searchModel->where("c.ci_mid", "=", $member->m_id);
        $list = $searchModel->paginate($limit, true);
        return $list;
    }
}