<?php

namespace app\bo\controller;

use app\bo\libs\BoController;
use think\Request;

class Member extends BoController
{
    function __construct(Request $request)
    {
        $this->model = new \app\bo\model\Member();
        parent::__construct($request);
    }

    public function searchMember()
    {
        $memberModel = new \app\bo\model\Member();
        $this->assign("type", "member");
        return $this->search($memberModel);
    }

    protected function doAdd()
    {
        $post = $this->request->post();

        $arr = ['code', 'is_lead', 'name', 'email', 'phone', 'department', 'did', 'office', 'password', 'isAdmin'];

        foreach ($arr as $item) {
            $data['m_' . $item] = trim($post[$item]);
        }

        $validate = new \app\bo\validate\Member();

        if ($validate->check($data)) {
            if ($this->model->insert($data)) {
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
        $this->assign('data', $data);
        return $this->fetch();
    }

    public function update()
    {
        $post = $this->request->post();

        $arr = ['email', 'code', 'is_lead', 'name', 'phone', 'department', 'did', 'office', 'password', 'isAdmin'];

        foreach ($arr as $k) {
            $member['m_' . $k] = trim($post[$k]);
        }

        if(!$member['m_password']){
            unset($member['m_password']);
        }else{
            $member['m_password'] = encryptPassword($member['m_password']);
        }

        if ( $this->model->save($member, ['m_id'=>$post['id']]) ) {
            $ret = ['flag' => 1, 'msg' => '更新成功'];
        } else {
            $ret = ['flag' => 0, 'msg' => '更新失败'];
        }


        return $ret;

    }

}