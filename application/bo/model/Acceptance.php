<?php

namespace app\bo\model;

use app\bo\libs\BoModel;

class Acceptance extends BoModel
{

    protected $pk = "a_id";

    protected $searchable = array(
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
            ->alias('a');
        if (!$member->m_isAdmin) {
            $searchModel->join('__CIRCULATION__ c', "a.a_id = c.ci_otid AND c.ci_type = 'acceptance'");
            $searchModel->where("c.ci_mid", "=", $member->m_id);
        }
        $searchModel->field("a.*");
        foreach ($search as $key => $value) {
            $searchModel->where('a.' . $value['field'], $value['opt'], $value['val']);
        }
        $list = $searchModel->paginate($limit, true);
        return $list;
    }

    public function checkUsed($id, $money)
    {
        $tmp = $this->find($id)->toArray();
        $return = true;
        if ($tmp['a_noused'] - $money < 0) {
            $return = false;
        } else {
            $this->save(['a_noused' => ($tmp['a_noused'] - $money), 'a_used' => ($tmp['a_used'] + $money)], $id);
        }
        return $return;
    }
}