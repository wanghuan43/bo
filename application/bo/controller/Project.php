<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use think\Request;
use think\Session;

class Project extends BoController
{
    public function searchProject()
    {
        $projectModel = new \app\bo\model\Project();
        $this->assign("type", "project");
        return $this->search($projectModel);
    }

}