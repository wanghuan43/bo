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
        $post = Request::instance()->post();
        $post['o_date'] = strtotime($post['o_date']);
        $where = [];
        $message = "保存成功";
        if ($op == "edit") {
            $logModel = new Logs();
            $old = $this->ordersModel->getOrderById($op_id);
            $result = $logModel->saveLogs($post, $old, $op_id, "orders");
        } else {
            unset($post['tagList']);
            unset($post['cList']);
            unset($post['project']);
            $post['o_no'] = $this->ordersModel->getOrderNO($post['o_pid']);
            $result = $this->ordersModel->save($post, $where);
        }
        if (!$result) {
            $message = "保存失败";
        }
        return array("status" => $result, "message" => $message);
    }

    public function pandingLog($opId = "")
    {
        $loglist = Logs::all(["l_model" => "orders", "l_mid" => $this->current->m_id, "l_otid" => $opId]);
        echo 1;
        var_dump($loglist);
    }

    private function setType($type, $params)
    {
        unset($params['type']);
        switch ($type) {
            case "contract":
                $this->ordersModel->where("o_status", "=", "6");
                $this->ordersModel->where("o_mid", "=", $this->current->m_id);
                $this->title = "我的合同";
                break;
            case "favourite":
                $this->title = "我的收藏";
                $this->ordersModel->alias("o");
                $this->ordersModel->join("__FAVORITE__ f", "o.o_id = f.f_oid", "LEFT");
                $this->ordersModel->where("f.f_mid", "=", $this->current->m_id);
                break;
            case "circulate":
                $this->title = "我的传阅";
                $this->ordersModel->alias("o");
                $this->ordersModel->join("__CIRCULATION__ c", "o.o_id = c.ci_otid AND c.ci_type = 'orders'", "LEFT");
                $this->ordersModel->where("c.ci_mid", "=", $this->current->m_id);
                break;
            default:
                $this->title = "我的订单";
                foreach ($params as $key => $value) {
                    $this->ordersModel->where($key, $value);
                }
                $this->ordersModel->where("o_status", "<>", "6");
                $this->ordersModel->where("o_mid", "=", $this->current->m_id);
                break;
        }
    }
}