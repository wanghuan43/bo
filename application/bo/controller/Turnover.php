<?php

namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\model\Chances;
use think\Request;

class Turnover extends BoController
{
    private $tModel;

    function __construct(Request $request)
    {
        parent::__construct($request);
        $this->tModel = new Chances();
    }

    public function index()
    {
        $list = $this->tModel->paginate($this->limit);
        $this->assign("lists", $list);
        return $this->fetch();
    }

    public function opt($cid = "")
    {
        $chances = $this->tModel->find($cid);
        $this->assign("chances", $chances);
        $this->assign("cs_id", $cid);
        return $this->fetch();
    }

    public function doOpt()
    {
        $tmpModel = new Chances();
        $where = array();
        $post = Request::instance()->post();
        $data = [
            'cs_mid' => $this->current->m_id,
            'cs_mname' => $this->current->m_name,
            'cs_name' => $post['cs_name'],
            'cs_isShow' => $post['cs_isShow'],
            'cs_createtime' => time()
        ];
        if (!empty($post['cs_id'])) {
            $where['cs_id'] = $post['cs_id'];
            $tmpModel->where('cs_id', '<>', $post['cs_id']);
        }
        $res = 0;
        $c = $tmpModel->where('cs_name', '=', $post['cs_name'])->count();
        if ($c == 0) {
            $res = $this->tModel->save($data, $where);
            if ($res) {
                $res = 1;
                $message = '保存成功';
            } else {
                $message = '保存失败';
            }
        } else {
            $message = "名称重复";
        }
        return ["status" => $res, 'message' => $message];
    }

    public function del()
    {
        $ids = Request::instance()->post('ids/a', false);
        if($ids){
            Chances::destroy($ids);
        }
        return ['status'=>1,'msg'=>'删除成功'];
    }
}