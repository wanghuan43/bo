<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use think\Request;

class Member extends BoController
{
    function __construct(Request $request)
    {
        $this->model = new \app\bo\model\Member();
        parent::__construct($request);
    }

    public function searchMember()
    {
        $memberModel = new \app\bo\model\Member();
        $this->assign("type", "member");
        return $this->search($memberModel);
    }
}