<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\model\Member;
use think\Request;

class Dashboard extends BoController
{
    public function index()
    {
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
}
