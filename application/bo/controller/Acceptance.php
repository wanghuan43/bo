<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
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

    public function all()
    {
        $this->assign('empty','<tr><td colspan="10">没有数据</td></tr>');
        return parent::all();
    }

    protected function doAdd()
    {

        $post = $this->request->post();

        $data['a_no'] = trim($post['no']);
        $data['a_content'] = trim($post['content']);
        $data['a_type'] = $post['type'];
        $data['a_money'] = floatval(trim($post['money']));
        $data['a_used'] = floatval(trim($post['used']));
        $data['a_noused'] = floatval(trim($post['noused']));
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
                $ret = ['flag'=>0,'msg'=>'发生错误'];
            }
        }else{
            $ret = ['flag'=>0,'msg'=>$validate->getError()];
        }

        return $ret;

    }

}