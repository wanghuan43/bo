<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use think\Request;

class Contract extends BoController
{

    function __construct(Request $request)
    {
        $this->model = new \app\bo\model\Contract();
        parent::__construct($request);
    }

    public function searchContract()
    {
        $contractModel = new \app\bo\model\Contract();
        $this->assign("type", "contract");
        return $this->search($contractModel);
    }

    public function all()
    {
        $this->assign('empty','<tr><td colspan="10"></td></tr>');
        return parent::all();
    }

    protected function doAdd()
    {

        $post = $this->request->post();

        $data['c_no'] = trim($post['no']);
        $data['c_name'] = trim($post['name']);
        $data['c_bakup'] = trim($post['bakup']);
        $data['c_type'] = $post['type'];
        $data['c_money'] = floatval(trim($post['money']));
        $data['c_used'] = floatval(trim($post['used']));
        $data['c_noused'] = floatval(trim($post['noused']))?:$data['c_money'];
        $data['c_date'] = strtotime($post['date']);
        $data['c_coid'] = trim($post['coid']);
        $data['c_coname'] = trim($post['coname']);
        $data['c_pid'] = $post['pid'];
        $data['c_pname'] = $post['pname'];

        $data['c_mid'] = $this->current->m_id;
        $data['c_mname'] = $this->current->m_name;

        $validate = validate('Contract');

        if($validate->check($data)){
            if( $res = $this->model->insert($data) ){
                $ret = ['flag'=>1,'msg'=>'添加成功'];
            }else{
                $ret = ['flag'=>0,'msg'=>'添加失败'];
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
        $this->assign('data',$data);
        $this->assign('orders',$orders);
        return $this->fetch();
    }

    public function update()
    {
        $post = $this->request->post();

        $id = $post['id'];

        $data['c_no'] = $post['no'];
        $data['c_date'] = strtotime(trim($post['date']));
        $data['c_money'] = floatval(trim($post['money']));
        $data['c_name'] = trim($post['name']);
        $data['c_type'] = intval($post['type']);
        $data['c_coname'] = trim($post['coname']);
        $data['c_coid'] = intval($post['coid']);
        $data['c_mname'] = trim($post['mname']);
        $data['c_mid'] = intval($post['mid']);
        $data['c_used'] = floatval($post['used']);
        $data['c_noused'] = floatval($post['noused']);

        if( empty($data['c_used']) && !!$post['oids'] ){
            $modelOrders = new \app\bo\model\Orders();
            $orders = $modelOrders->getOrdersByContractId($id);
            $used = 0;
            foreach( $orders as $order){
                $used += floatval($order->o_money);
            }
            $data['c_used'] = $used;
            $data['c_noused'] = $data['c_money'] - $used;
        }

        $res = $this->model->save($data,['c_id'=>$id]);

        if($res){
            $ret = ['flag'=>1,'msg'=>'更新成功'];
        }else{
            $ret = ['flag'=>0,'msg'=>'更新失败'];
        }

        return $ret;

    }

}