<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/9/26
 * Time: 下午2:24
 */

namespace app\bo\controller;


use app\bo\libs\BoController;
use app\bo\model\Member;
use think\Request;

class Department extends BoController
{

    public function __construct(Request $request)
    {
        $this->model = new \app\bo\model\Department();
        parent::__construct($request);
    }

    public function searchDepartment()
    {
        $this->assign("type", "department");
        return $this->search($this->model);
    }

    protected function doAdd()
    {

        $post = $this->request->post();
        $data['d_name'] = trim($post['name']);
        $data['d_code'] = trim($post['code']);
        $data['m_name'] = trim($post['mname']);
        $data['m_code'] = trim($post['mcode']);
        $data['d_cname'] = trim($post['cname']);

        $validate = new \app\bo\validate\Department();

        if ($validate->check($data)) {
            if ($this->model->save($data)) {
                if(!empty($data['m_code'])) {
                    Member::update(['m_is_lead'=>1],['m_code'=>$data['m_code']]);
                }
                $ret = ['flag' => 1, 'msg' => '添加成功'];
            } else {
                $ret = ['flag' => 0, 'msg' => '添加失败'];
            }
        } else {
            $ret = ['flag' => 0, 'msg' => $validate->getError()];
        }

        return $ret;

    }

    public function detail($id)
    {
        $data = $this->model->getDataById($id);
        $isAdmin = $this->current->m_isAdmin==1?true:false;
        $this->assign('isAdmin',$isAdmin);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function update()
    {
        $post = $this->request->post();

        $data = [
            'd_id' => $post['id'],
            'd_name' => trim($post['name']),
            'd_code' => trim($post['code']),
            'm_name' => trim($post['mname']),
            'm_code' => trim($post['mcode']),
            'd_cname' => trim($post['cname'])
        ];

        $validate = validate('Department');

        if($validate->check($data)) {
            $oldMemCode = trim($post['o-mcode']);
            if ($this->model->save($data, ['d_id' => $post['id']])) {
                if (!empty($data['m_code'])) {
                    if ($oldMemCode != $data['m_code']) {
                        if (!!$oldMemCode) Member::update(['m_is_lead' => 0], ['m_code' => $oldMemCode]);
                        if (!!$data['m_code']) Member::update(['m_is_lead' => 1], ['m_code' => $data['m_code']]);
                    }
                }
                $ret = ['flag' => 1, 'msg' => '更新成功'];
            } else {
                $ret = ['flag' => 0, 'msg' => '更新失败'];
            }
        }else{
            $ret = ['flag'=>0 , 'msg' => $validate->getError()];
        }

        return $ret;
    }

    public function del()
    {
        $ids = $this->request->post('ids/a');
        $list = $this->model->whereIn($this->model->getPk(),$ids)->select();
        $mCodes = [];
        $ret = parent::del();
        if($ret['flag']==1){ //删除成功
            foreach ($list as $i){
                $mCodes[] = $i->m_code;
            }
            Member::update(['m_is_lead'=>0],['m_code'=>['in',$mCodes]]);
        }
        return $ret;
    }

    public function export()
    {
        return $this->doExport();
    }

}