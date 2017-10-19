<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use think\Request;
use app\bo\libs\BudgetEntity;

class Budget extends BoController
{
    var $budgetEntity;
    function __construct(Request $request)
    {
        parent::__construct($request);
        $this->budgetEntity = new BudgetEntity();
    }

    public function template()
    {
        $lists = $this->budgetEntity->getTemplateList($this->limit);
        $this->assign("lists", $lists);
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

    public function addTemplate()
    {
        $post = Request::instance()->post();
        $result = $this->budgetEntity->saveTemplate($post);
        $return = ["status"=>$result, "message"=>($result ? "保存成功" : "保存失败")];
        return $return;
    }
}