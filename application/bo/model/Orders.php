<?php

namespace app\bo\model;

use app\bo\libs\BoModel;
use think\Exception;

class   Orders extends BoModel
{
    protected $pk = 'o_id';
    protected $ouList = "";

    protected $searchable = [
        'o_pname' => [
            'name' => '项目名称',
            'type' => 'text',
            'operators' => [
                'like' => '包含',
                '=' => '等于'
            ]
        ],
        'o_no' => [
            'name' => '订单号',
            'type' => 'text',
            'operators' => [
                'like' => '包含',
                '=' => '等于'
            ]
        ],
        'o_mname' => [
            'name' => '责任人',
            'type' => 'text',
            'operators' => [
                'like' => '包含',
                '=' => '等于'
            ]
        ],
        'o_type' => [
            'name' => '购销',
            'type' => 'select',
            'operators' => [
                '=' => '等于'
            ],
            'options' => [
                '0' => '-请选择-',
                '1' => '收入',
                '2' => '支出',
            ]
        ],
        'o_cno' => [
            'name' => '合同编号',
            'type' => 'text',
            'operators' => [
                "like" => "包含",
                "=" => "等于",
            ]
        ],
        'o_money' => [
            'name' => '总金额',
            'type' => 'price',
            'operators' => [
                "between" => "介于",
                "=" => "等于",
                "<=" => "小于等于",
                ">=" => "大于等于",
            ]
        ],
        "o_date" => array(
            "name" => "订单日期",
            "type" => "date",
            "operators" => array(
                "between" => "介于",
                "=" => "等于",
                ">" => "大于",
                "<" => "小于",
            ),
        ),
        'o_coname' => [
            'name' => '公司名称',
            'type' => 'text',
            'operators' => [
                "like" => "包含",
                "=" => "等于",
            ]
        ],
    ];

    public function getList($search, $limit = false)
    {
        $member = $this->getCurrent();
        $this->alias('o');
        if (!$member->m_isAdmin) {
            $this->join('__CIRCULATION__ c', "o.o_id = c.ci_otid AND c.ci_type = 'orders'");
            $this->where("c.ci_mid", "=", $member->m_id);
        }
        $this->field("o.*");
        foreach ($search as $key => $value) {
            $this->where("o." . $value['field'], $value['opt'], $value['val']);
        }
        if (empty($limit)) {
            $list = $this->select();
        } else {
            $list = $this->paginate($limit);
        }
        return $list;
    }

    public function getOrderNO($p_id, $otype)
    {
        $projectModel = new Project();
        $project = $projectModel->get($p_id);
        $str = "C";
        if ($otype == '1') {
            $str = "X";
        }
        $c = $this->where("o_pid", "=", $p_id)->where("o_type", "=", $otype)->count();
        return $project->p_no . "-" . $str . str_pad(($c + 1), 4, "0", STR_PAD_LEFT);
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
        $tlm = new Taglink();
        $clm = new Circulation();
        $opm = new OrderProject();
        $oum = new OrderUsed();
        $con = new Contract();
        $tagList = !empty($data['tagList']) ? $data['tagList'] : array();
        $cList = !empty($data['cList']) ? $data['cList'] : array();
        $pj = !empty($data['project']) ? $data['project'] : array();
        $used = !empty($data['used']) ? $data['used'] : array();
        $id = !empty($data['o_id']) ? $data['o_id'] : "";
        unset($data['tagList']);
        unset($data['cList']);
        unset($data['project']);
        unset($data['used']);
        if (!empty($data['o_cid']) and empty($id)) {
            $c = $con->where("c_id", "=", $data['o_cid'])->find();
            $c->c_used = $c->c_used + $data['o_money'];
            $c->c_noused = $c->c_money - $c->c_used;
            if ($c->c_noused < 0) {
                return -10;
            }
            $c->save();
        }
        if (!empty($id)) {
            $this->isUpdate(true);
        }
        $result = parent::save($data, $where, $sequence);
        $tlm->setTagLink($this->o_id, $tagList, "orders");
        $clm->setCirculation($this->o_id, $cList, "orders");
        $opm->setOrderProject($this->o_id, $data['o_date'], $pj);
        $oum->setOrderUsed($this->o_id, $used);
        return $result;
    }

    public function deleteOrders($id)
    {
        $order = $this->find($id);
        if (!empty($order->o_cid)) {
            $cont = Contract::get($order->o_cid);
            if ($cont) {
                $cont->c_used -= $order->o_money;
                $cont->c_noused += $order->o_money;
                $cont->isUpdate(true);
                $cont->save();
            }
        }
        return $order->delete(); // TODO: Change the autogenerated stub
    }

    public function getOrdersByContractId($cid)
    {
        return $this->where('o_cid', '=', $cid)->select();
    }

    public function import($dataset)
    {
        $mChances = new Chances();
        $mMember = new Member();
        $mProject = new Project();
        $mCompany = new Company();
        $mDepartment = new Department();
        $mContract = new Contract();
        foreach ($dataset as $key => $data) {
            if (isset($data['flag']) && $data['flag'] != 1) {
                unset($dataset[$key]);
                continue;
            } else {
                unset($data['flag']);
            }
            if (!isset($data['o_deal']) || empty($data['o_deal'])) {
                $data['o_deal'] = 0;
            } else {
                $data['o_deal'] = $mChances->getIdByName($data['o_deal']);
            }
            if ($data['o_mname']) { //责任人存在
                $member = $mMember->getMemberByName($data['o_mname'], $data['m_department']);
                if ($member)
                    $data['o_mid'] = $member->m_id;
            }
            unset($data['m_department']);
            if ($data['p_no']) {
                $project = $mProject->getProject($data['p_no'], $data['o_pname']);
                if ($project) {
                    $data['o_pid'] = $project->p_id;
                    $data['o_pname'] = $project->p_name;
                }
                unset($data['p_no']);
            }
            if (!isset($data['o_no']) || empty($data['o_no'])) {
                if (!isset($data['o_pid'])) {
                    unset($dataset[$key]);
                    continue;
                } else {
                    $data['o_no'] = $this->getOrderNO($data['o_pid'], $data['o_type']);
                }
            }
            if ($data['o_coname']) {
                $co_type = $data['o_type'] == 1 ? 2 : 1;
                $company = $mCompany->where('co_name', '=', $data['o_coname'])->where('co_status', '=', 1)
                    ->where('co_type', '=', $co_type)->find();
                if ($company) {
                    $data['o_coid'] = $company->co_id;
                }
            }
            if ($data['o_dname']) {
                $d = $mDepartment->where('d_name', '=', $data['o_dname'])->find();
                if ($d) {
                    $data['o_did'] = $d->d_id;
                }
            }
            if (isset($data['o_cno'])) {
                $contract = $mContract->where('c_no', '=', $data['o_cno'])->find();
                if ($contract) {
                    $data['o_cid'] = $contract->c_id;
                }
            }
            $data['o_createtime'] = $data['o_updatetime'] = time();
            $dataset[$key] = $data;
        }
        //var_dump($dataset);die;
        return parent::import($dataset); // TODO: Change the autogenerated stub
    }

    protected function doImport($dataset = false)
    {
        return $this->insertDuplicate($dataset);
    }

    public function reportList($cols, &$obj, $type = "", $begin = "2", $id = "")
    {
        $this->getOu();
        $count = $begin;
        switch ($type) {
            case "project":
                $this->where("o_pid", "=", $id);
                break;
            case "contract":
                $this->where("o_cid", "=", $id);
                break;
            case "invoice":
            case "received":
            case "acceptance":
                $this->where("o_id", "=", $id);
                break;
        }
        $list = $this->select();
        foreach ($list as $key => $value) {
            $count = $cell = intval($begin) + intval($key);
            $this->setCellValues($cell, $cols, $value, $obj);
        }
        return $count;
    }

    private function setCellValues($cell, $cols, $value, &$obj)
    {
        foreach ($cols as $key => $val) {
            $v = "";
            if (isset($value[$val])) {
                switch ($val){
                    case "o_lie":
                        $v = getLieList($value[$val]);
                        break;
                    case "o_tax":
                        $v = getTaxList($value[$val]);
                        break;
                    case "o_date":
                        $v = date("Y-m-d", $value[$val]);
                        break;
                    default:
                        $v = $value[$val];
                        break;
                }
            } else {
                $v = $this->customCellValue($value, $val);
            }
            $obj->setCellValue($key . $cell, $v);
        }
    }

    private function customCellValue($value, $val)
    {
        $v = "";
        $tax = intval(getTaxList($value['o_tax'])) / 100;
        $list = [1 => [0,0], 2 => [0,0], 3 => [0,0]];
        if (isset($this->ouList[$value['o_id']])) {
            foreach ($this->ouList[$value['o_id']] as $key=>$t) {
                $list[$key] = $t;
            }
        }
        switch ($val) {
            case "o_money" . $value['o_type'] . "_tax":
                $v = $value['o_money'];
                break;
            case "o_money" . $value['o_type']:
                $v = $value['o_money'] - $value['o_money'] * $tax;
                break;
            case "o_cmoney" . $value['o_type'] . "_tax":
                if (!empty($value['o_cid'])) {
                    $v = $value['o_money'];
                } else {
                    $v = 0;
                }
                break;
            case "o_cmoney" . $value['o_type']:
                if (!empty($value['o_cid'])) {
                    $v = $value['o_money'] - $value['o_money'] * $tax;
                } else {
                    $v = 0;
                }
                break;
            case "o_amoney" . $value['o_type'] . "_tax":
                $v = $list[2][0];
                break;
            case "o_amoney" . $value['o_type']:
                $v = $list[2][0] - $list[2][0] * $tax;
                break;
            case "o_imoney" . $value['o_type'] . "_tax":
                $v = $list[1][0] - $list[1][0];
                break;
            case "o_imoney" . $value['o_type']:
                $v = $list[1][0] - $list[1][0] * $tax;
                break;
            case "o_rmoney" . $value['o_type'] . "_tax":
                $v = $list[3][0] - $list[3][0];
                break;
            case "o_rmoney" . $value['o_type']:
                $v = $list[3][0] - $list[3][0] * $tax;
                break;
            case "o_wmoney1":
                $v = $value['o_money'] - $list[1][0];
                break;
            case "o_wimoney2":
                $v = $value['o_money'] - $list[3][0];
                break;
            case "o_cr_date":
                $v = $list[2][1];
                break;
            case "o_ci_date":
                $v = $list[1][1];
                break;
            case "o_ca_date":
                $v = $list[3][1];
                break;
        }
        return $v;
    }

    private function getOu()
    {
        $ou = new OrderUsed();
        $tmp = $ou->field("sum(ou_used) as su,ou_type,ou_oid,ou_date")
            ->group("ou_oid,ou_type")->order("ou_date", "desc")->select();
        $list = [];
        foreach ($tmp as $value) {
            $list[$value['ou_oid']][$value['ou_type']] = [$value['su'],date("Y-m-d", $value['ou_date'])];
        }
        $this->ouList = $list;
    }
}