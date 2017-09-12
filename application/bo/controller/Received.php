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
}