<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\model\OrderUsed;
use think\Request;

class Acceptance extends BoController
{
    public function __construct(Request $request)
    {
        $this->model = new \app\bo\model\Acceptance();
        parent::__construct($request);
    }

    public function searchAcceptance()
    {
        $acceptanceModel = new \app\bo\model\Acceptance();
        $this->assign("type", "acceptance");
        return $this->search($acceptanceModel, "common/popused");
    }

    protected function doAdd()
    {

        $post = $this->request->post();

        $data['a_no'] = trim($post['no']);
        $data['a_content'] = trim($post['content']);
        $data['a_type'] = $post['type'];
        $data['a_money'] = floatval(trim($post['money']));
        $data['a_used'] = floatval(trim($post['used']));
        $data['a_noused'] = floatval(trim($post['noused']))?:$data['a_money'];
        $data['a_date'] = strtotime($post['date']);
        $data['a_coid'] = trim($post['coid']);
        $data['a_coname'] = trim($post['coname']);

        $data['a_mid'] = $this->current->m_id;
        $data['a_mname'] = $this->current->m_name;

        $validate = validate('Acceptance');

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

        $data['a_no'] = $post['no'];
        $data['a_date'] = strtotime(trim($post['date']));
        $data['a_money'] = floatval(trim($post['money']));
        $data['a_type'] = intval($post['type']);
        $data['a_coname'] = trim($post['coname']);
        $data['a_coid'] = intval($post['coid']);
        $data['a_mname'] = trim($post['mname']);
        $data['a_mid'] = intval($post['mid']);
        $data['a_used'] = floatval($post['used']);
        $data['a_noused'] = floatval($post['noused']);

        if( empty($data['a_used']) && !!$post['oused'] ){
            $used = 0;
            foreach( $post['oused'] as $oused){
                $used += floatval($oused);
            }
            $data['a_used'] = $used;
            $data['a_noused'] = $data['a_money'] - $used;
        }

        $res = $this->model->save($data,['a_id'=>$id]);

        if($res){
            $ret = ['flag'=>1,'msg'=>'更新成功'];
        }else{
            $ret = ['flag'=>0,'msg'=>'更新失败'];
        }

        return $ret;

    }

}