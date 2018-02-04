<?php

namespace app\api\controller;

use app\api\helper\ParameterCheck;
use app\api\libs\ApiController;
use app\bo\model\Member;
use think\Db;
use think\Request;

class Contract extends ApiController
{
    private $trashed;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->controllers = "contract";
        $tmp = new \app\bo\model\Contract();
        $this->trashed = $tmp->getTrashedField();
    }

    private $check = [
        'lists' => [
            'must' => ['mid'],
            'default' => ['page' => '1', 'limit' => '20']
        ],
        'detail' => [
            'must' => ['mid', 'cid']
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
            $contractModel = new \app\bo\model\Contract();
            $contractModel->alias("cn");
            if (!empty($post['search'])) {
                $contractModel->where("(cn.c_pname LIKE '%" . $post['search'] . "%' OR 
                cn.c_no LIKE '%" . $post['search'] . "%' OR 
                cn.c_name LIKE '%" . $post['search'] . "%' OR 
                cn.c_coname LIKE '%" . $post['search'] . "%' OR 
                cn.c_mname LIKE '%" . $post['search'] . "%' OR 
                cn.c_bakup LIKE '%" . $post['search'] . "%' OR 
                cn.c_dname LIKE '%" . $post['search'] . "%')");
            }
            if ($member->m_isAdmin != '1') {
                $contractModel->join('__CIRCULATION__ c', "cn.c_id = c.ci_otid", "left");
                $contractModel->where("(cn.c_id IN (SELECT ci_otid FROM kj_circulation WHERE ci_mid = " . $member->m_id .
                    " and ci_type = 'orders') OR cn.c_mid = " . $member->m_id . ")")
                    ->group('cn.c_id');
            }
            $temp = $contractModel
                ->field('cn.*')
                ->where("cn." . $this->trashed . "=2")
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
                        'contract_id' => $value['c_id'],
                        'contract_no' => $value['c_no'],
                        'contract_title' => $value['c_name'],
                        'department' => $value['c_dname'],
                        'pm' => $value['c_mname'],
                        'date' => date("y-m-d", $value['c_date']),
                        'money' => number_format($value['c_money'], 2),
                        'type' => getTypeList2($value['c_type']),
                        'is_collect' => 0,
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
            $temp = Db::name('contract')
                ->where('c_id = ' . $post['cid'])
                ->where($this->trashed . "=2")
                ->find();
            if (!empty($temp)) {
                $member = Member::get(['m_code' => $post['mid']]);
                $status = 0;
                $data = [
                    'detail' => [
                        'contract_id' => $temp['c_id'],
                        'contract_no' => $temp['c_no'],
                        'contract_title' => $temp['c_name'],
                        'department' => $temp['c_dname'],
                        'pm' => $temp['c_mname'],
                        'company' => $temp['c_coname'],
                        'date' => date("Y-m-d", $temp['c_date']),
                        'money' => number_format($temp['c_money'], 2),
                        'type' => getTypeList2($temp['c_type']),
                        'month' => $temp['c_accdate'],
                        'bakup' => $temp['c_bakup'],
                        'attachment' => $temp['c_attachment'],
                        'is_collect' => 0,
                    ],
                    'order' => $this->getOrder($temp['c_id']),
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

    private function getOrder($id)
    {
        return Db::name("orders")
            ->field('o_id AS order_id,o_subject AS order_title,o_money AS money')
            ->where("o_cid = " . $id)
            ->select();
    }

    private function getStatistics($contract)
    {
        $data = [
            'company' => $contract['c_coname'],
            'date' => date("Y-m-d", $contract['c_date']),
            'money' => number_format($contract['c_money'], 2),
            'used' => $contract['c_used'],
            'invoice' => '0.00',
            'received' => '0.00',
            'acceptance' => '0.00',
        ];

        $temp = Db::name('orderUsed')
            ->alias('ou')
            ->field('SUM(ou.ou_used) AS totals,ou.ou_type')
            ->join('__ORDERS__ o', 'ou.ou_oid = o.o_id', 'LEFT')
            ->where("o.o_cid = " . $contract['c_id'])
            ->group('ou.ou_type')
            ->select();
        foreach ($temp as $val) {
            switch ($val['ou_type']) {
                case '1':
                    $data['invoice'] = number_format($val['totals'], 2);
                    break;
                case '2':
                    $data['acceptance'] = number_format($val['totals'], 2);
                    break;
                case '3':
                    $data['received'] = number_format($val['totals'], 2);
                    break;
            }
        }
        return $data;
    }
}