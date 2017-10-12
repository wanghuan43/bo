<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use think\Request;

class Budget extends BoController
{
    var $budgetModel;
    function __construct(Request $request)
    {
        parent::__construct($request);
        $this->budgetModel = new \app\bo\model\Budget();
    }

    public function template()
    {
        return $this->fetch("budget/template/index");
    }

    public function templateAdd()
    {
        return $this->fetch("budget/template/opt");
    }

    public function table()
    {
        return $this->fetch("budget/table/index");
    }
}