<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\model\Logs;
use app\bo\model\Orders;
use app\bo\model\OrderUsed;
use app\bo\model\Project as ModelProject;
use think\File;
use think\Request;


class Project extends BoController
{

    public function __construct(Request $request)
    {
        $this->model = new ModelProject();
        parent::__construct($request);
    }

    public function searchProject()
    {
        $this->assign("type", "project");
        return $this->search($this->model);
    }

    public function searchProjectNoList()
    {
        $this->assign("type", "project");
        $this->other = "main-pannel";
        return $this->search($this->model);
    }

    public function all()
    {
        $this->assign('empty','<tr><td colspan="3">没有数据</td></tr>');
        $this->assign('stype','project');
        $this->assign('title','所有项目');
        return parent::all();
    }

    /**
     * 添加
     * @return array
     */
    protected function doAdd()
    {
        $post = $this->request->post();
        $no = strtoupper(trim($this->request->post('no')));
        $name = trim($this->request->post('name'));

        $validate = validate('Project');

        $data = [
            'p_no'=>$no,
            'p_name'=>$name,
            'p_date' => trim($post['date']),
            'p_did' => $post['did'],
            'p_dname' => $post['dname'],
            'p_income' => trim($post['income']),
            'p_pay' => trim($post['pay']),
            'p_mname'=>$this->current->m_name,
            'p_mid'=>$this->current->m_id,
            'p_content' => trim($post['content']),
            'p_createtime'=>time(),
            'p_updatetime'=>time()
        ];

        if( $validate->check($data) ) {

            if(!empty($data['p_date'])){
                $data['p_date'] = strtotime($data['p_date']);
            }

            $file = $this->request->file('attachment');

            $res = $this->uploadFile($file);

            if($res['flag']===0){
                $ret = $res;
            }else {

                if($res['flag'] === 1){
                    $data['p_attachment'] = $res['name'];
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
        $mOrders = new Orders();
        $orders = $mOrders->where('o_pid','=',$id)->select();
        $this->setOrderUsed($orders);
        $this->setUpdateParams($data['p_mid']);
        if(!empty($orders)){
            $this->assign('readonly','true');
        }
        $mimeType = false;
        if($data['p_attachment']){
            $mimeType = $this->getAttachmentMimeType($data['p_attachment']);
        }
        $this->assign('aMimeType',$mimeType);
        $this->assign('orders',$orders);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function update()
    {

        $post = $this->request->post();

        $id = $post['id'];

        $data = [
            'p_id' => $id,
            'p_no' => strtoupper(trim($post['no'])),
            'p_name' => trim($post['name']),
            'p_mname' => trim($post['mname']),
            'p_mid' => $post['mid'],
            'p_did' => $post['did'],
            'p_dname' => $post['dname'],
            'p_income' => trim($post['income']),
            'p_pay' => trim($post['pay']),
            'p_updatetime' => time(),
            'p_date' => trim($post['date']),
            'p_content' => trim($post['content'])
        ];

        $validate = validate('Project');

        if($validate->check($data)) {
            if(!empty($data['p_date'])){
                $data['p_date'] = strtotime($data['p_date']);
            }

            $file = $this->request->file('attachment');

            $res = $this->uploadFile($file);

            if($res['flag']===0){
                $ret = $res;
            }else {

                if($res['flag'] === 1){
                    $data['p_attachment'] = $res['name'];
                }
                $old = $this->model->getDataById($data['p_id']);
                if ($res = $this->model->save($data,$data['p_id'])) {
                    $logModel = new Logs();
                    $logModel->saveLogs($data,$old,$data['p_id'],'project');
                    $ret = ['flag' => 1, 'msg' => '更新成功'];
                    if(isset($data['p_attachment']) && $data['p_attachment']){
                        if($this->getAttachmentMimeType($data['p_attachment']) == 'image'){
                            $ret['image'] = $data['p_attachment'];
                        }else{
                            $ret['file'] = $data['p_attachment'];
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

        $mOrders = new Orders();

        foreach ($ids as $id){
            $res = $mOrders->where('o_pid','=',$id)->find();
            if($res){
                $failed[] = $id;
            }
            if($this->current->m_isAdmin != 1){
                $p = $this->model->where('p_id','=',$id)->find();
                if(empty($p) || $p->p_mid != $this->current->m_id)
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
        $project = $this->model->where('p_id','=',$id)->find();
        $mOrders = new Orders();
        $mOrderUsed = new OrderUsed();
        $orders = $mOrders->where('o_pid','=',$id)->order('o_date','desc')->select();
        $total = [
            1 => [
                'plan' => 0, //计划
                'contract' => 0, //签约
                'received' => 0, //交付
                'invoice' => 0, //开票
                'acceptance' => 0 //收款
            ],
            2 => [
                'plan' => 0,
                'contract' => 0,
                'received' => 0,
                'invoice' => 0,
                'acceptance' => 0
            ],
            'pLeftIncome' => $project->p_income,
            'pProfit' => 0
        ];
        $oIncome = 0;
        $oPay = 0;
        foreach ($orders as $order){
            $ous = $mOrderUsed->where('ou_oid','=',$order->o_id)->select();

            $total[$order->o_type]['plan'] += $order->o_money;
            if( !!$order->o_cid ){
                $total[$order->o_type]['contract'] += $order->o_money;
            }
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
            $total[$order->o_type]['received'] += $r;
            $total[$order->o_type]['invoice'] += $i;
            $total[$order->o_type]['acceptance'] += $a;
            $total['orders'][$order->o_id] = [
                'received' => $order->o_money - $r,
                'invoice' => $order->o_money - $i,
                'acceptance' => $order->o_money - $a
            ];
            if($order->o_type == 1){
                $total['pLeftIncome'] -= $order->o_money;
                $oIncome  += $order->o_money/(1+intval(getTaxList($order->o_tax))/100);
            }elseif ($order->o_type == 2){
                $oPay += $order->o_money/(1+intval(getTaxList($order->o_tax))/100);
            }
        }
        $total['pProfit'] = $oIncome-$oPay;
        $this->assign('total',$total);
        $this->assign('project',$project);
        $this->assign('orders',$orders);
        return $this->fetch();
    }

}