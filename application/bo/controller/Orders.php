<?php
namespace app\bo\controller;

use app\bo\model\Chances;
use app\bo\model\Circulation;
use app\bo\model\Department;
use app\bo\model\TagLink;
use think\Controller;
use think\Loader;
use think\Request;

class Orders extends Controller
{
    var $ordersModel;
    var $limit=20;

    function __construct(Request $request)
    {
        parent::__construct($request);
        $this->ordersModel = Loader::model("orders");
    }

    public function index()
    {
        $filters = Request::instance()->session('filters', array());
        $params = Request::instance()->param();
        $page = isset($params['page']) ? $params['page'] : 1;
        unset($params['page']);
        $params = array_merge($filters, $params);
        foreach($params as $key=>$value){
            $this->ordersModel->where($key, $value);
        }
        $lists = $this->ordersModel->paginate($this->limit, true);
        $this->assign("lists", $lists);
        $this->assign("page", $page);
        $this->assign("empty", '<tr><td colspan="10">暂时没有商机数据.</td></tr>');
        return $this->fetch("index");
    }

    public function operation($op = "add", $op_id = 0)
    {
        $tagModel = new TagLink();
        $cModel = new Circulation();
        $dModel = new Department();
        $chancesModel = new Chances();
        $cNameList = array();
        $cIDList = array();
        $tagNameList = array();
        $tagIDList = array();
        $baseMonth = getMonth();
        $order = $this->ordersModel->get($op_id);
        if ($op == "add") {
            $title = "新建订单";
        } elseif (!empty($op_id) AND $op == "edit") {
            $title = "编辑" . $order->o_subject;
            $tmp = $tagModel->getTagList($op_id, "orders");
            foreach($tmp as $key=>$value){
                $tagNameList[] = $value->tl_name;
                $tagIDList[] = $value->tl_id;
            }
            $tmp = $cModel->getCirculationList($op_id, "orders");
            foreach($tmp as $key=>$value){
                $cNameList[] = $value->m_name;
                $cIDList[] = $value->m_id;
            }
        }
        $this->assign('order', $order);
        $this->assign('title', $title);
        $this->assign('typeList', getTypeList());
        $this->assign('taxList', getTaxList());
        $this->assign('lieList', getLieList());
        $this->assign('statusList', getStatusList());
        $this->assign('tagNameList', implode(" ", $tagNameList));
        $this->assign('tagIDList', implode(",", $tagIDList));
        $this->assign('cNameList', implode(" ", $cNameList));
        $this->assign('cIDList', implode(",", $cIDList));
        $this->assign('baseMonth', json_encode($baseMonth));
        $this->assign('dList', $dModel->all());
        $this->assign('chancesList', $chancesModel->all());
        return $this->fetch("operation");
    }

    public function doOperation($op = "add", $op_id = 0){

    }
}