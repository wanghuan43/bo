<?php

namespace app\bo\model;

use app\bo\libs\BoModel;

class Invoice extends BoModel
{
    protected $pk = "i_id";

    protected $searchable = array(
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
            ->alias('i');
        if (!$member->m_isAdmin) {
            $searchModel->join('__CIRCULATION__ c', "i.i_id = c.ci_otid AND c.ci_type = 'invoice'");
            $searchModel->where("c.ci_mid", "=", $member->m_id);
        }
        $searchModel->field("i.*");
        foreach ($search as $key => $value) {
            $searchModel->where("i." . $value['field'], $value['opt'], $value['val']);
        }
        $list = $searchModel->paginate($limit, true);
        return $list;
    }

    public function checkUsed($id, $money, $op = "-")
    {
        $tmp = $this->find($id)->toArray();
        $return = true;
        if ($op == "-") {
            if ($tmp['i_noused'] - $money < 0) {
                $return = false;
            } else {
                $this->save(['i_noused' => ($tmp['i_noused'] - $money), 'i_used' => ($tmp['i_used'] + $money)], ["i_id" => $id]);
            }
        }else{
            $this->save(['i_noused' => ($tmp['i_noused'] + $money), 'i_used' => ($tmp['i_used'] - $money)], ["i_id" => $id]);
        }
        return $return;
    }
}