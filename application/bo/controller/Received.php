<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\model\Logs;
use app\bo\model\OrderUsed;
use think\Request;
use app\bo\model\Received as MReceived;

class Received extends BoController
{
    public function __construct(Request $request)
    {
        $this->model = new MReceived();
        parent::__construct($request);
    }

    public function searchReceived()
    {
        $receivedModel = new \app\bo\model\Received();
        $receivedModel->where("r.r_noused", "<>", "0");
        $this->assign("type", "received");
        return $this->search($receivedModel, "common/popused");
    }

    public function searchReceivedNoList()
    {
        $receivedModel = new \app\bo\model\Received();
        $this->assign("type", "received");
        $this->other = "main-pannel";
        return $this->search($receivedModel);
    }

    public function all($trashed=2)
    {
        $this->assign("stype", "received");
        return parent::all($trashed); // TODO: Change the autogenerated stub
    }

    public function doAdd()
    {
        $post = $this->request->post();

        $data['r_no'] = trim($post['no']);
        $data['r_type'] = $post['type'];
        $data['r_money'] = trim($post['money']);
        $data['r_date'] = trim($post['date']);
        $data['r_coid'] = trim($post['coid']);
        $data['r_coname'] = trim($post['coname']);
        $data['r_subject'] = trim($post['subject']);
        $data['r_content'] = trim($post['content']);
        $data['r_accdate'] = trim($post['accdate']);
        $data['r_mid'] = $this->current->m_id;
        $data['r_mname'] = $this->current->m_name;
        $data['r_did'] = $this->current->m_did;
        $data['r_dname'] = $this->current->m_department;
        $data['r_createtime'] = $data['r_updatetime'] = time();

        $validate = validate('Received');

        if($validate->check($data)){

            $data['r_date'] = strtotime($data['r_date']);
            if(empty($data['r_accdate'])){
                $data['r_accdate'] = date('ym',$data['r_date']);
            }
            $data['r_money'] = $data['r_noused'] = floatval($data['r_money']);

            $file = $this->request->file('attachment');

            $res = $this->uploadFile($file);

            if($res['flag']===0){
                $ret = $res;
            }else {

                if($res['flag'] === 1){
                    $data['r_attachment'] = $res['name'];
                }

                if ($res = $this->model->save($data)) {

                    $ret = ['flag' => 1, 'msg' => '添加成功'];

                } else {
                    $ret = ['flag' => 0, 'msg' => '添加失败'];
                }
            }

        }else{
            $ret = ['flag'=>0,'msg'=>$validate->getError()];
        }

        return $ret;
    }

    public function detail($id)
    {
        $data = $this->model->getDataById($id);
        $modelOrderUsed = new OrderUsed();
        $orders = $modelOrderUsed->getOrderUsedByOtid($id,3);
        $this->setOrderUsed($orders);
        $this->setUpdateParams($data['r_mid']);
        if(!empty($orders)){
            $this->assign('readonly',true);
        }
        $mimeType = false;
        if($data['r_attachment']){
            $mimeType = $this->getAttachmentMimeType($data['r_attachment']);
        }
        $this->assign('aMimeType',$mimeType);
        $this->assign('data',$data);
        $this->assign('orders',$orders);
        return $this->fetch();
    }

    public function update()
    {
        $post = $this->request->post();

        $arr = ['id','no','subject','content','date','accdate','money','type','coname','coid','mid','mname','used','noused'];

        foreach($arr as $i){
            $data['r_'.$i] = trim($post[$i]);
        }

        $data['r_updatetime'] = time();

        $validate = \validate('Received');

        if($validate->check($data)){

            if(!empty($data['r_date'])){
                $data['r_date'] = strtotime($data['r_date']);
            }

            $data['r_noused'] = floatval($data['r_money']) - floatval($data['r_used']);

            $file = $this->request->file('attachment');

            $res = $this->uploadFile($file);

            if($res['flag']===0){
                $ret = $res;
            }else {

                if($res['flag'] === 1){
                    $data['r_attachment'] = $res['name'];
                }
                $old = $this->model->getDataById($data['r_id']);
                if ($res = $this->model->save($data,$data['r_id'])) {
                    $logModel = new Logs();
                    $logModel->saveLogs($data,$old,$data['r_id'],'received');
                    $ret = ['flag' => 1, 'msg' => '更新成功'];
                    if(isset($data['r_attachment']) && $data['r_attachment']){
                        if($this->getAttachmentMimeType($data['r_attachment']) == 'image'){
                            $ret['image'] = $data['r_attachment'];
                        }else{
                            $ret['file'] = $data['r_attachment'];
                        }
                    }
                } else {
                    $ret = ['flag' => 0, 'msg' => '更新失败'];
                }
            }
        }else{
            $ret = ['flag'=>0,'msg'=>$validate->getError()];
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
            $res = $mOrderUsed->where('ou_type','=',3)->where('ou_otid','=',$id)->find();
            if($res){
                $failed[] = $id;
            }
            if($this->current->m_isAdmin != 1){
                $r = $this->model->where('r_id','=',$id)->find();
                if(empty($r) || $r->r_mid != $this->current->m_id)
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