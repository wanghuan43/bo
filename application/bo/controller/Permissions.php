<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\model\Member;
use app\bo\model\Menu;
use think\Request;

class Permissions extends BoController
{
    public function index()
    {
        $memberModel = new Member();
        $filters = Request::instance()->session('filtersPermissions', array());
        $params = Request::instance()->param();
        unset($params['page']);
        $params = array_merge($filters, $params);
        foreach ($params as $key => $value) {
            $memberModel->where($key, $value);
        }
        session("filtersPermissions", $params);
        $lists = $memberModel->paginate($this->limit);
        $this->assign("memberList", $lists);
        $this->assign("empty", '<tr><td colspan="4">无用户权限数据.</td></tr>');
        return $this->fetch("permissions/index");
    }

    public function setPermissions($mid)
    {
        $menuModel = new Menu();
        $menuList = $menuModel->getList();
        $this->assign("mid", $mid);
        $this->assign("menuList", $menuList);
        return $this->fetch("permissions/opt");
    }
}