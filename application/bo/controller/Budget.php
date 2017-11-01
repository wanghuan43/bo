<?php

namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\model\BudgetColumn;
use app\bo\model\BudgetTable;
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
        $template = $this->budgetEntity->getTemplateByID($tid, true);

        $this->assign("tid", $tid);
        $this->assign("lists", $lists);
        $this->assign("template", $template);
        return $this->fetch("budget/template/opt");
    }

    public function addTemplate()
    {
        $post = Request::instance()->post();
        $result = $this->budgetEntity->saveTemplate($post);
        $return = ["status" => $result, "message" => ($result ? "保存成功" : "保存失败")];
        return $return;
    }

    public function table()
    {
        $lists = $this->budgetEntity->getTableList($this->limit);
        $tlists = $this->budgetEntity->getTemplateList(false);
        $this->assign("memberList", \app\bo\model\Member::all());
        $this->assign("lists", $lists);
        $this->assign("tlists", $tlists);
        return $this->fetch("budget/table/index");
    }

    public function tableAdd()
    {
        $model = new \app\bo\model\Department();
        $id = Request::instance()->get("id", 0);
        $lists = $this->budgetEntity->getTemplateList(false);
        $table = $this->budgetEntity->getTableByID($id);
        $tpcr = $this->budgetEntity->getPermissionsByTable($id);
        $tmp = [];
        foreach ($tpcr as $value) {
            $t = $value['rw'] == 1 ? "read" : "other";
            $tmp[$t][] = $value;
        }
        $this->assign("lists", $lists);
        $this->assign("table", $table);
        $this->assign("tpcr", json_encode($tmp));
        $this->assign("departments", $model->all());
        $this->assign("memberList", \app\bo\model\Member::all());
        $this->assign("tableID", $id);
        return $this->fetch("budget/table/opt");
    }

    public function addTable()
    {
        $post = Request::instance()->post();
        $result = $this->budgetEntity->saveTable($post);
        $return = ["status" => $result, "message" => ($result ? "保存成功" : "保存失败")];
        return $return;
    }

    public function getTemplateTable()
    {
        $tid = Request::instance()->get("tid", false);
        $tableID = Request::instance()->get("tableID", 0);
        $return = ["status" => false, "message" => "模板加载失败，请选择其他模板"];
        $template = false;
        if ($tid) {
            if ($tableID) {
                $template = $this->budgetEntity->getTableByID($tableID, true);
            }
            $tmp = $this->budgetEntity->getTemplateByID($tid, true);
            if($template){
                $template->t_row = $tmp->t_row;
                $template->t_col = $tmp->t_col;
            }else{
                $template = $tmp;
            }
            $return = ["status" => true, "message" => $template];
        }
        return $return;
    }

    public function getTableByTemplate()
    {
        $tid = Request::instance()->get("tid", false);
        $return = ["status" => false, "message" => "模板加载失败，请选择其他模板"];
        if ($tid) {
            $template = $this->budgetEntity->getTableByTemplate($tid);
            $return = ["status" => true, "message" => $template];
        }
        return $return;
    }

    public function tablePermissions()
    {
        $pcr = Request::instance()->post("pcr", false);
        $return = ["status" => false, "message" => "参数有问题"];
        if ($pcr) {
            $pcr = json_decode(urldecode($pcr), true);
            $lists = [];
            foreach ($pcr as $value) {
                $tmp = [
                    "tid" => $value['tid'],
                    "mid" => $value['mid'],
                    "cid" => $value['cid'],
                    "rw" => $value['rw'],
                ];
                $lists[$value['tid']][] = $tmp;
            }
            foreach ($lists as $key => $value) {
                $this->budgetEntity->saveTablePermissions($value, $key);
            }
            $return = ["status" => true, "message" => "保存成功"];
        }
        return $return;
    }

    public function export($type='template',$id=false)
    {


        $model = new BudgetColumn();
        $isTemplate = $type == 'template'?1:0;
        if($isTemplate==1){
            $tplModel = new BudgetTemplate();
            $res = $tplModel->where($tplModel->getPk(),'=',$id)->find();
            $title = $res->t_title;
        }else{
            $tblModel = new BudgetTable();
            $res = $tblModel->where($tblModel->getPk(),'=',$id)->find();
            $title = $res->title;
        }
        $res = $model->where('c_isTemplate','=',$isTemplate)->where('c_tid','=',$id)->select();
        $excel = new \PHPExcel();
        $excel->getProperties()->setCreator('新智云商机系统')
                ->setLastModifiedBy('新智云商机系统')
                ->setTitle($title)
                ->setSubject($title)
                ->setDescription('新智云商机系统-'.$title.'-'.date('Y-m-d h:i:s'));

        $excel->setActiveSheetIndex(0);
        $activeSheet = $excel->getActiveSheet();
        $activeSheet->setTitle($title);

        foreach($res as $cell){
            $colIndex = $cell->c_row;
            $col = \PHPExcel_Cell::stringFromColumnIndex($colIndex-1);
            $row = $cell->c_col;
            $cSpan = $cell->c_colspan;
            $rSpan = $cell->c_rowspan;
            if( $cSpan || $rSpan ){
                $sRow = $row + $rSpan-1;
                $sCol = \PHPExcel_Cell::stringFromColumnIndex($colIndex + $cSpan - 2);
                $activeSheet->mergeCells($col.$row.':'.$sCol.$sRow);
            }
            $val = $cell->c_value;
            $activeSheet->setCellValue($col.$row,$val);
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$title.'.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->save('php://output');

        exit;

    }

}