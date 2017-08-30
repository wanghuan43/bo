<?php
namespace app\bo\controller;

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
        $tagNameList = array();
        $tagIDList = array();
        $order = $this->ordersModel->get($op_id);
        if ($op == "add") {
            $title = "新建商机";
        } elseif ($op_id != "" AND $op == "edit") {
            $title = "编辑" . $order->subject;
            $tagNameList = $tagModel->getTagList($op_id, "orders");
            foreach($tagNameList as $key=>$value){
                $tagNameList[] = $value->tl_name;
                $tagIDList[] = $value->tl_id;
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
        return $this->fetch("operation");
    }
}