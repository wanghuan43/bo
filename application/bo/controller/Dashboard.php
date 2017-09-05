<?php
namespace app\bo\controller;

use app\bo\model\Menu;
use app\bo\model\Permissions;
use think\Controller;

class Dashboard extends Controller
{

    public function index()
    {
        $permissionsModel = new Permissions();
        $this->assign("menuList", $permissionsModel->getMenuList());
        return $this->fetch('index');
    }

}
