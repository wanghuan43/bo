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
use PHPExcel_IOFactory;
use think\Config;
use think\Request;

class Orders extends BoController
{
    private $ordersModel;
    protected $title;
    protected $unuseF = ["zc_name", "zc_id", "zc_dname", "zc_did", "zc_mid", "zc_mname", "zc_coname", "zc_coid"];

    function __construct(Request $request)
    {
        parent::__construct($request);
        $this->model = $this->ordersModel = new \app\bo\model\Orders();
    }

    public function index($type = "orders")
    {
        $this->setType($type, []);
        $this->assign("title", $this->title);
        $this->assign("type", $type);
        $this->assign("stype", "orders");
        $formUrl = "/orders/index/type/orders";
        if ($type !== 'orders') {
            $formUrl = '/orders/index/type/' . $type;
        }
        if ($type == 'orders' || $type == 'contract') {
            $this->assign('hideSelected', false);
        } else {
            $this->assign('hideSelected', true);
        }
        $this->assign('formUrl', $formUrl);
        return $this->all();
    }

    public function trashed()
    {
        $type = 'orders';
        $this->setType($type, []);
        $this->assign("title", '回收站.订单列表');
        $this->assign("type", $type);
        $this->assign("stype", "orders");

        $formUrl = "/orders/trashed";

        if ($type == 'orders' || $type == 'contract') {
            $this->assign('hideSelected', false);
        } else {
            $this->assign('hideSelected', true);
        }
        $this->assign('pageType', 'trashed');
        $this->assign('formUrl', $formUrl);
        return $this->all(1);
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
        $isAdmin = true;
        if (!empty($op_id) AND $op == "edit") {
            $isAdmin = $this->current->m_isAdmin == "1" ? true : ($this->current->m_id == $order->o_mid ? true : false);
            $tmp = $tagLinkModel->getList($op_id, "orders");
            foreach ($tmp as $key => $value) {
                $tagIDList[$value->tl_id] = $value->tl_name;
            }
            $tmp = $cModel->getList($op_id, "orders");
            foreach ($tmp as $key => $value) {
                $cIDList[$value->m_id] = $value->m_name;
            }
            if (!$isAdmin) {
                if (!isset($cIDList[$this->current->m_id])) {
                    $this->error("你没有权限查看此订单", "/");
                }
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
        $mimeType = false;
        if ($order['o_attachment']) {
            $mimeType = $this->getAttachmentMimeType($order['o_attachment']);
        }

        $this->assign('aMimeType', $mimeType);
        $this->assign('order', $order);
        $this->assign('typeList', getTypeList());
        $this->assign('taxList', getTaxList());
        $this->assign('lieList', getLieList());
        $this->assign('statusList', getStatusList());
        $this->assign('tagIDList', $tagIDList);
        $this->assign('cIDList', $cIDList);
        $this->assign('baseMonth', json_encode($baseMonth));
        $this->assign('dList', $dModel->all());
        $this->assign('chancesList', $chancesModel->where("cs_isShow", "=", "1")->select());
        $this->assign('memberList', $memberModel->all());
        $this->assign('tagList', $tagList);
        $this->assign('opject', $opm->getOrderProject($op_id));
        $this->assign('oused', $oum->getOrderUesd($op_id));
        $this->assign('op', $op);
        $this->assign('op_id', $op_id);
        $this->assign("isAdmin", $isAdmin);
        $this->assign("current", $this->current);
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

        $file = $this->request->file('o_attachment');
        $res = $this->uploadFile($file);

        if ($res['flag'] === 0) {
            return $res;
        } elseif ($res['flag'] === 1) {
            $post['o_attachment'] = $res['name'];
        } else {
            $post['o_attachment'] = '';
        }

        if ($op == "edit") {
//            $search = [
//                "l_otid" => $op_id,
//                "l_mid" => $this->current->m_id,
//                "l_model" => "orders",
//                "l_panding" => "0",
//            ];
//            $check = Logs::get($search);
//            if ($check) {
//                return array("status" => 0, "message" => "已有还未审核的修改,请等待上一次提交的审核.");
//            }
            $old = $this->ordersModel->getOrderById($op_id);
            $post['o_updatetime'] = time();
            $logModel->saveLogs($post, $old, $op_id, "orders", "edit");
            $_POST['id'] = $logModel->l_id;
            $_POST['val'] = 1;
            $this->savePanding();
            return array("status" => 1, "message" => "保存成功!");
        } else {
            $post['o_no'] = $this->ordersModel->getOrderNO($post['o_pid'], $post['o_type']);
            $post['o_createtime'] = $post['o_updatetime'] = time();
            $zc = [
                "zc_name" => $post['zc_name'],
                "zc_id" => $post['zc_id'],
                "zc_dname" => $post['zc_dname'],
                "zc_did" => $post['zc_did'],
                "zc_mid" => empty($post['zc_mid']) ? "" : $post['zc_mid'],
                "zc_mname" => $post['zc_mname'],
                "zc_coname" => empty($post['zc_coname']) ? "" : $post['zc_coname'],
                "zc_coid" => $post['zc_coid'],
            ];
            foreach ($this->unuseF as $v) {
                unset($post[$v]);
            }
            $result = $this->ordersModel->save($post, $where);
            if ($result AND $post['o_lie'] == '1') {
                $nO = new \app\bo\model\Orders();
                $post['o_pid'] = $zc['zc_id'];
                $post['o_pname'] = $zc['zc_name'];
                $post['o_did'] = $zc['zc_did'];
                $post['o_dname'] = $zc['zc_dname'];
                $post['o_mid'] = $zc['zc_mid'];
                $post['o_mname'] = $zc['zc_mname'];
                $post['o_coid'] = $zc['zc_coid'];
                $post['o_coname'] = $zc['zc_coname'];
                $post['o_foreign'] = $this->ordersModel->o_id;
                $post['o_type'] = $post['o_type'] == '1' ? '2' : '1';
                $post['o_no'] = $nO->getOrderNO($post['o_pid'], $post['o_type']);
                $result = $nO->save($post, $where);
                $this->ordersModel->where("o_id", "=", $this->ordersModel->o_id)->update(["o_foreign" => $nO->o_id]);
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
        $order = $this->ordersModel->find($opID);
        if ($order->o_mid != $this->current->m_id AND $this->current->m_isAdmin == "2") {
            $ret = ["status" => "false", "message" => "只有订单责任人和管理员可以删除订单"];
        } else {
            $oum = new OrderUsed();
            $tmp = $oum->getOrderUesd($opID);
            $ret = ["status" => "false", "message" => "订单删除失败，请先去除订单的关系数据。"];
            if (count($tmp[1]) == 0 AND count($tmp[2]) == 0 AND count($tmp[3]) == 0) {
                $this->ordersModel->deleteOrders($opID);
                $ret = ["status" => "true", "message" => "订单删除成功"];
            }
        }
        return $ret;
    }

    public function myLogList($opId = "")
    {
        $logModel = new Logs();
        $logModel->where("l_model", "=", "orders")->where("l_otid", "=", $opId);
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
        if ($depart->m_code == $this->current->m_code OR $this->current->m_isAdmin == "1") {
            $this->assign("admin", true);
        } else {
            $this->assign("admin", false);
        }
        $log['l_new'] = unserialize($log['l_new']);
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
        if ($log['l_opt'] == "add") {
            $log['l_old'] = $log['l_new'];
        } else {
            $log['l_old'] = unserialize($log['l_old']);
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
        }
        $log['l_old']['o_tax'] = isset($log['l_old']['o_tax']) ? getTaxList($log['l_old']['o_tax']) : "未知";
        $log['l_new']['o_tax'] = isset($log['l_new']['o_tax']) ? getTaxList($log['l_new']['o_tax']) : "未知";
        $log['l_old']['o_deal'] = !empty($log['l_old']['o_deal']) ? $chancesModle->getChanges($log['l_old']['o_deal']) : ["cs_id" => "0", "cs_name" => "无"];
        $log['l_new']['o_deal'] = !empty($log['l_new']['o_deal']) ? $chancesModle->getChanges($log['l_new']['o_deal']) : ["cs_id" => "0", "cs_name" => "无"];
        $log['l_old']['o_status'] = isset($log['l_old']['o_status']) ? getStatusList($log['l_old']['o_status']) : "未知";
        $log['l_new']['o_status'] = isset($log['l_new']['o_status']) ? getStatusList($log['l_new']['o_status']) : $log['l_old']['o_status'];
        $log['l_old']['o_lie'] = isset($log['l_old']['o_lie']) ? getLieList($log['l_old']['o_lie']) : "未知";
        $log['l_new']['o_lie'] = isset($log['l_new']['o_lie']) ? getLieList($log['l_new']['o_lie']) : $log['l_old']['o_lie'];
        $log['l_old']['o_type'] = isset($log['l_old']['o_type']) ? getLieList($log['l_old']['o_type']) : "未知";
        $log['l_new']['o_type'] = isset($log['l_new']['o_type']) ? getLieList($log['l_new']['o_type']) : $log['l_old']['o_type'];
        $this->assign("log", $log);
        return $this->fetch("orders/viewLog");
    }

    public function savePanding()
    {
        $post = Request::instance()->post();
        $id = empty($post['id']) ? $_POST['id'] : $post['id'];
        $vp = empty($post['val']) ? $_POST['val'] : $post['val'];
        $log = Logs::get($id);
        $new = unserialize($log['l_new']);
        $old = unserialize($log['l_old']);
        foreach ($this->unuseF as $val) {
            unset($new[$val]);
            unset($old[$val]);
        }
        $oum = new OrderUsed();
        if ($vp == "1") {
            $log->l_panding = 2;
            $new['o_id'] = $log->l_otid;
            if (!empty($old['o_cid'])) {
                $con = \app\bo\model\Contract::get($old['o_cid']);
                if ($con) {
                    $con->c_noused += $old['o_money'];
                    $con->c_used -= $old['o_money'];
                    $con->save();
                }
            }
            if ($old['o_pid'] != $new['o_pid']) {
                $new['o_no'] = $this->ordersModel->getOrderNO($new['o_pid'], $old['o_type']);
            }
            $this->ordersModel->save($new);
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

    private function setType($type, $params, $trashed = 2)
    {
        unset($params['type']);
        $this->ordersModel->alias("o");
        switch ($type) {
            case "contract":
                $this->title = "订单（签约）";
                $this->ordersModel->where("o.o_status", "=", "6");
                if ($this->current->m_isAdmin == "2") {
                    $this->ordersModel->where("o.o_mid", "=", $this->current->m_id);
                }
                break;
            case "favourite":
                $this->title = "订单管理.我的收藏";
                $this->ordersModel->join("__FAVORITE__ f", "o.o_id = f.f_oid", "LEFT");
//                if(!$this->current->m_isAdmin){
                $this->ordersModel->where("f.f_mid", "=", $this->current->m_id);
//                }
                break;
            case "circulate":
                $this->title = "我的传阅";
                $this->ordersModel->join("__CIRCULATION__ c", "o.o_id = c.ci_otid", "LEFT");
//                if(!$this->current->m_isAdmin){
                $this->ordersModel->where("c.ci_mid", "=", $this->current->m_id)->where('c.ci_type', "=", "orders");
//                }
                break;
            case "panding":
                $this->title = "待审核订单";
                $this->ordersModel->join("__LOGS__ l", "l.l_otid = o.o_id", "LEFT");
                $this->ordersModel->join("__MEMBER__ m", "o.o_mid = m.m_id", "LEFT");
                $this->ordersModel->join("__DEPARTMENT__ d", "m.m_did = d.d_id", "LEFT");
                if ($this->current->m_isAdmin == "2") {
                    $this->ordersModel->where("d.m_code", "=", $this->current->m_code);
                }
                $this->ordersModel->where("l.l_panding", "=", "0");
                break;
            default:
                $this->title = "订单管理.订单列表";
                //$this->ordersModel->where("o.o_status", "<>", "6");
                if ($this->current->m_isAdmin == "2") {
                    $this->ordersModel->join("__CIRCULATION__ c", "o.o_id = c.ci_otid", "LEFT");
                    $this->ordersModel->where(function ($query) {
                        $query->where('c.ci_mid', '=', $this->current->m_id)->where('c.ci_type', '=', 'orders')->whereOr('o.o_mid', '=', $this->current->m_id);
                    });
                }
                break;
        }
        foreach ($params as $key => $value) {
            $this->ordersModel->where("o." . $key, $value['op'], $value['val']);
        }
        $this->model = $this->ordersModel;
    }

    public function export()
    {
        if ($this->request->post('operator') == 'export') {

            ini_set("memory_limit", "1024M");

            $type = $this->request->param('type') ?: 'orders';
            $post = $this->request->post();
            $search = $this->getSearch(false, $post, 'orders');

            $ids = [];
            if (isset($post['ids'])) {
                $ids = $post['ids'];
            } else {
                if ($this->current->m_isAdmin == "2") {
                    $this->model->where("o.o_mid", "=", $this->current->m_id);
                }
                $res = $this->model->getList($search);
                foreach ($res as $i)
                    $ids[] = $i->o_id;
            }

            $ids = implode(',', $ids);

            $view = $type == 'orders' ? 'kj_vw_my_orders' : 'kj_vw_my_contract';

            $order = "ORDER BY `o_updatetime` DESC";

            $sql = 'SELECT * FROM ' . $view . ' WHERE `o_id` IN (' . $ids . ') ' . $order;
            $res = $this->model->query($sql);


            $arr = [];
            foreach ($res as $item) {
                $arr[$item['o_id']][] = $item;
            }

            $res = [];
            foreach ($arr as $key => $val) {

                $arr1 = $val[0];
                $arr1['op_type'] = 'C合同';
                $arr1['op_used'] = $arr1['o_money'];
                $arr1['o_date'] = $arr1['op_date'] = date('Y/m/d', $arr1['o_date']);
                $arr1['o_type'] = getTypeList($arr1['o_type']);
                $arr1['c_bakup'] = '';
                $arr1['flag1'] = $arr1['o_status'] == "6" ? '1' : '';
                $arr1['b_no'] = '';
                $arr1['c_no'] = $arr1['c_name'] = '';

                foreach ($val as $k => $i) {

                    if ($i['op_type'] == 1) {
                        //$arr1['op_used'] += $i['op_used'];
                        $i['op_type'] = 'I发票';
                    } elseif ($i['op_type'] == 2) {
                        $i['op_type'] = 'D交付';
                    } elseif ($i['op_type'] == 3) {
                        $i['op_type'] = 'P付款';
                    } else {
                        $i['op_type'] = '';
                    }

                    $arr1['o_status'] = $i['o_status'] = getStatusList($i['o_status']);

                    $arr1['o_lie'] = $i['o_lie'] = getLieList($i['o_lie']);

                    $i['o_type'] = getTypeList($i['o_type']);

                    $i['o_date'] = date('Y/m/d', $i['o_date']);

                    $i['op_date'] = empty($i['op_date'])? $i['o_date'] : date('Y/m/d', $i['op_date']);

                    $i['c_no'] = '';
                    $i['c_name'] = '';
                    $i['c_bakup'] = '';
                    $i['flag1'] = '';
                    $i['b_no'] = '';
                    if ((isset($i['op_type']) && empty($i['op_type'])) || (isset($i['ou_type']) && empty($i['ou_type']))) {
                        unset($val[$k]);
                    } else {
                        $val[$k] = $i;
                    }
                }

                $arr2 = false;

                if($arr1['o_status'] == 6){

                    $sql = 'SELECT * FROM kj_vw_my_contract WHERE o_id ='.$arr1['o_id'];
                    $arr2 = $this->model->query($sql);

                    if(isset($arr2[0])) {
                        $arr1['c_no'] = $arr2[0]['c_no'];
                        $arr1['c_name'] = $arr2[0]['c_name'];
                        $arr1['c_bakup'] = $arr2['0']['c_bakup'];
                    }

                    foreach ($arr2 as $k=>$v){
                        $v['c_no'] = $v['c_name'] = $v['c_bakup'] = '';
                        if($v['ou_type'] == '1'){
                            $v['op_type'] = 'I发票';
                        }elseif($v['ou_type'] == '2'){
                            $v['op_type'] = 'D交付';
                        }elseif ($v['ou_type'] == '3'){
                            $v['op_type'] = 'P付款';
                        }else{
                            $v['op_type'] = '';
                        }
                        $v['b_no'] = $v['no'];
                        $v['flag1'] = 1;
                        $v['op_used'] = $v['ou_used'];
                        $v['op_date'] = date('Y/m/d',$v['ou_date']);
                        $v['o_type'] = getTypeList($v['o_type']);
                        $v['o_date'] = date('Y/m/d',$v['o_date']);
                        $v['o_lie'] = getLieList($v['o_lie']);
                        $v['op_status'] = $v['o_status'] == "6" ? "是" : "否";
                        $v['o_status'] = getStatusList($v['o_status']);
                        if ((isset($v['op_type']) && empty($v['op_type'])) || (isset($v['ou_type']) && empty($v['ou_type']))) {
                            unset($arr2[$k]);
                        } else {
                            $arr2[$k] = $v;
                        }
                    }

                    /*echo "<pre>";
                    var_dump($arr2);die;*/

                }

                if(empty($arr2)) {
                    $val = array_merge([$arr1], $val);
                }else{
                    $val = array_merge([$arr1],$val,$arr2);
                }
                $arr[$key] = $val;
            }

            $title = '订单跟踪表';

            $res = $arr;
            unset($arr);

            $obj = new \PHPExcel();
            $obj->getProperties()->setCreator("新智云商机管理系统")
                ->setLastModifiedBy("新智云商机管理系统")
                ->setTitle($title)
                ->setSubject($title)
                ->setDescription($title);
            $config = Config::load(APP_PATH . 'bo' . DS . 'excelExport.php', 'boExcel');
            $config = $config['boExcel'][$type];
            $obj->setActiveSheetIndex(0);
            $activeSheet = $obj->getActiveSheet();
            foreach ($config as $k => $i) {
                $activeSheet->setCellValue($k . '1', $i['title']);
            }
            $col = 2;
            foreach ($res as $group) {
                foreach ($group as $row) {
                    foreach ($config as $k => $i) {
                        $val = $row[$i['key']];
                        $activeSheet->setCellValue($k . $col, $val);
                    }
                    $col++;
                }
            }
            $activeSheet->setTitle($title);
            $fileName = $title . '-' . date('ymdHis');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
            $objWriter->save('php://output');

            exit;

        } else {
            return $this->filter("export", 3);
        }
    }

    public function restore()
    {
        $ids = $this->request->post('ids/a');
        if (empty($ids)) {
            $ret = ['flag' => 0, 'msg' => '参数错误'];
        } else {
            $orders = $this->model->whereIn($this->model->getPk(), $ids)->select();
            $fo = [];
            foreach ($orders as $order) {
                if ($order->o_foreign)
                    $fo[] = $order->o_foreign;
            }
            $ids = array_merge($ids, $fo);
            try {
                $res = $this->model->whereIn($this->model->getPk(), $ids)->update([$this->model->getTrashedField() => 2]);
                if ($res) {
                    $ret = ['flag' => 1, 'msg' => '操作成功'];
                } else {
                    $ret = ['flag' => 0, 'msg' => '操作失败'];
                }
            } catch (\Exception $e) {
                $ret = ['flag' => 0, 'msg' => '发生错误'];
            }
        }
        return $ret;
    }

}
