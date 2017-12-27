<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\model\Invoice as ModelInvoice;
use app\bo\model\Logs;
use app\bo\model\OrderUsed;
use think\Request;


class Invoice extends BoController
{

    public function __construct(Request $request)
    {
        $this->model = new ModelInvoice();
        parent::__construct($request);
    }

    public function all()
    {
        $this->assign("stype", "invoice");
        return parent::all(); // TODO: Change the autogenerated stub
    }

    public function searchInvoice()
    {
        $invoiceModel = new ModelInvoice();
        $invoiceModel->where("i.i_noused", "<>", "0");
        $this->assign("type", "invoice");
        return $this->search($invoiceModel, "common/popused");
    }

    public function searchInvoiceNoList()
    {
        $invoiceModel = new ModelInvoice();
        $this->assign("type", "invoice");
        $this->other = "main-pannel";
        return $this->search($invoiceModel);
    }

    public function doAdd()
    {
        $post = $this->request->post();

        $data['i_no'] = trim($post['no']);
        $data['i_content'] = trim($post['content']);
        $data['i_type'] = $post['type'];
        $data['i_money'] = trim($post['money']);
        $data['i_tax'] = trim($post['tax']);
        $data['i_date'] = trim($post['date']);
        $data['i_coid'] = trim($post['coid']);
        $data['i_coname'] = trim($post['coname']);
        $data['i_subject'] = trim($post['subject']);
        $data['i_accdate'] = trim($post['accdate']);
        $data['i_mid'] = $this->current->m_id;
        $data['i_mname'] = $this->current->m_name;
        $data['i_did'] = $this->current->m_did;
        $data['i_dname'] = $this->current->m_department;
        $data['i_createtime'] = $data['i_updatetime'] = time();

        $validate = validate('Invoice');

        if($validate->check($data)){

            $data['i_date'] = strtotime($data['i_date']);

            if(empty($data['i_accdate'])){
                $data['i_accdate'] = date('ym',$data['i_date']);
            }

            $data['i_money'] = $data['i_noused'] = floatval($data['i_money']);

            $file = $this->request->file('attachment');

            $res = $this->uploadFile($file);

            if($res['flag']===0){
                $ret = $res;
            }else {

                if($res['flag'] === 1){
                    $data['i_attachment'] = $res['name'];
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
        $orders = $modelOrderUsed->getOrderUsedByOtid($id,1);
        $this->setOrderUsed($orders);
        $this->setUpdateParams($data['i_mid']);
        if(!empty($orders)){
            $this->assign('readonly',true);
        }
        $mimeType = false;
        if($data['i_attachment']){
            $mimeType = $this->getAttachmentMimeType($data['i_attachment']);
        }
        $this->assign('aMimeType',$mimeType);
        $this->assign('data',$data);
        $this->assign('orders',$orders);
        return $this->fetch();
    }

    public function update()
    {
        $post = $this->request->post();

        $arr = ['id','no','subject','content','date','tax','accdate','money','type','coname','coid','mid','mname','used','noused'];

        foreach($arr as $i){
            $data['i_'.$i] = trim($post[$i]);
        }

        $data['i_updatetime'] = time();

        $validate = \validate('Invoice');

        if($validate->check($data)){

            if(!empty($data['i_date'])){
                $data['i_date'] = strtotime($data['i_date']);
            }

            $data['i_noused'] = floatval($data['i_money']) - floatval($data['i_used']);

            $file = $this->request->file('attachment');

            $res = $this->uploadFile($file);

            if($res['flag']===0){
                $ret = $res;
            }else {
                if($res['flag'] === 1){
                    $data['i_attachment'] = $res['name'];
                }
                $old = $this->model->getDataById($data['i_id']);
                if ($res = $this->model->save($data,$data['i_id'])) {
                    $logModel = new Logs();
                    $logModel->saveLogs($data,$old,$data['i_id'],'invoice');
                    $ret = ['flag' => 1, 'msg' => '更新成功'];
                    if(isset($data['i_attachment']) && $data['i_attachment']){
                        if($this->getAttachmentMimeType($data['i_attachment']) == 'image'){
                            $ret['image'] = $data['i_attachment'];
                        }else{
                            $ret['file'] = $data['i_attachment'];
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
            $res = $mOrderUsed->where('ou_type','=',1)->where('ou_otid','=',$id)->find();
            if($res){
                $failed[] = $id;
            }
            if($this->current->m_isAdmin != 1){
                $i = $this->model->where('i_id','=',$id)->find();
                if(empty($i) || $i->i_mid != $this->current->m_id)
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