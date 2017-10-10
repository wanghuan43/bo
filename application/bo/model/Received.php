<?php

namespace app\bo\model;

use app\bo\libs\BoModel;

class Received extends BoModel
{

    protected $pk = "r_id";

    protected $searchable = array(
        "r_date" => array(
            "name" => "付款日期",
            "type" => "date",
            "operators" => array(
                "between" => "介于",
                "=" => "等于",
                ">" => "大于",
                "<" => "小于",
            ),
        ),
        "r_money" => array(
            "name" => "金额",
            "type" => "price",
            "operators" => array(
                "between" => "介于",
                "=" => "等于",
                ">" => "大于",
                "<" => "小于",
            ),
        ),
    );

    public function getList($search = array(), $limit = 20)
    {
        $member = $this->getCurrent();
        $this->alias('r');
        if (!$member->m_isAdmin) {
            $this->join('__CIRCULATION__ c', "r.r_id = c.ci_otid AND c.ci_type = 'received'");
            $this->where("c.ci_mid", "=", $member->m_id);
        }
        $this->field("r.*");
        foreach ($search as $key => $value) {
            $this->where("r." . $value['field'], $value['opt'], $value['val']);
        }
        $list = $this->paginate($limit);
        return $list;
    }

    public function checkUsed($id, $money, $op = '-')
    {
        $tmp = $this->find($id)->toArray();
        $return = true;
        if ($op == "-") {
            if ($tmp['r_noused'] - $money < 0) {
                $return = false;
            } else {
                $this->save(['r_noused' => ($tmp['r_noused'] - $money), 'r_used' => ($tmp['r_used'] + $money)], ["r_id" => $id]);
            }
        } else {
            $this->save(['r_noused' => ($tmp['r_noused'] + $money), 'r_used' => ($tmp['r_used'] - $money)], ["r_id" => $id]);
        }
        return $return;
    }

    public function getCodeAndNameById($id)
    {
        $data = $this->getDataById($id);
        return ['code'=>$data['r_no'],'name'=>'付款单'.$data['r_no']];
    }

}