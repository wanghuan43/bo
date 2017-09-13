<?php
namespace app\bo\model;

use app\bo\libs\BoModel;

class Contract extends BoModel
{
    protected $pk = "c_id";

    private $searchable = array(
        "c_name" => array(
            "name" => "合同名称",
            "type" => "text",
            "operators" => array(
                "like" => "包含",
                "=" => "等于",
            ),
        ),
        "c_money" => array(
            "name" => "合同金额",
            "type" => "price",
            "operators" => array(
                "between" => "介于",
                "=" => "等于",
                "<" => "大于",
                ">" => "小于",
            ),
        ),
        "c_date" => array(
            "name" => "签约日期",
            "type" => "date",
            "operators" => array(
                "between" => "介于",
                "=" => "等于",
                "<" => "大于",
                ">" => "小于",
            ),
        ),
        "c_coname" => array(
            "name" => "对方名称",
            "type" => "text",
            "operators" => array(
                "like" => "包含",
                "=" => "等于",
            ),
        ),
    );

    public function getList($search, $limit)
    {
        $member = $this->getCurrent();
        $db = $this->db();
        $searchModel = $db->table('__CONTRACT__')
            ->alias('ct')
            ->field("ct.*")
            ->join('__CIRCULATION__ C', "ct.c_id = c.ci_otid AND c.ci_type = 'contract'");
        foreach ($search as $key => $value) {
            $searchModel->where("ct." . $value['field'], $value['opt'], $value['val']);
        }
        $searchModel->where("c.ci_mid", "=", $member->m_id);
        $list = $searchModel->paginate($limit, true);
        return $list;
    }
}