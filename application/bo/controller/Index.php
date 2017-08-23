<?php
namespace app\bo\controller;

use think\Controller;
use think\Loader;

class Index extends Controller{
    public function index(){
        $taglib = Loader::model("taglib");
        $this->view->assign("taglib", $taglib->get(1));
        $this->view->assign("test", "这是测试");
        return $this->fetch("index");
    }
}