<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\libs\ICome;
use app\bo\model\Member;
use think\Request;

class Dashboard extends BoController
{
    public function index()
    {
        $icom = new ICome();
        $this->assign('icomJs', $icom->getJsAddress());
        $this->assign("member", $this->current);
        $this->assign("menuList", $this->current->menu);
        return $this->fetch('index');
    }

    public function login()
    {
        if (Request::instance()->isPost()) {
            $post = Request::instance()->post();
            $model = new Member();
            $member = $model->loginMember($post);
            if (empty($member['code'])) {
                session("mid", $member['member']);
                $this->success('登录成功', '/');
            } else {
                $this->error("请确认您的用户名或者密码是否正确");
            }
        }
        return $this->fetch("common/login");
    }

    public function logout()
    {
        session("mid", null);
        $this->success('退出成功', '/dashboard/login');
    }

    public function changePassword()
    {
        if($this->request->isPost()){
            $post = $this->request->post();
            $p0 = isset($post['password0'])?trim($post['password0']):'123123';
            $p1 = trim($post['password']);
            $p2 = trim($post['password2']);
            if(encryptPassword($p0) != $this->current->m_password) {
                $this->error("原密码不正确");
            }elseif(empty($p1)){
                $this->error("新密码不能为空");
            }elseif(strlen($p1)<6){
                $this->error('密码不能少于6位');
            }elseif($p1 == '123123'){
                $this->error('新密码不合要求');
            }elseif($p0 == $p1){
                $this->error('新密码不能和原密码一样');
            }elseif($p1 != $p2){
                $this->error('两次输入的密码不一致');
            }else{
                $model = new Member();
                $res = $model->save(['m_password'=>encryptPassword($p1),'m_id'=>$this->current->m_id],['m_id'=>$this->current->m_id]);
                if($res){
                    $member = $model->where('m_id','=',$this->current->m_id)->find();
                    $member->menu = session('mid')->menu;
                    session('mid',$member);
                    $this->success('密码修改成功','/');
                }else{
                    $this->error($model->getError());
                }
            }
        }else{
            $needOldPwd = true;
            if($this->current->m_password == encryptPassword('123123')){
                $needOldPwd = false;
            }
            $this->assign('email',$this->current->m_email);
            $this->assign('needOldPwd',$needOldPwd);
            return $this->fetch("common/changepassword");
        }

    }

}
