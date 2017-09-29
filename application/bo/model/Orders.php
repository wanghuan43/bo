<?php

namespace app\bo\model;

use app\bo\libs\BoModel;

class   Orders extends BoModel
{
    protected $pk = 'o_id';

    protected $searchable = [
        'o_cno' => [
            'name' => '合同编号',
            'type' => 'text',
            'operators' => [
                "like" => "包含",
                "=" => "等于",
            ]
        ],
        'o_money' => [
            'name' => '总金额',
            'type' => 'price',
            'operators' => [
                "between" => "介于",
                "=" => "等于",
                "<=" => "小于等于",
                ">=" => "大于等于",
            ]
        ],
        "o_date" => array(
            "name" => "订单日期",
            "type" => "date",
            "operators" => array(
                "between" => "介于",
                "=" => "等于",
                ">" => "大于",
                "<" => "小于",
            ),
        ),
        'o_coname' => [
            'name' => '公司名称',
            'type' => 'text',
            'operators' => [
                "like" => "包含",
                "=" => "等于",
            ]
        ],
    ];

    public function getList($search, $limit)
    {
        $member = $this->getCurrent();
        $this->alias('o');
        if (!$member->m_isAdmin) {
            $this->join('__CIRCULATION__ c', "o.o_id = c.ci_otid AND c.ci_type = 'orders'");
            $this->where("c.ci_mid", "=", $member->m_id);
        }
        $this->field("o.*");
        foreach ($search as $key => $value) {
            $this->where("o." . $value['field'], $value['opt'], $value['val']);
        }
        $list = $this->paginate($limit, true);
        return $list;
    }

    public function getOrderNO($p_id, $otype)
    {
        $projectModel = new Project();
        $project = $projectModel->get($p_id);
        $str = "C";
        if ($otype == '1') {
            $str = "x";
        }
        $c = $this->where("o_pid", "=", $p_id)->where("o_type", "=", $otype)->count();
        return $project->p_no . "-" . $str . str_pad(($c + 1), 5, "0", STR_PAD_LEFT);
    }

    public function getOrderById($id)
    {
        $orderProjectModel = new OrderProject();
        $orderUsedModel = new OrderUsed();
        $tagLibModel = new Taglink();
        $circulationModel = new Circulation();
        $order = $this->find($id)->toArray();
        $order['project'] = $orderProjectModel->getOrderProject($id);
        $order['used'] = $orderUsedModel->getOrderUesd($id);
        $order['tagList'] = $tagLibModel->getList($id, "orders", true);
        $order['cList'] = $circulationModel->getList($id, "orders", true);
        return $order;
    }

    public function getOrderDeparent($id)
    {
        $list = $this->field("m.m_did")->alias("o")->join("__MEMBER__ m", "o.o_mid = m.m_id", "left")
            ->where("o.o_id", "=", $id)->find();
        return Department::get($list->m_did);
    }

    /**
     * @param array $data
     * @param array $where
     * @param null $sequence
     * @return false|int
     */
    public function save($data = [], $where = [], $sequence = null)
    {
        $tlm = new Taglink();
        $clm = new Circulation();
        $opm = new OrderProject();
        $oum = new OrderUsed();
        $con = new Contract();
        $tagList = !empty($data['tagList']) ? $data['tagList'] : array();
        $cList = !empty($data['cList']) ? $data['cList'] : array();
        $pj = !empty($data['project']) ? $data['project'] : array();
        $used = !empty($data['used']) ? $data['used'] : array();
        unset($data['tagList']);
        unset($data['cList']);
        unset($data['project']);
        unset($data['used']);
        $result = parent::save($data, $where, $sequence);
        if (!empty($data['o_cid'])) {
            $c = $con->where("c_id", "=", $data['o_cid'])->find();
            $c->c_used = $c->c_used + $data['o_money'];
            $c->c_noused = $c->c_money - $c->c_used;
            $c->save();
        }
        $tlm->setTagLink($this->o_id, $tagList, "orders");
        $clm->setCirculation($this->o_id, $cList, "orders");
        $opm->setOrderProject($this->o_id, $data['o_date'], $pj);
        $oum->setOrderUsed($this->o_id, $used);
        return $result;
    }

    public function getOrdersByContractId($cid)
    {
        return $this->where('o_cid', '=', $cid)->select();
    }
}