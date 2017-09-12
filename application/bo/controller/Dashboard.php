<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\model\Permissions;
use think\Request;

class Dashboard extends BoController
{
    public function index()
    {
        $permissionsModel = new Permissions();
        $this->assign("menuList", $permissionsModel->getList());
        return $this->fetch('index');
    }
}
