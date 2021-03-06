<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\model\Logs;
use app\bo\model\Orders;
use app\bo\model\OrderUsed;
use think\Request;

class Contract extends BoController
{

    function __construct(Request $request)
    {
        $this->model = new \app\bo\model\Contract();
        parent::__construct($request);
    }

    public function all($trashed=2)
    {
        $this->assign("stype", "contract");
        return parent::all($trashed); // TODO: Change the autogenerated stub
    }

    public function searchContract()
    {
        $contractModel = new \app\bo\model\Contract();
        $contractModel->where("c_noused", "<>", "0");
        $this->assign("type", "contract");
        return $this->search($contractModel);
    }

    public function searchContractNoList()
    {
        $contractModel = new \app\bo\model\Contract();
        $this->assign("type", "contract");
        $this->other = "main-pannel";
        return $this->search($contractModel);
    }

    protected function doAdd()
    {

        $post = $this->request->post();

        $data['c_no'] = trim($post['no']);
        $data['c_name'] = trim($post['name']);
        $data['c_bakup'] = trim($post['bakup']);
        $data['c_type'] = $post['type'];
        $data['c_money'] = trim($post['money']);
        $data['c_date'] = $post['date'];
        $data['c_coid'] = trim($post['coid']);
        $data['c_coname'] = trim($post['coname']);
        $data['c_accdate'] = trim($post['accdate']);
        if(isset($post['pid']))
            $data['c_pid'] = $post['pid'];
        else
            $data['c_pid'] = 0;
        if(isset($post['pname']))
            $data['c_pname'] = $post['pname'];

        $data['c_mid'] = $this->current->m_id;
        $data['c_mname'] = $this->current->m_name;
        $data['c_did'] = $this->current->m_did;
        $data['c_dname'] = $this->current->m_department;

        $data['c_createtime'] = $data['c_updatetime'] = time();

        $validate = validate('Contract');

        if($validate->check($data)){
            $data['c_money'] = $data['c_noused'] = floatval($data['c_money']);
            $data['c_date'] = strtotime($data['c_date']);
            if(empty($data['c_accdate'])){
                $data['c_accdate'] = date('ym',$data['c_date']);
            }

            $file = $this->request->file('attachment');

            $res = $this->uploadFile($file);

            if($res['flag']===0){
                $ret = $res;
            }else {

                if($res['flag'] === 1){
                    $data['c_attachment'] = $res['name'];
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
        $modelOrders = new \app\bo\model\Orders();
        $orders = $modelOrders->getOrdersByContractId($id);
        $this->setOrderUsed($orders);
        $this->setUpdateParams($data['c_mid']);
        if(!empty($orders)){
            $this->assign('readonly',true);
        }
        $mimeType = false;
        if($data['c_attachment']){
            $mimeType = $this->getAttachmentMimeType($data['c_attachment']);
        }
        $this->assign('aMimeType',$mimeType);
        $this->assign('data',$data);
        $this->assign('orders',$orders);
        return $this->fetch();
    }

    public function update()
    {
        $post = $this->request->post();

        $arr = ['id','no','date','accdate','money','used','noused','name','type','coname','coid','mname','mid','bakup'];

        foreach( $arr as $i ){
            $data['c_'.$i] = trim($post[$i]);
        }
        $data['c_updatetime'] = time();

        $validate = \validate('Contract');

        if($validate->check($data)){

            $data['c_date'] = strtotime($data['c_date']);

            $data['c_noused'] = floatval($data['c_money']) - floatval($data['c_used']);

            $file = $this->request->file('attachment');

            $res = $this->uploadFile($file);

            if($res['flag']===0){
                $ret = $res;
            }else {

                if($res['flag'] === 1){
                    $data['c_attachment'] = $res['name'];
                }
                $old = $this->model->getDataById($data['c_id']);
                if ($res = $this->model->save($data,$data['c_id'])) {
                    $logModel = new Logs();
                    $logModel->saveLogs($data,$old,$data['c_id'],'contract');
                    $ret = ['flag' => 1, 'msg' => '更新成功'];
                    if(isset($data['c_attachment']) && $data['c_attachment']){
                        if($this->getAttachmentMimeType($data['c_attachment']) == 'image'){
                            $ret['image'] = $data['c_attachment'];
                        }else{
                            $ret['file'] = $data['c_attachment'];
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

        $mOrders = new \app\bo\model\Orders();

        foreach ($ids as $id){
            $res = $mOrders->where('o_cid','=',$id)->find();
            if($res){
                $failed[] = $id;
            }
            if($this->current->m_isAdmin != 1){
                $c = $this->model->where('c_id','=',$id)->find();
                if(empty($c) || $c->c_mid != $this->current->m_id)
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

    public function statistics($id)
    {
        $contract = $this->model->where('c_id','=',$id)->find();
        $mOrders = new Orders();
        $mOrderUsed = new OrderUsed();
        $orders = $mOrders->where('o_cid','=',$id)->order('o_date','desc')->select();
        $total = [
                'received' => 0,
                'invoice' => 0,
                'acceptance' => 0
        ];

        foreach ($orders as $order){
            $ous = $mOrderUsed->where('ou_oid','=',$order->o_id)->select();
            $r = 0;
            $i = 0;
            $a = 0;
            if(!!$ous){
                foreach ($ous as $ou){
                    if($ou->ou_type == 1){
                        $i += $ou->ou_used;
                    }elseif ($ou->ou_type == 2){
                        $a += $ou->ou_used;
                    }elseif($ou->ou_type == 3){
                        $r += $ou->ou_used;
                    }
                }
            }
            $total['received'] += $r;
            $total['invoice'] += $i;
            $total['acceptance'] += $a;
            $total['orders'][$order->o_id] = [
                'received' => $order->o_money - $r,
                'invoice' => $order->o_money - $i,
                'acceptance' => $order->o_money - $a
            ];
        }
        $this->assign('total',$total);
        $this->assign('contract',$contract);
        $this->assign('orders',$orders);
        return $this->fetch();
    }

}