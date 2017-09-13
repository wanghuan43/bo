<?php
namespace app\bo\controller;

use app\bo\model\Chances;
use app\bo\model\Circulation;
use app\bo\model\Department;
use app\bo\model\Logs;
use app\bo\model\Member;
use app\bo\model\OrderProject;
use app\bo\model\Taglib;
use app\bo\model\Taglink;
use app\bo\libs\BoController;
use think\Loader;
use think\Request;

class Orders extends BoController
{
    private $ordersModel;

    function __construct(Request $request)
    {
        parent::__construct($request);
        $this->ordersModel = Loader::model("orders");
    }

    public function index()
    {
        $filters = Request::instance()->session('filtersOrders', array());
        $params = Request::instance()->param();
        unset($params['page']);
        $params = array_merge($filters, $params);
        foreach ($params as $key => $value) {
            $this->ordersModel->where($key, $value);
        }
        session("filtersOrders", $params);
        $lists = $this->ordersModel->paginate($this->limit, true);
        $this->assign("lists", $lists);
        $this->assign("empty", '<tr><td colspan="10">暂时没有商机数据.</td></tr>');
        return $this->fetch("index");
    }

    public function operation($op = "add", $op_id = 0)
    {
        $tagLinkModel = new Taglink();
        $tagListModel = new Taglib();
        $cModel = new Circulation();
        $dModel = new Department();
        $chancesModel = new Chances();
        $opm = new OrderProject();
        $cIDList = array();
        $tagIDList = array();
        $memberModel = new Member();
        $baseMonth = getMonth();
        $order = $this->ordersModel->get($op_id);
        if (!empty($op_id) AND $op == "edit") {
            $tmp = $tagLinkModel->getList($op_id, "orders");
            foreach ($tmp as $key => $value) {
                $tagIDList[$value->tl_id] = $value->tl_id;
            }
            $tmp = $cModel->getList($op_id, "orders");
            foreach ($tmp as $key => $value) {
                $cIDList[$value->m_id] = $value->m_id;
            }
        }
        $tmp = $tagListModel->all(function ($query) {
            $query->order('tl_name', 'asc');
        });
        $tagList = array();
        foreach ($tmp as $value) {
            $f = getFirstCharter($value['tl_name']);
            $tagList[$f][] = $value;
        }
        $this->assign('order', $order);
        $this->assign('typeList', getTypeList());
        $this->assign('taxList', getTaxList());
        $this->assign('lieList', getLieList());
        $this->assign('statusList', getStatusList());
        $this->assign('tagIDList', $tagIDList);
        $this->assign('cIDList', $cIDList);
        $this->assign('baseMonth', json_encode($baseMonth));
        $this->assign('dList', $dModel->all());
        $this->assign('chancesList', $chancesModel->all());
        $this->assign('memberList', $memberModel->all());
        $this->assign('tagList', $tagList);
        $this->assign('opject', $opm->getOrderProject($op_id));
        $this->assign('op', $op);
        $this->assign('op_id', $op_id);
        return $this->fetch("operation");
    }

    public function doOperation($op = "add", $op_id = "")
    {
        $post = Request::instance()->post();
        $tlm = new Taglink();
        $clm = new Circulation();
        $opm = new OrderProject();
        $tagList = !empty($post['tagList']) ? $post['tagList'] : array();
        $cList = !empty($post['cList']) ? $post['cList'] : array();
        $pj = !empty($post['project']) ? $post['project'] : array();
        unset($post['tagList']);
        unset($post['cList']);
        unset($post['project']);
        $post['o_date'] = strtotime($post['o_date']);
        $where = [];
        if ($op == "edit") {
            $logModel = new Logs();
            $result = $logModel->saveLogs($post, $op_id, "orders");
        } else {
            $post['o_no'] = $this->ordersModel->getOrderNO($post['o_pid']);
            $result = $this->ordersModel->save($post, $where);
            $o_id = $this->ordersModel->o_id;
            $tlm->setTagLink($o_id, $tagList, "orders");
            $clm->setCirculation($o_id, $cList, "orders");
            $opm->setOrderProject($o_id, $post['o_date'], $pj);
        }
        return array("status" => $result, "message" => "");
    }

    public function pandingLog()
    {

    }
}