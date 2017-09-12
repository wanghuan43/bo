<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use think\Request;

class Contract extends BoController
{
    function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function searchContract()
    {
        $contractModel = new \app\bo\model\Contract();
        $this->assign("type", "contract");
        return $this->search($contractModel);
    }
}