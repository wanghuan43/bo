<?php

namespace app\bo\controller;

use app\bo\model\Chances;
use app\bo\model\Circulation;
use app\bo\model\Department;
use app\bo\model\Logs;
use app\bo\model\Member;
use app\bo\model\OrderProject;
use app\bo\model\OrderUsed;
use app\bo\model\Taglib;
use app\bo\model\Taglink;
use app\bo\libs\BoController;
use think\Request;
use think\Session;

class Orders extends BoController
{
    private $ordersModel;
    protected $title;

    function __construct(Request $request)
    {
        parent::__construct($request);
        $this->model = $this->ordersModel = new \app\bo\model\Orders();
    }

    public function index($type = "orders")
    {
        $filters = Session::get('filtersOrders');
        $params = Request::instance()->param();
        $filters = empty($filters) ? array() : $filters;
        unset($params['page']);
        $params = array_merge($filters, $params);
        $this->setType($type, $params);
        session("filtersOrders", $params);
        $lists = $this->ordersModel->paginate($this->limit);
        $this->assign("lists", $lists);
        $this->assign("title", $this->title);
        $this->assign("type", $type);
        $this->assign("stype", "orders");
        $this->assign("empty", '<tr><td colspan="11">无数据.</td></tr>');
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
        $oum = new OrderUsed();
        $cIDList = array();
        $tagIDList = array();
        $memberModel = new Member();
        $baseMonth = getMonth();
        $fmodel = new \app\bo\model\Favorite();
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
        $isAdmin = $this->current->m_isAdmin ? true : ($this->current->m_id == $order->o_mid ? true : false);
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
        $this->assign('oused', $oum->getOrderUesd($op_id));
        $this->assign('op', $op);
        $this->assign('op_id', $op_id);
        $this->assign("isAdmin", $isAdmin);
        $this->assign("isFavorite", $fmodel->where("f_oid", "=", $op_id)->where("f_mid", "=", $this->current->m_id)->count());
        return $this->fetch("operation");
    }

    public function doOperation($op = "add", $op_id = "")
    {
        $logModel = new Logs();
        $post = Request::instance()->post();
        $post['o_date'] = strtotime($post['o_date']);
        $where = [];
        $message = "保存成功";
        $post['o_mid'] = $this->current->m_id;
        $post['o_mname'] = $this->current->m_name;
        if ($op == "edit") {
            $search = [
                "l_otid" => $op_id,
                "l_mid" => $this->current->m_id,
                "l_model" => "orders",
                "l_panding" => "0",
            ];
            $check = Logs::get($search);
            if ($check) {
                return array("status" => 0, "message" => "已有还未审核的修改,请等待上一次提交的审核.");
            }
            $old = $this->ordersModel->getOrderById($op_id);
            $logModel->saveLogs($post, $old, $op_id, "orders", "edit");
            return array("status" => 1, "message" => "以保存此次提交,请等待审核");
        } else {
            $post['o_no'] = $this->ordersModel->getOrderNO($post['o_pid'], $post['o_type']);
            $result = $this->ordersModel->save($post, $where);
            if($result AND $post['o_lie'] == '2'){
                $nO = new \app\bo\model\Orders();
                $oldI = $post['o_did'];$oldN = $post['o_dname'];$newI = $post['o_coid'];$newN = $post['o_coname'];
                $post['o_did'] = $newI;
                $post['o_dname'] = $newN;
                $post['o_coid'] = $oldI;
                $post['o_coname'] = $oldN;
                $post['o_type'] = $post['o_type'] == '1' ? '2' : '1';
                $post['o_no'] = $nO->getOrderNO($post['o_pid'], $post['o_type']);
                $result = $nO->save($post, $where);
            }
            $logModel->saveLogs($post, array(), $this->ordersModel->o_id, "orders", "add");
        }
        if (!$result) {
            $message = "保存失败";
        }
        return array("status" => $result, "message" => $message);
    }

    public function delete($opID)
    {
        $orders = $this->ordersModel->where("o_id", "=", $opID)->find();
        $oum = new OrderUsed();
        $tmp = $oum->getOrderUesd($opID);
        $ret = ["status" => "false", "message" => "订单删除失败，请先去除订单的关系数据。"];
        if (count($tmp[1]) == 0 AND count($tmp[2]) == 0 AND count($tmp[3]) == 0 AND $orders->o_status != 6) {
            $orders->delete();
            $ret = ["status" => "true", "message" => "订单删除成功"];
        }
        return $ret;
    }

    public function myLogList($opId = "")
    {
        $logModel = new Logs();
        $logModel->where("l_model", "=", "orders")->where("l_mid", "=", $this->current->m_id)->where("l_otid", "=", $opId);
        $loglist = $logModel->paginate($this->limit);
        $this->assign("loglist", $loglist);
        $this->assign("title", "日志");
        $this->assign("empty", '<tr><td colspan="6">无数据.</td></tr>');
        return $this->fetch("orders/loglist");
    }

    public function viewLog($opId = "")
    {
        $chancesModle = new Chances();
        $log = Logs::get($opId)->toArray();
        $depart = $this->ordersModel->getOrderDeparent($log['l_otid']);
        if ($depart->m_code == $this->current->m_code OR $this->current->m_isAdmin) {
            $this->assign("admin", true);
        } else {
            $this->assign("admin", false);
        }
        $log['l_new'] = unserialize($log['l_new']);
        $log['l_old'] = unserialize($log['l_old']);
        if (isset($log['l_new']['tagList']) AND is_array($log['l_new']['tagList'])) {
            $t = implode(",", $log['l_new']['tagList']);
            $m = new Taglib();
            $tmp = $m->where("tl_id", "in", $t)->select();
            foreach ($tmp as $key => $value) {
                $tmp[$key] = $value->tl_name;
            }
            $log['l_new']['tagList'] = $tmp;
        } else {
            $log['l_new']['tagList'] = [];
        }
        if (isset($log['l_new']['cList']) AND is_array($log['l_new']['cList'])) {
            $t = implode(",", $log['l_new']['cList']);
            $m = new Member();
            $tmp = $m->where("m_id", "in", $t)->select();
            foreach ($tmp as $key => $value) {
                $tmp[$key] = $value->m_department . ' - ' . $value->m_name;
            }
            $log['l_new']['cList'] = $tmp;
        } else {
            $log['l_new']['cList'] = [];
        }
        if (isset($log['l_old']['tagList']) AND is_array($log['l_old']['tagList'])) {
            foreach ($log['l_old']['tagList'] as $key => $value) {
                $log['l_old']['tagList'][$key] = $value['tl_name'];
            }
        } else {
            $log['l_old']['tagList'] = [];
        }
        if (isset($log['l_old']['cList']) AND is_array($log['l_old']['cList'])) {
            foreach ($log['l_old']['cList'] as $key => $value) {
                $log['l_old']['cList'][$key] = $value['m_department'] . ' - ' . $value['m_name'];
            }
        } else {
            $log['l_old']['cList'] = [];
        }
        $log['l_old']['o_tax'] = getTaxList($log['l_old']['o_tax']);
        $log['l_new']['o_tax'] = getTaxList($log['l_new']['o_tax']);
        $log['l_old']['o_deal'] = $chancesModle->getChanges($log['l_old']['o_deal']);
        $log['l_new']['o_deal'] = $chancesModle->getChanges($log['l_new']['o_deal']);
        $log['l_old']['o_status'] = getStatusList($log['l_old']['o_status']);
        $log['l_new']['o_status'] = getStatusList($log['l_new']['o_status']);
        $log['l_old']['o_lie'] = getLieList($log['l_old']['o_lie']);
        $log['l_new']['o_lie'] = getLieList($log['l_new']['o_lie']);
        $log['l_old']['o_type'] = getLieList($log['l_old']['o_type']);
        $log['l_new']['o_type'] = getLieList($log['l_new']['o_type']);
        $this->assign("log", $log);
        return $this->fetch("orders/viewLog");
    }

    public function savePanding()
    {
        $post = Request::instance()->post();
        $log = Logs::get($post['id']);
        $data = unserialize($log['l_new']);
        $oum = new OrderUsed();
        if ($post['val'] == "1") {
            $log->l_panding = 2;
            $data['o_id'] = $log->l_otid;
            $oum->resetOrderUsed($log->l_otid);
            $this->ordersModel->save($data, ["o_id" => $log->l_otid]);
            $message = "审核通过";
        } else {
            $log->l_panding = 1;
            $message = "审核不通过";
        }
        $log->save();
        return ["status" => 1, "message" => $message];
    }

    public function searchOrders($type = "orders")
    {
        $this->setType($type, array());
        $this->assign("controller", "orders");
        $this->assign("type", $type);
        $this->other = "main-pannel";
        return $this->search($this->ordersModel, "common/poplayer", 11);
    }

    private function setType($type, $params)
    {
        unset($params['type']);
        $this->ordersModel->alias("o");
        switch ($type) {
            case "contract":
                $this->title = "我的合同";
                $this->ordersModel->where("o.o_status", "=", "6");
                if(!$this->current->m_isAdmin){
                    $this->ordersModel->where("o.o_mid", "=", $this->current->m_id);
                }
                break;
            case "favourite":
                $this->title = "我的收藏";
                $this->ordersModel->join("__FAVORITE__ f", "o.o_id = f.f_oid", "LEFT");
//                if(!$this->current->m_isAdmin){
                    $this->ordersModel->where("f.f_mid", "=", $this->current->m_id);
//                }
                break;
            case "circulate":
                $this->title = "我的传阅";
                $this->ordersModel->join("__CIRCULATION__ c", "o.o_id = c.ci_otid AND c.ci_type = 'orders'", "LEFT");
//                if(!$this->current->m_isAdmin){
                    $this->ordersModel->where("c.ci_mid", "=", $this->current->m_id);
//                }
                break;
            default:
                $this->title = "我的订单";
                $this->ordersModel->where("o.o_status", "<>", "6");
                if(!$this->current->m_isAdmin){
                    $this->ordersModel->where("o.o_mid", "=", $this->current->m_id);
                }
                break;
        }
        foreach ($params as $key => $value) {
            $this->ordersModel->where("o." . $key, $value['op'], $value['val']);
        }
    }
}