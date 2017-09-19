<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
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

    public function all()
    {
        $this->assign('empty','<tr><td colspan="9">没有数据</td></tr>');
        return parent::all();
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

}