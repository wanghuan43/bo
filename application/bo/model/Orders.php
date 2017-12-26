<?php

namespace app\bo\model;

use app\bo\libs\BoModel;
use app\bo\libs\CustomUtils;
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
                'like' => '包含'
            ]
        ],
        'o_no' => [
            'name' => '项目号/订单号',
            'type' => 'text',
            'operators' => [
                'like' => '包含'
            ]
        ],
        'o_mname' => [
            'name' => '责任人',
            'type' => 'text',
            'operators' => [
                'like' => '包含'
            ]
        ],
        'o_type' => [
            'name' => '收支',
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
        'o_lie' => [
            'name' => '内/外',
            'type' => 'select',
            'operators' => [
                '=' => '等于'
            ],
            'options' => [
                '0' => '-请选择-',
                '1' => '内部',
                '2' => '外部'
            ]
        ],
        'o_cno' => [
            'name' => '合同编号',
            'type' => 'text',
            'operators' => [
                "like" => "包含"
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
                "like" => "包含"
            ]
        ],
        'o_subject' => [
            'name' => '订单摘要',
            'type' => 'text',
            'operators' => [
                'like' => '包含'
            ]
        ],
        'o_dname' => [
            'name' => '部门',
            'type' => 'text',
            'operators' => [
                'like' => '包含'
            ]
        ]
    ];

    public function getList($search, $limit = false)
    {
        $this->alias('o');
        $this->field("o.*");
        foreach ($search as $key => $value) {
            $this->where("o." . $value['field'], $value['opt'], $value['val']);
        }
        $this->order("o.o_id", "desc");
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
        $tmp = $this->field("o_no")->where("o_pid", "=", $p_id)->where("o_type", "=", $otype)->order("o_createtime", "desc")->find();
        $c = 0;
        if ($tmp) {
            $c = intval(str_replace(['C', 'X'], "", explode("-", $tmp->o_no)[1]));
        }
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
        $tagList = !empty($data['tagList']) ? $data['tagList'] : array();
        $cList = !empty($data['cList']) ? $data['cList'] : array();
        $pj = !empty($data['project']) ? $data['project'] : array();
        $used = (!empty($data['used']) AND !empty($data['o_cid'])) ? $data['used'] : array();
        $id = !empty($data['o_id']) ? $data['o_id'] : "";
        unset($data['tagList']);
        unset($data['cList']);
        unset($data['project']);
        unset($data['used']);
        if (!empty($data['o_cid'])) {
            $con = new Contract();
            $c = $con->where("c_id", "=", $data['o_cid'])->find();
            if ($c) {
                $c->c_used = $c->c_used + $data['o_money'];
                $c->c_noused = $c->c_money - $c->c_used;
                if ($c->c_noused < 0) {
                    return -10;
                }
                $c->save();
            }
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

    protected function doImport($dataset = false)
    {

        foreach ($dataset as $key => $data) {
            $mChances = new Chances();
            $mMember = new Member();
            $mProject = new Project();
            $mCompany = new Company();
            $mDepartment = new Department();
            $mContract = new Contract();

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


            $did = $mDepartment->getDepartmentIdByName($data['o_dname']);
            if ($did) {
                $data['o_did'] = $did;
            } else {
                CustomUtils::writeImportLog('Department ID is null - ' . serialize($data), strtolower($this->name));
                unset($dataset[$key]);
                continue;
            }


            $m = $mMember->getMemberByName($data['o_mname'], $data['o_dname']);
            if (empty($m)) {
                CustomUtils::writeImportLog('Member ID is null - ' . serialize($data), strtolower($this->name));
                unset($dataset[$key]);
                continue;
            } else {
                $data['o_mid'] = $m->m_id;
            }

            $project = $mProject->getProject($data['p_no']);
            unset($data['p_no']);
            if ($project) {
                $data['o_pid'] = $project->p_id;
                $data['o_pname'] = $project->p_name;
            } else {
                CustomUtils::writeImportLog('Project ID is null - ' . serialize($data), strtolower($this->name));
                unset($dataset[$key]);
                continue;
            }

            if ($data['o_lie'] == 1) {
                $did = $mDepartment->getDepartmentIdByName($data['o_coname']);
                if ($did) {
                    $data['o_coid'] = $did;
                } else {
                    CustomUtils::writeImportLog('Inner department ID is null - ' . serialize($data), strtolower($this->name));
                    unset($dataset[$key]);
                    continue;
                }
            } else {
                $co_type = $data['o_type'] == 1 ? 2 : 1;
                $company = $mCompany->getCompany(false, $data['o_coname'], $co_type);
                if ($company) {
                    $data['o_coid'] = $company->co_id;
                } else {
                    CustomUtils::writeImportLog('Company ID is null - ' . serialize($data), strtolower($this->name));
                    unset($dataset[$key]);
                    continue;
                }
            }


            if (isset($data['o_cno'])) {
                $contract = $mContract->where('c_no', '=', $data['o_cno'])->find();
                if ($contract) {
                    $data['o_cid'] = $contract->c_id;
                }
            }
            if (empty($data['o_date'])) {
                $data['o_date'] = 0;
            }

            $data['o_createtime'] = $data['o_updatetime'] = time();
            $dataset[$key] = $data;

        }

        return $this->insertDuplicate($dataset);

    }

    public function updateForeign($dataset)
    {

        foreach ($dataset as $data) {
            if (empty($data['o_no'])) {
                CustomUtils::writeImportLog('Order no is null - ' . serialize($data), $this->name);
                continue;
            }
            if (empty($data['o_foreign'])) {
                continue;
            }

            $o = $this->where('o_no', '=', $data['o_no'])->find();
            if (empty($o)) {
                CustomUtils::writeImportLog('Order is not found - ' . $data['o_no'], $this->name);
                continue;
            }
            $fo = $this->where('o_no', '=', $data['o_foreign'])->find();
            if (empty($fo)) {
                CustomUtils::writeImportLog('Foreign order is not found - ' . $data['o_foreign'], $this->name);
                continue;
            }

            try {
                $d['o_foreign'] = $fo->o_id;
                $d['o_id'] = $o->o_id;
                $res = parent::save($d, ['o_id' => $o->o_id]);
                if (empty($res)) {
                    CustomUtils::writeImportLog('Fail to save - ' . serialize($data), $this->name);
                }
            } catch (\Exception $e) {
                CustomUtils::writeImportLog('Save exception - ' . $e->getMessage() . ' - ' . serialize($data), $this->name);
            }

        }

        return true;

    }

    public function reportList($cols, &$obj, $type = "", $begin = "2", $id = "", $search = "")
    {
        $this->getOu();
        $count = $begin;
        $in = false;
        $f = "o";
        $this->alias($f);
        switch ($type) {
            case "project":
                $this->where($f . ".o_pid", "=", $id);
                break;
            case "contract":
                $this->where($f . ".o_cid", "=", $id);
                break;
            case "invoice":
            case "received":
            case "acceptance":
                $this->where($f . ".o_id", "=", $id);
                break;
        }
        if (is_array($search)) {
            foreach ($search['fields'] as $key => $value) {
                $val = count($search['values'][$key]) > 1 ? $search['values'][$key] : trim($search['values'][$key][0]);
                $opt = trim($search['operators'][$key]);
                $val = is_array($val) ? ((empty($val['0']) AND empty($val['1'])) ? "" : $val) : $val;
                if (!empty($val)) {
                    if ($opt == "between") {
                        $val = is_array($val) ? $val : explode(" ~ ", $val);
                    } elseif ($opt == "like") {
                        $val = "%$val%";
                    }
                    if (in_array($value, ['i_type', 'i_tax', 'c_type', 'a_type', 'r_type', 'o_type', 'm_isAdmin']) AND empty($val)) {
                        break;
                    }
                    $this->where($f . "." . $value, $opt, $val);
                }
            }
        }
        $list = $this->select();
        foreach ($list as $key => $value) {
            $in = true;
            $count = $cell = intval($begin) + intval($key);
            $this->setCellValues($cell, $cols, $value, $obj);
        }
        return ($in ? $count : $in);
    }

    private function setCellValues($cell, $cols, $value, &$obj)
    {
        foreach ($cols as $key => $val) {
            $v = "";
            if (isset($value[$val])) {
                switch ($val) {
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
        $list = [1 => [0, 0], 2 => [0, 0], 3 => [0, 0]];
        if (isset($this->ouList[$value['o_id']])) {
            foreach ($this->ouList[$value['o_id']] as $key => $t) {
                $list[$key] = $t;
            }
        }
        switch ($val) {
            case "o_money" . $value['o_type'] . "_tax":
                $v = $value['o_money'];
                break;
            case "o_money" . $value['o_type']:
                $v = $value['o_money'] / (1 + $tax);
                break;
            case "o_iomoney" . $value['o_type'] . "_tax":
                $v = $list[1][0] / (1 + $tax) - $list[3][0] / (1 + $tax);
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
                    $v = $value['o_money'] / (1 + $tax);
                } else {
                    $v = 0;
                }
                break;
            case "o_amoney" . $value['o_type'] . "_tax":
                $v = $list[2][0];
                break;
            case "o_amoney" . $value['o_type']:
                $v = $list[2][0] / (1 + $tax);
                break;
            case "o_imoney" . $value['o_type'] . "_tax":
                $v = $list[1][0];
                break;
            case "o_imoney" . $value['o_type']:
                $v = $list[1][0] / (1 + $tax);
                break;
            case "o_rmoney" . $value['o_type'] . "_tax":
                $v = $list[3][0];
                break;
            case "o_rmoney" . $value['o_type']:
                $v = $list[3][0] / (1 + $tax);
                break;
            case "o_wimoney".$value['o_type']:
                $v = $value['o_money'] - $list[1][0];
                break;
            case "o_wmoney" . $value['o_type']:
                if (empty($value['o_cid'])) {
                    $v = $value['o_money'];
                } else {
                    $v = 0;
                }
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
            $list[$value['ou_oid']][$value['ou_type']] = [$value['su'], date("Y-m-d", $value['ou_date'])];
        }
        $this->ouList = $list;
    }
}