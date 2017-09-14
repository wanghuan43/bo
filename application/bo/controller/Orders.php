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
use think\Request;
use think\Session;

class Orders extends BoController
{
    private $ordersModel;
    protected $title;

    function __construct(Request $request)
    {
        parent::__construct($request);
        $this->ordersModel = new \app\bo\model\Orders();
    }

    public function index($type = "")
    {
        $filters = Session::get('filtersOrders');
        $params = Request::instance()->param();
        $filters = empty($filters) ? array() : $filters;
        unset($params['page']);
        $params = array_merge($filters, $params);
        $this->setType($type, $params);
        session("filtersOrders", $params);
        $lists = $this->ordersModel->paginate($this->limit, true);
        $this->assign("lists", $lists);
        $this->assign("title", $this->title);
        $this->assign("empty", '<tr><td colspan="10">无数据.</td></tr>');
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
        $logModel = new Logs();
        $post = Request::instance()->post();
        $post['o_date'] = strtotime($post['o_date']);
        $where = [];
        $message = "保存成功";
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
            unset($post['tagList']);
            unset($post['cList']);
            unset($post['project']);
            $logModel->saveLogs($post, array(), "", "orders", "add");
            $post['o_no'] = $this->ordersModel->getOrderNO($post['o_pid']);
            $result = $this->ordersModel->save($post, $where);
        }
        if (!$result) {
            $message = "保存失败";
        }
        return array("status" => $result, "message" => $message);
    }

    public function myLogList($opId = "")
    {
        $logModel = new Logs();
        $logModel->where("l_model", "=", "orders")->where("l_mid", "=", $this->current->m_id)->where("l_otid", "=", $opId);
        $loglist = $logModel->paginate($this->limit, true);
        $this->assign("loglist", $loglist);
        $this->assign("title", "日志");
        $this->assign("empty", '<tr><td colspan="6">无数据.</td></tr>');
        return $this->fetch("orders/loglist");
    }

    public function viewLog($opId = "")
    {
        $log = Logs::get($opId)->toArray();
        $log['l_new'] = unserialize($log['l_new']);
        $log['l_old'] = unserialize($log['l_old']);
        if (isset($log['l_new']['tagList']) AND is_array($log['l_new']['tagList'])) {
            $t = implode(",", $log['l_new']['tagList']);
            $m = new Taglib();
            $log['l_new']['tagList'] = $m->where("tl_id", "in", "(".$t.")")->select();
        }
        if (isset($log['l_new']['cList']) AND is_array($log['l_new']['cList'])) {
            $t = implode(",", $log['l_new']['cList']);
            $m = new Member();
            $log['l_new']['cList'] = $m->where("m_id", "in", "(".$t.")")->select();
        }
        echo "<pre>";
        print_r($log);
        exit;
        $this->assign("log", $log);
        return $this->fetch("orders/viewLog");
    }

    private function setType($type, $params)
    {
        unset($params['type']);
        $this->ordersModel->alias("o");
        switch ($type) {
            case "contract":
                $this->title = "我的合同";
                $this->ordersModel->where("o.o_status", "=", "6");
                $this->ordersModel->where("o.o_mid", "=", $this->current->m_id);
                break;
            case "favourite":
                $this->title = "我的收藏";
                $this->ordersModel->join("__FAVORITE__ f", "o.o_id = f.f_oid", "LEFT");
                $this->ordersModel->where("f.f_mid", "=", $this->current->m_id);
                break;
            case "circulate":
                $this->title = "我的传阅";
                $this->ordersModel->join("__CIRCULATION__ c", "o.o_id = c.ci_otid AND c.ci_type = 'orders'", "LEFT");
                $this->ordersModel->where("c.ci_mid", "=", $this->current->m_id);
                break;
            default:
                $this->title = "我的订单";
                $this->ordersModel->where("o.o_status", "<>", "6");
                $this->ordersModel->where("o.o_mid", "=", $this->current->m_id);
                break;
        }
        foreach ($params as $key => $value) {
            $this->ordersModel->where("o." . $key, $value['op'], $value['val']);
        }
    }
}