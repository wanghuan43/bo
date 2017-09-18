<?php
namespace app\bo\controller;

use app\bo\libs\BoController;

class Member extends BoController
{
    function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function searchCompany()
    {
        $memberModel = new \app\bo\model\Member();
        $this->assign("type", "member");
        return $this->search($memberModel);
    }
}