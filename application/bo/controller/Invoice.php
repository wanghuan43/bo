<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\model\Invoice as ModelInvoice;
use app\bo\model\OrderUsed;
use think\Request;


class Invoice extends BoController
{

    public function __construct(Request $request)
    {
        $this->model = new ModelInvoice();
        parent::__construct($request);
    }

    public function searchInvoice()
    {
        $invoiceModel = new ModelInvoice();
        $this->assign("type", "invoice");
        return $this->search($invoiceModel, "common/popused");
    }

    public function doAdd()
    {
        $post = $this->request->post();

        $data['i_no'] = trim($post['no']);
        $data['i_content'] = trim($post['content']);
        $data['i_type'] = $post['type'];
        $data['i_money'] = floatval(trim($post['money']));
        $data['i_used'] = floatval(trim($post['used']));
        $data['i_noused'] = floatval(trim($post['noused']))?:$data['i_money'];
        $data['i_date'] = strtotime($post['date']);
        $data['i_coid'] = trim($post['coid']);
        $data['i_coname'] = trim($post['coname']);

        $data['i_mid'] = $this->current->m_id;
        $data['i_mname'] = $this->current->m_name;

        $validate = validate('Invoice');

        if($validate->check($data)){
            if( $res = $this->model->insert($data) ){
                $ret = ['flag'=>1,'msg'=>'添加成功'];
            }else{
                $ret = ['flag'=>0,'msg'=>'发生错误'];
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
        $this->assign('data',$data);
        $this->assign('orders',$orders);
        return $this->fetch();
    }

    public function update()
    {
        $post = $this->request->post();

        $id = $post['id'];

        $data['i_no'] = $post['no'];
        $data['i_date'] = strtotime(trim($post['date']));
        $data['i_money'] = floatval(trim($post['money']));
        $data['i_type'] = intval($post['type']);
        $data['i_coname'] = trim($post['coname']);
        $data['i_coid'] = intval($post['coid']);
        $data['i_mname'] = trim($post['mname']);
        $data['i_mid'] = intval($post['mid']);
        $data['i_used'] = floatval($post['used']);
        $data['i_noused'] = floatval($post['noused']);

        if( empty($data['i_used']) && !!$post['oused'] ){
            $used = 0;
            foreach( $post['oused'] as $oused){
                $used += floatval($oused);
            }
            $data['i_used'] = $used;
            $data['i_noused'] = $data['i_money'] - $used;
        }

        $res = $this->model->save($data,['i_id'=>$id]);

        if($res){
            $ret = ['flag'=>1,'msg'=>'更新成功'];
        }else{
            $ret = ['flag'=>0,'msg'=>'更新失败'];
        }

        return $ret;

    }

}