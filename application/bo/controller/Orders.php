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
        if (!empty($op_id)) {
            $isAdmin = $this->current->m_isAdmin ? true : ($this->current->m_id == $order->o_mid ? true : false);
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
        $this->assign('chancesList', $chancesModel->where("cs_isShow", "=","1")->select());
        $this->assign('memberList', $memberModel->all());
        $this->assign('tagList', $tagList);
        $this->assign('opject', $opm->getOrderProject($op_id));
        $this->assign('oused', $oum->getOrderUesd($op_id));
        $this->assign('op', $op);
        $this->assign('op_id', $op_id);
        $this->assign("isAdmin", $isAdmin);
        $this->assign("md", $this->current->m_did);
        $this->assign("mn", $this->current->m_name);
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
            $post['o_createtime'] = $post['o_updatetime'] = time();
            $result = $this->ordersModel->save($post, $where);
            if ($result AND $post['o_lie'] == '2') {
                $nO = new \app\bo\model\Orders();
                $zc = [
                    "zc_name" => $post['zc_name'],
                    "zc_id" => $post['zc_id'],
                    "zc_dname" => $post['zc_dname'],
                    "zc_did" => $post['zc_did'],
                    "zc_mid" => $post['zc_mid'],
                    "zc_mname" => $post['zc_mname'],
                ];
                unset($post['zc_name']);
                unset($post['zc_id']);
                unset($post['zc_dname']);
                unset($post['zc_did']);
                unset($post['zc_mid']);
                unset($post['zc_mname']);
                if ($post['o_type'] == "1") {
                    $post['o_pid'] = $zc['zc_id'];
                    $post['o_pname'] = $zc['zc_name'];
                    $post['o_did'] = $zc['zc_did'];
                    $post['o_dname'] = $zc['zc_dname'];
                    $post['o_mid'] = $zc['zc_mid'];
                    $post['o_mname'] = $zc['zc_mname'];
                    $post['o_foreign'] = $this->ordersModel->o_id;
                }
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
        if ($order->o_mid != $this->current->m_id AND empty($this->current->m_isAdmin)) {
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
        if ($depart->m_code == $this->current->m_code OR $this->current->m_isAdmin) {
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
        $log = Logs::get($post['id']);
        $new = unserialize($log['l_new']);
        $old = unserialize($log['l_old']);
        $oum = new OrderUsed();
        if ($post['val'] == "1") {
            $log->l_panding = 2;
            $new['o_id'] = $log->l_otid;
            $oum->resetOrderUsed($log->l_otid);
            if ($new['o_cid'] != $old['o_cid']) {
                $con = \app\bo\model\Contract::get($old['o_cid']);
                $con->c_noused += $old['o_money'];
                $con->c_used -= $old['o_money'];
                $con->save();
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

    private function setType($type, $params)
    {
        unset($params['type']);
        $this->ordersModel->alias("o");
        switch ($type) {
            case "contract":
                $this->title = "合同订单";
                $this->ordersModel->where("o.o_status", "=", "6");
                if (!$this->current->m_isAdmin) {
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
            case "panding":
                $this->title = "待审核订单";
                $this->ordersModel->join("__LOGS__ l", "l.l_otid = o.o_id", "LEFT");
                $this->ordersModel->join("__MEMBER__ m", "o.o_mid = m.m_id", "LEFT");
                $this->ordersModel->join("__DEPARTMENT__ d", "m.m_did = d.d_id", "LEFT");
                if (!$this->current->m_isAdmin) {
                    $this->ordersModel->where("d.m_code", "=", $this->current->m_code);
                }
                $this->ordersModel->where("l.l_panding", "=", "0");
                break;
            default:
                $this->title = "商机订单";
                $this->ordersModel->where("o.o_status", "<>", "6");
                if (!$this->current->m_isAdmin) {
                    $this->ordersModel->where("o.o_mid", "=", $this->current->m_id);
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
            $search = $this->getSearch(false,$post, 'orders');

            $ids = [];
            if (isset($post['ids'])) {
                $ids = $post['ids'];
            } else {
                $res = $this->model->getList($search);
                foreach ($res as $i)
                    $ids[] = $i->o_id;
            }

            $ids = implode(',', $ids);

            $view = $type == 'orders' ? 'kj_vw_my_orders' : 'kj_vw_my_contract';

            $sql = 'SELECT * FROM ' . $view . ' WHERE `o_id` IN (' . $ids . ') ORDER BY `o_updatetime` DESC';

            $res = $this->model->query($sql);


            $arr = [];
            foreach ($res as $item) {
                $arr[$item['o_id']][] = $item;
            }
            $res = [];
            foreach ($arr as $key => $val) {
                $arr1 = $val[0];
                if ($type == 'orders') {
                    $arr1['op_type'] = 'C合同';
                    $arr1['op_used'] = 0;
                    $arr1['o_date'] = $arr1['op_date'] = date('Y/m/d', $arr1['o_date']);
                } else {
                    $arr1['ou_type'] = 'C合同';
                    $arr1['ou_used'] = $arr1['c_used'];
                    $arr1['ou_date'] = date('Y/m/d', $arr1['c_date']);
                    $arr1['o_date'] = date('Y/m/d', $arr1['o_date']);
                }
                $types = getTypeList();
                $arr1['o_type'] = isset($types[$arr1['o_type']])?$types[$arr1['o_type']]:'';

                foreach ($val as $k => $i) {
                    if ($type == 'orders') {
                        if ($i['op_type'] == 1) {
                            $arr1['op_used'] += $i['op_used'];
                            $i['op_type'] = 'I发票';
                        } elseif ($i['op_type'] == 2) {
                            $i['op_type'] = '付款单';
                        } elseif ($i['op_type'] == 3) {
                            $i['op_type'] = '验收单';
                        } else {
                            $i['op_type'] = '';
                        }
                        $i['op_date'] = date('Y/m/d', $i['op_date']);
                    } else {
                        if ($i['ou_type'] == 1) {
                            $i['ou_type'] = '已开票';
                        } elseif ($i['ou_type'] == 2) {
                            $i['ou_type'] = '已交付';
                        } elseif ($i['ou_type'] == 3) {
                            $i['ou_type'] = '已付款';
                        } else {
                            $i['ou_type'] = '';
                        }
                        $i['ou_date'] = date('Y/m/d', $i['ou_date']);
                    }
                    if ($i['o_status'] == 1) {
                        $arr1['o_status'] = $i['o_status'] = '1接洽';
                    } elseif ($i['o_status'] == 2) {
                        $arr1['o_status'] = $i['o_status'] = '2意向';
                    } elseif ($i['o_status'] == 3) {
                        $arr1['o_status'] = $i['o_status'] = '3立项';
                    } elseif ($i['o_status'] == 4) {
                        $arr1['o_status'] = $i['o_status'] = '4招标';
                    } elseif ($i['o_status'] == 5) {
                        $arr1['o_status'] = $i['o_status'] = '5定向';
                    } elseif ($i['o_status'] == 6) {
                        $arr1['o_status'] = $i['o_status'] = '6合同';
                    }
                    if ($i['o_lie'] == 1) {
                        $arr1['o_lie'] = $i['o_lie'] = '1';
                    } elseif ($i['o_lie'] == 2) {
                        $arr1['o_lie'] = $i['o_lie'] = '';
                    } else {
                        $arr1['o_lie'] = $i['o_lie'] = '';
                    }
                    $i['o_type'] = isset($types[$i['o_type']])?$types[$i['o_type']]:'';
                    $i['o_date'] = date('Y/m/d', $i['o_date']);

                    $i['c_no'] = '';
                    $i['c_name'] = '';

                    $val[$k] = $i;
                }
                $val = array_merge([$arr1], $val);
                $arr[$key] = $val;
            }

            if ($type == 'orders') {
                $title = '商机表';
            } else {
                $type = 'orders-' . $type;
                $title = '合同跟踪表';
            }

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
}
