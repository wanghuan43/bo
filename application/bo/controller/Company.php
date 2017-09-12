<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use think\Request;

class Company extends BoController
{
    function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function searchCompany()
    {
        $companyModel = new \app\bo\model\Company();
        $this->assign("type", "company");
        return $this->search($companyModel);
    }
}