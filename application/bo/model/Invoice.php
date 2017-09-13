<?php
namespace app\bo\model;

use app\bo\libs\BoModel;

class Invoice extends BoModel
{
    protected $pk = "i_id";

    private $searchable = array(
        "i_date" => array(
            "name" => "发票日期",
            "type" => "date",
            "operators" => array(
                "between" => "介于",
                "=" => "等于",
                "<" => "大于",
                ">" => "小于",
            ),
        ),
        "i_money" => array(
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
        $searchModel = $db->table('__INVOICE__')
            ->alias('i')
            ->field("i.*")
            ->join('__CIRCULATION__ C', "i.i_id = c.ci_otid AND c.ci_type = 'invoice'");
        foreach ($search as $key => $value) {
            $searchModel->where("i." . $value['field'], $value['opt'], $value['val']);
        }
        $searchModel->where("c.ci_mid", "=", $member->m_id);
        $list = $searchModel->paginate($limit, true);
        return $list;
    }
}