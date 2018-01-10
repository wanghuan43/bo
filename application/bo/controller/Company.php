<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use think\Request;

class Company extends BoController
{
    function __construct(Request $request)
    {
        $this->model = new \app\bo\model\Company();
        parent::__construct($request);
    }

    public function searchCompany()
    {
        $companyModel = new \app\bo\model\Company();
        $this->assign("type", "company");
        return $this->search($companyModel);
    }

    public function searchCompanyNoList()
    {
        $companyModel = new \app\bo\model\Company();
        $this->assign("type", "company");
        $this->other = "main-pannel";
        return $this->search($companyModel);
    }

    protected function doAdd()
    {
        $post = $this->request->post();

        $arr = ['code','name','mnemonic_code','industry','address','tax_id','reg_id','lr',
            'internal_name','create_org','remark','bank','bank_card',
            'invoice_type','invoice_address','invoice_recipient','invoice_phone'];

        foreach( $arr as $item ){
            $data['co_'.$item] = trim($post[$item]);
        }

        $arr = ['type','internal_flag','flag','status'];

        foreach( $arr as $item ){
            $data['co_'.$item] = intval($post[$item]);
        }

        if( $data['co_type'] == 2 ){
            unset($data['co_flag']);
        }

        $data['co_create_time'] = time();

        $validate = validate('Company');

        if($validate->check($data)){
            try{
                $res = $this->model->save($data);
                if( $res ){
                    $ret = ['flag'=>1,'msg'=>'添加成功'];
                }else{
                    $ret = ['flag'=>0,'msg'=>'添加失败'];
                }
            }catch (\Exception $e){
               $ret = ['flag'=>0,'msg'=>'公司已存在'];
            }

        }else{
            $ret = ['flag'=>0,'msg'=>$validate->getError()];
        }

        return $ret;

    }

    public function all($trashed=2)
    {
        $type = $this->request->param('type');
        $titleName = $type == '2'?'客户':'供应商';
        $this->assign('titleName',$titleName);
        $this->assign('type',$type);
        $this->assign("stype", "company");
        $this->assign('formUrl','/company/all/type/'.$type);
        return parent::all($trashed);
    }

    public function detail($id)
    {
        $data = $this->model->getDataById($id);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function update(){
        $post = $this->request->post();
        $arr = ['code','name','mnemonic_code','industry','address','tax_id','reg_id','lr',
            'internal_name','create_org','remark','bank','bank_card',
            'invoice_type','invoice_address','invoice_recipient','invoice_phone'];

        foreach( $arr as $item ){
            $data['co_'.$item] = trim($post[$item]);
        }

        $arr = ['type','internal_flag','flag','status'];

        foreach( $arr as $item ){
            $data['co_'.$item] = intval($post[$item]);
        }

        if( $data['co_type'] == 2 ){
            unset($data['co_flag']);
        }

        $data['co_id'] = intval($post['id']);

        $validate = validate('Company');

        if($validate->check($data)){
            if( $res = $this->model->save($data,['co_id'=>$data['co_id']]) ){
                $ret = ['flag'=>1,'msg'=>'更新成功'];
            }else{
                $ret = ['flag'=>0,'msg'=>'更新失败'];
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

}