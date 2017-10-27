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

        $arr = ['code','name','mnemonic_code','industry','address','tax_id','reg_id','lr','internal_name','create_org','remark'];

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

    public function all()
    {
        $type = $this->request->param('type');
        $list = $this->model->where('co_type','=',$type)->paginate($this->limit);
        $titleName = $type == '2'?'客户':'供应商';
        $this->assign('lists', $list);
        $this->assign('titleName',$titleName);
        $this->assign('type',$type);
        $this->assign("stype", "company");
        return $this->fetch('all');
    }

    public function detail($id)
    {
        $data = $this->model->getDataById($id);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function export()
    {
        return $this->doExport();
    }

}