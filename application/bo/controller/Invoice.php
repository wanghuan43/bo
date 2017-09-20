<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\model\Invoice as ModelInvoice;
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

    public function all()
    {
        $this->assign('empty','<tr><td colspan="10">没有数据</td></tr>');
        return parent::all();
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

}