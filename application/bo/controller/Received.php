<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
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
        $this->assign("type", "received");
        return $this->search($receivedModel, "common/popused");
    }

    public function doAdd()
    {
        $post = $this->request->post();

        $data['r_no'] = trim($post['no']);
        $data['r_type'] = $post['type'];
        $data['r_money'] = floatval(trim($post['money']));
        $data['r_used'] = floatval(trim($post['used']));
        $data['r_noused'] = floatval(trim($post['noused']))?:$data['r_money'];
        $data['r_date'] = strtotime($post['date']);
        $data['r_coid'] = trim($post['coid']);
        $data['r_coname'] = trim($post['coname']);

        $data['r_mid'] = $this->current->m_id;
        $data['r_mname'] = $this->current->m_name;

        $validate = validate('Received');

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

        $data['r_no'] = $post['no'];
        $data['r_date'] = strtotime(trim($post['date']));
        $data['r_money'] = floatval(trim($post['money']));
        $data['r_type'] = intval($post['type']);
        $data['r_coname'] = trim($post['coname']);
        $data['r_coid'] = intval($post['coid']);
        $data['r_mname'] = trim($post['mname']);
        $data['r_mid'] = intval($post['mid']);
        $data['r_used'] = floatval($post['used']);
        $data['r_noused'] = floatval($post['noused']);

        if( empty($data['r_used']) && !!$post['oused'] ){
            $used = 0;
            foreach( $post['oused'] as $oused){
                $used += floatval($oused);
            }
            $data['r_used'] = $used;
            $data['r_noused'] = $data['r_money'] - $used;
        }

        $res = $this->model->save($data,['r_id'=>$id]);

        if($res){
            $ret = ['flag'=>1,'msg'=>'更新成功'];
        }else{
            $ret = ['flag'=>0,'msg'=>'更新失败'];
        }

        return $ret;

    }

}