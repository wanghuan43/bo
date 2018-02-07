<?php

namespace app\api\controller;

use app\api\helper\ParameterCheck;
use app\api\libs\ApiController;
use app\bo\model\Member;
use app\bo\model\Orders;
use think\Db;
use think\Request;

class Order extends ApiController
{
    private $trashed;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->controllers = "order";
        $tmp = new Orders();
        $this->trashed = $tmp->getTrashedField();
    }

    private $check = [
        'lists' => [
            'must' => ['mid'],
            'default' => ['page' => '1', 'limit' => '20']
        ],
        'detail' => [
            'must' => ['mid', 'oid']
        ]
    ];

    public function lists()
    {
        $this->functions = "lists";
        $post = Request::instance()->post();
        $return = ParameterCheck::checkPost($post, $this->check['lists']);
        if (empty($return['status'])) {
            $status = 10;
            $data = "无数据";
            $member = Member::get(['m_code' => $post['mid']]);
            $orderModel = new Orders();
            $orderModel->alias("o");
            if (!empty($post['search'])) {
                $orderModel->where("(o.o_no LIKE '%" . $post['search'] . "%' OR 
                o.o_cno LIKE '%" . $post['search'] . "%' OR 
                o.o_mname LIKE '%" . $post['search'] . "%' OR 
                o.o_pname LIKE '%" . $post['search'] . "%' OR 
                o.o_subject LIKE '%" . $post['search'] . "%' OR 
                o.o_coname LIKE '%" . $post['search'] . "%' OR 
                o.o_content LIKE '%" . $post['search'] . "%' OR 
                o.o_dname LIKE '%" . $post['search'] . "%')");
            }
            if ($member->m_isAdmin != '1') {
                $orderModel->join('__CIRCULATION__ c', "o.o_id = c.ci_otid", "left");
                $orderModel->where("(o.o_id IN (SELECT ci_otid FROM kj_circulation WHERE ci_mid = " . $member->m_id .
                    " and ci_type = 'orders') OR o.o_mid = " . $member->m_id . ")")
                    ->group('o.o_id');
            }
            $temp = $orderModel
                ->field('o.*')
                ->where("o." . $this->trashed . "=2")
                ->order("o.o_updatetime DESC,o.o_date DESC")
                ->paginate(['list_rows' => $post['limit'], 'page' => $post['page']])
                ->toArray();
            if ($temp['total'] > 0) {
                $status = 0;
                $data = [
                    'page' => $temp['current_page'],
                    'pageCount' => $temp['last_page'],
                    'limit' => $temp['per_page'],
                    'total' => $temp['total'],
                    'list' => []
                ];
                foreach ($temp['data'] as $key => $value) {
                    $row = [
                        'order_id' => $value['o_id'],
                        'order_no' => $value['o_no'],
                        'order_title' => $value['o_subject'],
                        'project_id' => $value['o_pid'],
                        'project_title' => $value['o_pname'],
                        'department' => $value['o_dname'],
                        'pm' => $value['o_mname'],
                        'order_date' => date("Y-m-d", $value['o_date']),
                        'order_money' => number_format($value['o_money'], 2),
                        'order_type' => getTypeList($value['o_type']),
                        'is_collect' => ParameterCheck::checkCollect($value['o_id'], $member->m_id),/*0：当前未收藏,1：已收藏*/
                    ];
                    $data['list'][] = $row;
                }
            }
        } else {
            $status = $return['status'];
            $data = $return['data'];
        }
        $this->returnData($data, $status);
    }

    public function detail()
    {
        $this->functions = "detail";
        $post = Request::instance()->post();
        $return = ParameterCheck::checkPost($post, $this->check['detail']);
        if (empty($return['status'])) {
            $status = 10;
            $temp = Db::name('orders')
                ->where('o_id = ' . $post['oid'])
                ->where($this->trashed . "=2")
                ->find();
            if (!empty($temp)) {
                $member = Member::get(['m_code' => $post['mid']]);
                $status = 0;
                $data = [
                    'detail' => [
                        'order_id' => $temp['o_id'],
                        'order_no' => $temp['o_no'],
                        'contract_id' => $temp['o_cid'],
                        'contract_no' => $temp['o_cno'],
                        'order_title' => $temp['o_subject'],
                        'project_id' => $temp['o_pid'],
                        'project_title' => $temp['o_pname'],
                        'company' => $temp['o_coname'],
                        'department' => $temp['o_dname'],
                        'pm' => $temp['o_mname'],
                        'order_date' => date("Y-m-d", $temp['o_date']),
                        'order_money' => number_format($temp['o_money'], 2),
                        'order_type' => $temp['o_type'] == '1' ? "收入" : "支出",
                        'order_status' => getStatusList($temp['o_status']),
                        'tag' => ParameterCheck::getTags($temp['o_id'], 'orders'),
                        'circulation' => ParameterCheck::getCirculations($temp['o_id'], 'orders'),
                        'chance' => ParameterCheck::getChance($temp['o_deal']),
                        'lie' => getLieList($temp['o_lie']),
                        'tax' => getTaxList($temp['o_tax']),
                        'num' => number_format($temp['o_num'], 2),
                        'bakup' => $temp['o_content'],
                        'attachment' => $temp['o_attachment'],
                        'is_collect' => ParameterCheck::checkCollect($temp['o_id'], $member->m_id)
                    ],
                    'project' => $this->getProject($temp['o_id']),
                    'used' => $this->getUsed($temp['o_id']),
                    'statistics' => $this->getStatistics($temp),
                ];
            } else {
                $data = "无数据";
            }
        } else {
            $status = $return['status'];
            $data = $return['data'];
        }
        $this->returnData($data, $status);
    }

    private function getProject($oid)
    {
        $data = [
            'invoice' => [],
            'received' => [],
            'acceptance' => []
        ];
        $temp = Db::name("orderProject")->where("op_oid = " . $oid)->select();
        if (count($temp) > 0) {
            foreach ($temp as $value) {
                $row = [
                    'date' => date('Y-m-d', $value['op_date']),
                    'percent' => $value['op_percent'],
                    'money' => number_format($value['op_used'], 2)
                ];
                switch ($value['op_type']) {
                    case "1":
                        $data['invoice'][] = $row;
                        break;
                    case "2":
                        $data['acceptance'][] = $row;
                        break;
                    case "3":
                        $data['received'][] = $row;
                        break;
                }
            }
        }
        return $data;
    }

    private function getUsed($oid)
    {
        $data = [
            'invoice' => [],
            'received' => [],
            'acceptance' => []
        ];
        $temp = Db::name("orderUsed")->where("ou_oid = " . $oid)->select();
        if (count($temp) > 0) {
            foreach ($temp as $value) {
                $row = [
                    'date' => date('Y-m-d', $value['ou_date']),
                    'no' => '',
                    'money' => number_format($value['ou_used'], 2)
                ];
                switch ($value['ou_type']) {
                    case "1":
                        $tmp = Db::name('invoice')->where("i_id = " . $value['ou_otid'])->find();
                        $row['no'] = $tmp['i_no'];
                        $data['invoice'][] = $row;
                        break;
                    case "2":
                        $tmp = Db::name('acceptance')->where("a_id = " . $value['ou_otid'])->find();
                        $row['no'] = $tmp['a_no'];
                        $data['acceptance'][] = $row;
                        break;
                    case "3":
                        $tmp = Db::name('received')->where("r_id = " . $value['ou_otid'])->find();
                        $row['no'] = $tmp['r_no'];
                        $data['received'][] = $row;
                        break;
                }
            }
        }
        return $data;
    }

    private function getStatistics($order)
    {
        $project = Db::name('project')->where("p_id = " . $order['o_pid'])->find();
        $invoice = Db::name("orderUsed")->field('SUM(ou_used) AS totals')
            ->where("ou_oid = " . $order['o_id'] . " AND ou_type = '1'")->find();
        $acceptance = Db::name("orderUsed")->field('SUM(ou_used) AS totals')
            ->where("ou_oid = " . $order['o_id'] . " AND ou_type = '2'")->find();
        $received = Db::name("orderUsed")->field('SUM(ou_used) AS totals')
            ->where("ou_oid = " . $order['o_id'] . " AND ou_type = '3'")->find();
        $data = [
            'project_in' => number_format($project['p_income'], 2),
            'project_out' => number_format($project['p_pay'], 2),
            'order_in' => $order['o_type'] == "1" ? number_format($order['o_money'], 2) : '0.00',
            'order_out' => $order['o_type'] == "2" ? number_format($order['o_money'], 2) : '0.00',
            'contract_in' =>
                $order['o_status'] == 6 ?
                    ($order['o_type'] == "1" ? number_format($order['o_money'], 2) : '0.00') : '0.00',
            'contract_out' =>
                $order['o_status'] == 6 ?
                    ($order['o_type'] == "2" ? number_format($order['o_money'], 2) : '0.00') : '0.00',
            'invoice_in' => $order['o_type'] == "1" ? number_format($invoice['totals'], 2) : '0.00',
            'invoice_out' => $order['o_type'] == "2" ? number_format($invoice['totals'], 2) : '0.00',
            'received_in' => $order['o_type'] == "1" ? number_format($received['totals'], 2) : '0.00',
            'received_out' => $order['o_type'] == "2" ? number_format($received['totals'], 2) : '0.00',
            'acceptance_in' => $order['o_type'] == "1" ? number_format($acceptance['totals'], 2) : '0.00',
            'acceptance_out' => $order['o_type'] == "2" ? number_format($acceptance['totals'], 2) : '0.00'
        ];
        return $data;
    }
}