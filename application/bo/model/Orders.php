<?php
namespace app\bo\model;

use app\bo\libs\BoModel;

class Orders extends BoModel
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
        $order['tagList'] = $tagLibModel->getList($id);
        $order['cList'] = $circulationModel->getList($id);
        return $order;
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
        $tlm = new Taglink();
        $clm = new Circulation();
        $opm = new OrderProject();
        $result = parent::save($data, $where, $sequence);
        $tlm->setTagLink($this->o_id, $tagList, "orders");
        $clm->setCirculation($this->o_id, $cList, "orders");
        $opm->setOrderProject($this->o_id, $data['o_date'], $pj);
        return $result;
    }
}