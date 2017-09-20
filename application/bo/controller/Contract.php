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
        $this->assign('data',$data);
        return $this->fetch();
    }

}