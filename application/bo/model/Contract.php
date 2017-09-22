<?php

namespace app\bo\model;

use app\bo\libs\BoModel;

class Contract extends BoModel
{
    protected $pk = "c_id";

    protected $searchable = array(
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
            ->alias('ct');
        if (!$member->m_isAdmin) {
            $searchModel->join('__CIRCULATION__ c', "ct.c_id = c.ci_otid AND c.ci_type = 'contract'")
                ->where("c.ci_mid", "=", $member->m_id);
        }
        $searchModel->field("ct.*,p.p_no,cp.co_type");
        $searchModel->join('__PROJECT__ p', "ct.c_pid = p.p_id");
        $searchModel->join('__COMPANY__ cp', "ct.c_coid = cp.co_id");
        foreach ($search as $key => $value) {
            $searchModel->where("ct." . $value['field'], $value['opt'], $value['val']);
        }
        $list = $searchModel->paginate($limit, true);
        return $list;
    }

    public function getCodeAndNameById($id )
    {
        $data = $this->getDataById($id);
        return ['code'=>$data['c_no'],'name'=>$data['c_name']];
    }

}