<?php

namespace app\api\controller;

use app\api\helper\ParameterCheck;
use app\api\libs\ApiController;
use app\bo\model\Member;
use think\Db;
use think\Request;

class Project extends ApiController
{
    private $trashed;
    private $check = [
        'lists' => [
            'must' => ['mid'],
            'default' => ['page' => '1', 'limit' => '20']
        ],
        'detail' => [
            'must' => ['mid', 'pid']
        ]
    ];

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->controllers = "project";
        $tmp = new \app\bo\model\Project();
        $this->trashed = $tmp->getTrashedField();
    }

    public function lists()
    {
        $this->functions = "lists";
        $post = Request::instance()->post();
        $return = ParameterCheck::checkPost($post, $this->check['lists']);
        if (empty($return['status'])) {
            $status = 10;
            $data = "无数据";
            $member = Member::get(['m_code' => $post['mid']]);
            $projectModel = new \app\bo\model\Project();
            $projectModel->alias("p");
            if (!empty($post['search'])) {
                $projectModel->where("(p.p_no LIKE '%" . $post['search'] . "%' OR 
                p.p_name LIKE '%" . $post['search'] . "%' OR 
                p.p_mname LIKE '%" . $post['search'] . "%' OR 
                p.p_content LIKE '%" . $post['search'] . "%' OR 
                p.p_dname LIKE '%" . $post['search'] . "%')");
            }
            if ($member->m_isAdmin != '1') {
                $projectModel->join('__CIRCULATION__ c', "p.p_id = c.ci_otid", "left");
                $projectModel->where("(p.p_id IN (SELECT ci_otid FROM kj_circulation WHERE ci_mid = " . $member->m_id .
                    " and ci_type = 'project') OR p.p_mid = " . $member->m_id . ")")
                    ->group('p.p_id');
            }
            $temp = $projectModel
                ->field('p.*')
                ->where("p." . $this->trashed . "=2")
                ->order("p.p_updatetime DESC,p.p_date DESC")
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
                        'project_id' => $value['p_id'],
                        'project_no' => $value['p_no'],
                        'project_title' => $value['p_name'],
                        'department' => $value['p_dname'],
                        'pm' => $value['p_mname'],
                        'in' => number_format($value['p_income'], 2),
                        'out' => number_format($value['p_pay'], 2),
                        'is_collect' => 0
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
            $temp = Db::name('project')
                ->where('p_id = ' . $post['pid'])
                ->where($this->trashed . "=2")
                ->find();
            if (!empty($temp)) {
                $member = Member::get(['m_code' => $post['mid']]);
                $status = 0;
                $data = [
                    'detail' => [
                        'project_id' => $temp['p_id'],
                        'project_no' => $temp['p_no'],
                        'project_title' => $temp['p_name'],
                        'department' => $temp['p_dname'],
                        'pm' => $temp['p_mname'],
                        'in' => number_format($temp['p_income'], 2),
                        'out' => number_format($temp['p_pay'], 2),
                        'date' => date('Y-m-d', $temp['p_date']),
                        'bakup' => $temp['p_content'],
                        'attachment' => $temp['p_attachment'],
                        'is_collect' => 0
                    ],
                    'order' => $this->getOrder($temp['p_id']),
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
            ->where("o_pid = " . $id)
            ->select();
    }

    private function getStatistics($project)
    {
        $data = [
            'project_in' => number_format($project['p_income'], 2),
            'project_out' => number_format($project['p_pay'], 2),
            'order_in' => '0.00',
            'order_out' => '0.00',
            'contract_in' => '0.00',
            'contract_out' => '0.00',
            'invoice_in' => '0.00',
            'invoice_out' => '0.00',
            'received_in' => '0.00',
            'received_out' => '0.00',
            'acceptance_in' => '0.00',
            'acceptance_out' => '0.00'
        ];
        $temp = Db::name('orders')
            ->field('SUM(o_money) AS totals,o_type')
            ->where("o_pid = " . $project['p_id'] . " AND o_status = 6")
            ->group('o_type')
            ->select();
        foreach ($temp as $val) {
            switch ($val['o_type']) {
                case '1':
                    $data['contract_in'] = number_format($val['totals'], 2);
                    break;
                case '2':
                    $data['contract_out'] = number_format($val['totals'], 2);
                    break;
            }
        }
        $temp = Db::name('orders')
            ->field('SUM(o_money) AS totals,o_type')
            ->where("o_pid = " . $project['p_id'])
            ->group('o_type')
            ->select();
        foreach ($temp as $val) {
            switch ($val['o_type']) {
                case '1':
                    $data['order_in'] = number_format($val['totals'], 2);
                    break;
                case '2':
                    $data['order_out'] = number_format($val['totals'], 2);
                    break;
            }
        }
        $temp = Db::name('orderUsed')
            ->alias('ou')
            ->field('SUM(ou.ou_used) AS totals,ou.ou_type')
            ->join('__ORDERS__ o', 'ou.ou_oid = o.o_id', 'LEFT')
            ->where("o.o_pid = " . $project['p_id'] . " AND o.o_type = 1")
            ->group('ou.ou_type')
            ->select();
        foreach ($temp as $val) {
            switch ($val['ou_type']) {
                case '1':
                    $data['invoice_in'] = number_format($val['totals'], 2);
                    break;
                case '2':
                    $data['acceptance_in'] = number_format($val['totals'], 2);
                    break;
                case '3':
                    $data['received_in'] = number_format($val['totals'], 2);
                    break;
            }
        }
        $temp = Db::name('orderUsed')
            ->alias('ou')
            ->field('SUM(ou.ou_used) AS totals,ou.ou_type')
            ->join('__ORDERS__ o', 'ou.ou_oid = o.o_id', 'LEFT')
            ->where("o.o_pid = " . $project['p_id'] . " AND o.o_type = 2")
            ->group('ou.ou_type')
            ->select();
        foreach ($temp as $val) {
            switch ($val['ou_type']) {
                case '1':
                    $data['invoice_out'] = number_format($val['totals'], 2);
                    break;
                case '2':
                    $data['acceptance_out'] = number_format($val['totals'], 2);
                    break;
                case '3':
                    $data['received_out'] = number_format($val['totals'], 2);
                    break;
            }
        }
        return $data;
    }
}