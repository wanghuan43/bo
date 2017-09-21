<?php

namespace app\bo\model;

use app\bo\libs\BoModel;

class   Orders extends BoModel
{
    protected $pk = 'o_id';

    public function getOrderNO($p_id)
    {
        $projectModel = new Project();
        $project = $projectModel->get($p_id);
        $c = $this->where("o_pid", "=", $p_id)->count();
        return $project->p_no . "-" . str_pad(($c + 1), 6, "0", STR_PAD_LEFT);
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
        $tagList = !empty($data['tagList']) ? $data['tagList'] : array();
        $cList = !empty($data['cList']) ? $data['cList'] : array();
        $pj = !empty($data['project']) ? $data['project'] : array();
        $used = !empty($data['used']) ? $data['used'] : array();
        unset($data['tagList']);
        unset($data['cList']);
        unset($data['project']);
        unset($data['used']);
        $tlm = new Taglink();
        $clm = new Circulation();
        $opm = new OrderProject();
        $oum = new OrderUsed();
        $result = parent::save($data, $where, $sequence);
        $tlm->setTagLink($this->o_id, $tagList, "orders");
        $clm->setCirculation($this->o_id, $cList, "orders");
        $opm->setOrderProject($this->o_id, $data['o_date'], $pj);
        $oum->setOrderUsed($this->o_id, $used);
        return $result;
    }
}