<?php

namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\model\BudgetTemplate;
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
        $tid = Request::instance()->get("tid", 0);
        $filter = false;
        if (!empty($tid)) {
            $filter['t_id'] = ["op" => "<>", "val" => $tid];
        }
        $lists = $this->budgetEntity->getTemplateList(false, $filter);
        $this->assign("lists", $lists);
        $template = $this->budgetEntity->getTemplateByID($tid, true);
        $this->assign("template", $template);
        return $this->fetch("budget/template/opt");
    }

    public function getTemplateTable()
    {
        $tid = Request::instance()->get("tid", false);
        $return = ["status" => false, "message" => "模板加载失败，请选择其他模板"];
        if ($tid) {
            $template = $this->budgetEntity->getTemplateByID($tid, true);
            $return = ["status"=> false, "message" => $template];
        }
        return $return;
    }

    public function table()
    {
        $lists = $this->budgetEntity->getTableList($this->limit);
        $this->assign("lists", $lists);
        return $this->fetch("budget/table/index");
    }

    public function tableAdd()
    {
        $model = new \app\bo\model\Department();
        $id = Request::instance()->get("id", 0);
        $lists = $this->budgetEntity->getTemplateList(false);
        $this->assign("lists", $lists);
        $table = $this->budgetEntity->getTableByID($id, true);
        $this->assign("table", $table);
        $this->assign("departments", $model->all());
        $this->assign("memberList", \app\bo\model\Member::all());
        return $this->fetch("budget/table/opt");
    }

    public function addTemplate()
    {
        $post = Request::instance()->post();
        $result = $this->budgetEntity->saveTemplate($post);
        $return = ["status" => $result, "message" => ($result ? "保存成功" : "保存失败")];
        return $return;
    }
}