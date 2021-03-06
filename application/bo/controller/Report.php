<?php

namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\libs\ReportEntity;
use think\Request;

class Report extends BoController
{
    var $reportEntity;

    function __construct(Request $request)
    {
        parent::__construct($request);
        $this->reportEntity = new ReportEntity();
    }

    public function getReport($type = "project")
    {
        $this->switchType($type);
        $cols = $this->reportEntity->getReportCols($type);
        $mname = "app\\bo\\model\\" . ucfirst($type);
        $model = new $mname();
        $this->assign('search', $model->getSearchable());
        $this->assign('modelName', ucfirst($type));
        $this->assign("cols", $cols);
        $this->assign("type", $type);
        return $this->fetch("report/report");
    }

    public function doReport($type)
    {
        $cols = Request::instance()->post("selectCell");
        $cols = json_decode($cols, true);
        $search['fields'] = Request::instance()->post("fields/a")[$type];
        $search['operators'] = Request::instance()->post("operators/a")[$type];
        $search['values'] = Request::instance()->post("values/a")[$type];
        $tmp = [];
        foreach($cols as $val){
            foreach($val as $k=>$v){
                $tmp[$k] = $v;
            }
        }
        $this->reportEntity->doReport($type, $tmp, $search);
    }

    private function switchType($type)
    {
        switch ($type) {
            case "project":
                $this->assign("title", "导出项目");
                break;
            case "orders":
                $this->assign("title", "导出订单");
                break;
            case "contract":
                $this->assign("title", "导出合同");
                break;
            case "invoice":
                $this->assign("title", "导出发票");
                break;
            case "received":
                $this->assign("title", "导出付款单");
                break;
            case "acceptance":
                $this->assign("title", "导出验收单");
                break;
        }
    }
}