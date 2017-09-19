<?php

namespace app\bo\model;

use app\bo\libs\BoModel;

class Received extends BoModel
{

    protected $pk = "i_id";

    protected $searchable = array(
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
            ->alias('r');
        if (!$member->m_isAdmin) {
            $searchModel->join('__CIRCULATION__ c', "r.r_id = c.ci_otid AND c.ci_type = 'received'");
            $searchModel->where("c.ci_mid", "=", $member->m_id);
        }
        $searchModel->field("r.*");
        foreach ($search as $key => $value) {
            $searchModel->where("r." . $value['field'], $value['opt'], $value['val']);
        }
        $list = $searchModel->paginate($limit, true);
        return $list;
    }

    public function checkUsed($id, $money)
    {
        $tmp = $this->find($id)->toArray();
        $return = true;
        if ($tmp['r_noused'] - $money < 0) {
            $return = false;
        } else {
            $this->save(['r_noused' => ($tmp['r_noused'] - $money), 'r_used' => ($tmp['r_used'] + $money)], $id);
        }
        return $return;
    }
}