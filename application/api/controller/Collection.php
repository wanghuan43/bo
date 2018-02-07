<?php

namespace app\api\controller;

use app\api\helper\ParameterCheck;
use app\api\libs\ApiController;
use app\bo\model\Member;
use app\bo\model\Orders;
use think\Db;
use think\Request;

class Collection extends ApiController
{
    private $trashed;
    private $check = [
        'lists' => [
            'must' => ['mid'],
            'default' => ['page' => '1', 'limit' => '20']
        ],
        'setCollect' => [
            'must' => ['mid', 'otid'],
            'default' => ['op' => '1', 'type' => 'order']
        ]
    ];

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->controllers = "collection";
        $tmp = new Orders();
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
            $ordersModel = new Orders();
            $ordersModel->alias("o");
            if (!empty($post['search'])) {
                $ordersModel->where("(o.o_no LIKE '%" . $post['search'] . "%' OR 
                o.o_cno LIKE '%" . $post['search'] . "%' OR 
                o.o_mname LIKE '%" . $post['search'] . "%' OR 
                o.o_pname LIKE '%" . $post['search'] . "%' OR 
                o.o_subject LIKE '%" . $post['search'] . "%' OR 
                o.o_coname LIKE '%" . $post['search'] . "%' OR 
                o.o_content LIKE '%" . $post['search'] . "%' OR 
                o.o_dname LIKE '%" . $post['search'] . "%')");
            }
            $ordersModel->join('__FAVORITE__ f', "o.o_id = f.f_oid", "left");
            $temp = $ordersModel
                ->field('o.*')
                ->where("f.f_mid = " . $member->m_id)
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
                        'id' => $value['o_id'],
                        'no' => $value['o_no'],
                        'title' => $value['o_subject'],
                        'department' => $value['o_dname'],
                        'pm' => $value['o_mname'],
                        'date' => date("y-m-d", $value['o_date']),
                        'money' => number_format($value['o_money'], 2),
                        'type' => '项目'
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

    public function setCollect()
    {
        $this->functions = "setCollect";
        $post = Request::instance()->post();
        $return = ParameterCheck::checkPost($post, $this->check['setCollect']);
        if (empty($return['status'])) {
            $member = Member::get(['m_code' => $post['mid']]);
            $order = Orders::get(['o_id' => $post['otid']]);
            $check = Db::name("favorite")
                ->where("f_mid = " . $member->m_id . " AND f_oid = " . $post['otid'])
                ->count();
            if ($check AND $post['op'] == 1) {
                $status = 20;
                $data = "已收藏";
            } else {
                if (!empty($order)) {
                    $status = "0";
                    $data = "收藏成功";
                    switch ($post['op']) {
                        case "1":
                            $temp = Db::name("favorite")
                                ->insert(['f_oid' => $post['otid'], 'f_mid' => $member->m_id]);
                            if (!$temp) {
                                $status = "10";
                                $data = "收藏失败";
                            }
                            break;
                        case "0":
                            $temp = Db::name("favorite")
                                ->where("f_oid = " . $post['otid'] . " AND f_mid = " . $member->m_id)
                                ->delete();
                            if (!$temp) {
                                $status = "15";
                                $data = "取消收藏失败";
                            } else {
                                $status = "5";
                                $data = "取消收藏成功";
                            }
                            break;
                    }
                } else {
                    $status = "10";
                    $data = "收藏失败";
                }
            }
        } else {
            $status = $return['status'];
            $data = $return['data'];
        }
        $this->returnData($data, $status);
    }
}