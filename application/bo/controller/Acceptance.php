<?php

namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\libs\CustomUtils;
use app\bo\model\Logs;
use app\bo\model\OrderUsed;
use think\Request;

class Acceptance extends BoController
{
    public function __construct(Request $request)
    {
        $this->model = new \app\bo\model\Acceptance();
        parent::__construct($request);
    }

    public function all($trashed=2)
    {
        $this->assign("stype", "acceptance");
        return parent::all($trashed); // TODO: Change the autogenerated stub
    }

    public function searchAcceptance()
    {
        $acceptanceModel = new \app\bo\model\Acceptance();
        $this->assign("type", "acceptance");
        $acceptanceModel->where("a.a_noused", "<>", "0");
        return $this->search($acceptanceModel, "common/popused");
    }

    public function searchAcceptanceNoList()
    {
        $acceptanceModel = new \app\bo\model\Acceptance();
        $this->assign("type", "acceptance");
        $this->other = "main-pannel";
        return $this->search($acceptanceModel);
    }

    protected function doAdd()
    {

        $post = $this->request->post();

        $data['a_no'] = trim($post['no']);
        $data['a_content'] = trim($post['content']);
        $data['a_subject'] = trim($post['subject']);
        $data['a_type'] = $post['type'];
        $data['a_money'] = trim($post['money']);
        $data['a_date'] = trim($post['date']);
        $data['a_coid'] = trim($post['coid']);
        $data['a_coname'] = trim($post['coname']);
        $data['a_accdate'] = trim($post['accdate']);
        $data['a_mid'] = $this->current->m_id;
        $data['a_mname'] = $this->current->m_name;
        $data['a_dname'] = $this->current->m_department;
        $data['a_did'] = $this->current->m_did;

        $validate = validate('Acceptance');

        if ($validate->check($data)) {

            $data['a_date'] = strtotime($data['a_date']);

            if (empty($data['a_accdate'])) {
                $data['a_accdate'] = date('ym', $data['a_date']);
            }

            $data['a_createtime'] = $data['a_updatetime'] = time();

            $data['a_money'] = $data['a_noused'] = floatval($data['a_money']);

            $file = $this->request->file('attachment');

            $res = $this->uploadFile($file);

            if ($res['flag'] === 0) {
                $ret = $res;
            } else {
                if ($res['flag'] === 1) {
                    $data['a_attachment'] = $res['name'];
                }
                if ($res = $this->model->insert($data)) {
                    $ret = ['flag' => 1, 'msg' => '添加成功'];
                } else {
                    $ret = ['flag' => 0, 'msg' => '添加失败'];
                }
            }
        } else {
            $ret = ['flag' => 0, 'msg' => $validate->getError()];
        }

        return $ret;

    }

    public function detail($id)
    {
        $data = $this->model->getDataById($id);
        $modelOrderUsed = new OrderUsed();
        $orders = $modelOrderUsed->getOrderUsedByOtid($id, 2);
        $this->setOrderUsed($orders);
        $this->setUpdateParams($data['a_mid']);
        if(!empty($orders)){
            $this->assign('readonly',true);
        }
        $this->assign('data', $data);
        $this->assign('orders', $orders);
        return $this->fetch();
    }

    public function update()
    {
        $post = $this->request->post();

        $arr = ['id', 'no', 'date', 'money', 'type', 'coname', 'coid', 'mname', 'mid', 'subject', 'content', 'used', 'noused'];

        foreach ($arr as $i) {
            $data['a_' . $i] = trim($post[$i]);
        }

        $mModel = new \app\bo\model\Member();
        $m = $mModel->where('m_id','=',$data['a_mid'])->find();
        if($m){
            $data['a_dname'] = $m->m_department;
            $data['a_did'] = $m->m_did;
        }

        $validate = \validate('Acceptance');

        if ($validate->check($data)) {

            $data['a_date'] = strtotime($data['a_date']);
            $data['a_updatetime'] = time();

            $data['a_noused'] = floatval($data['a_money']) - floatval($data['a_used']);

            $file = $this->request->file('attachment');

            $res = $this->uploadFile($file);

            if ($res['flag'] === 0) {
                $ret = $res;
            } else {

                if ($res['flag'] === 1) {
                    $data['a_attachment'] = $res['name'];
                }
                $old = $this->model->getDataById($data['a_id']);
                if ($res = $this->model->save($data, $data['a_id'])) {
                    $logModel = new Logs();
                    $logModel->saveLogs($data, $old, $data['a_id'], 'acceptance');
                    $ret = ['flag' => 1, 'msg' => '更新成功'];
                    if(isset($data['i_attachment']) && $data['i_attachment']){
                        if($this->getAttachmentMimeType($data['i_attachment']) == 'image'){
                            $ret['image'] = $data['i_attachment'];
                        }else{
                            $ret['file'] = $data['i_attachment'];
                        }
                    }
                } else {
                    $ret = ['flag' => 0, 'msg' => '更新失败，请确认是否有做过修改'];
                }
            }

        } else {
            $ret = ['flag' => 0, 'msg' => $validate->getError()];
        }

        return $ret;

    }

    public function export()
    {
        return $this->doExport();
    }

    protected function deleteCheck($ids)
    {
        $failed = [];

        $mOrderUsed = new OrderUsed();

        foreach ($ids as $id){
            $res = $mOrderUsed->where('ou_type','=',2)->where('ou_otid','=',$id)->find();
            if($res){
                $failed[] = $id;
            }
            if($this->current->m_isAdmin != 1){
                $a = $this->model->where('a_id','=',$id)->find();
                if(empty($a) || $a->a_mid != $this->current->m_id)
                    $failed[] = $id;
            }
        }

        if(empty($failed)){
            $ret = true;
        }else{
            $ret = ['failed'=>$failed];
        }

        return $ret;
    }

}