<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use think\Request;

class Received extends BoController
{
    public function searchReceived()
    {
        $receivedModel = new \app\bo\model\Received();
        $this->assign("type", "received");
        return $this->search($receivedModel, "common/popused");
    }

    public function add()
    {
        return '<h2>新建付款单</h2>';
    }

    public function all()
    {
        return '<h2>所有付款单</h2>';
    }
}