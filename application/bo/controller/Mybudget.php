<?php

namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\libs\BudgetEntity;
use think\Request;

class Mybudget extends BoController
{
    var $budgetEntity;

    function __construct(Request $request)
    {
        parent::__construct($request);
        $this->budgetEntity = new BudgetEntity();
    }

    public function index()
    {
        $lists = $this->budgetEntity->getTableListByMidFromPermissions($this->current->m_id, $this->limit);
        $this->assign("lists", $lists);
        return $this->fetch("mybudget/index");
    }

    public function edit()
    {
        $id = Request::instance()->get("id", false);
        $table = $this->budgetEntity->getTableByID($id, true);
        $template = $this->budgetEntity->getTemplateByID($table->tid, false);
        $lists = $this->budgetEntity->getColsListByMidFromPermissions($this->current->m_id, $id, false);
        $this->assign("id", $id);
        $this->assign("table", $table);
        $this->assign("template", $template);
        $this->assign("lists", json_encode($lists));
        return $this->fetch("mybudget/edit");
    }

    public function doEdit()
    {
        $cols = Request::instance()->post("cols", false);
        $result = false;
        if ($cols) {
            $cols = json_decode(urldecode($cols), true);
            $result = $this->budgetEntity->saveColValue($cols);
        }
        if ($result) {
            return array("status" => "true", "message" => "保存成功");
        } else {
            return array("status" => "false", "message" => "保存失败");
        }
    }
}