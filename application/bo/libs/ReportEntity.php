<?php
/**
 * Created by PhpStorm.
 * User: jerry
 * Date: 2017/11/16
 * Time: 03:33
 */

namespace app\bo\libs;


use app\bo\model\Orders;
use PHPExcel;
use PHPExcel_IOFactory;
use think\Config;

class ReportEntity extends BoModel
{
    var $cols;

    /**
     * @param $type
     */
    public function getReportCols($type)
    {
        if (!isset($this->cols[$type])) {
            Config::load(APP_PATH . "bo" . DS . "reportExcel.php", "", "reportExcel");
            $this->cols[$type] = Config::get($type, "reportExcel");
        }
        return $this->cols[$type];
    }

    /**
     * @param $type
     */
    public function doReport($type, $sendCells)
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $title = $this->switchTitle($type);
        $obj = new PHPExcel();
        $obj->getProperties()->setCreator("新智云商机管理系统")
            ->setLastModifiedBy("新智云商机管理系统")
            ->setTitle($title)
            ->setSubject($title)
            ->setDescription($title);
        $obj->setActiveSheetIndex(0);
        $activeSheet = $obj->getActiveSheet();
        $fcols = $this->getColsName(count($sendCells));
        $cols = array_keys($sendCells);
        foreach ($fcols as $k => $i) {
            $activeSheet->setCellValue($i . "1", $cols[$k]);
        }
        $tmp = [];
        $i = 0;
        foreach ($sendCells as $key => $value) {
            $tmp[$value['by']][$fcols[$i]] = $value['col'];
            $i++;
        }
        $model = new Orders();
        $omodel = new Orders();
        switch ($type) {
            case "orders":
                $model->reportList($tmp[$type], $activeSheet, $type, 2, "");
                break;
            default:
                $mname = "app\\bo\\model\\" . ucfirst($type);
                $mtmp = new $mname();
                $lists = $mtmp->select();
                $begin = 1;
                $id = "";
                $tmps = $model->field("SUM(o_money) as om,o_pid,o_type")->group("o_pid,o_type")->select();
                $clist = [];
                foreach ($tmps as $val) {
                    $clist[$val['o_pid']][$val["o_type"]] = $val['om'];
                }
                $tmps = $model->field("o_money,o_tax,o_pid,o_type")->select();
                $cclist = [];
                foreach ($tmps as $val) {
                    $tax = intval(getTaxList($val['o_tax'])) / 100;
                    if (isset($cclist[$val['o_pid']][$val['o_type']])) {
                        $cclist[$val['o_pid']][$val['o_type']] += $val['o_money'] - $val['o_money'] * $tax;
                    }else{
                        $cclist[$val['o_pid']][$val['o_type']] = $val['o_money'] - $val['o_money'] * $tax;
                    }
                }
                foreach ($lists as $key => $value) {
                    switch ($type) {
                        case "project":
                            $id = $value['p_id'];
                            break;
                        case "contract":
                            $id = $value['c_id'];
                            break;
                        case "invoice":
                            $id = $omodel->alias("o")->join("__ORDER_USED__ ou", "o.o_id = ou.ou_oid", "left")
                                ->field("o.o_id")->where("ou.ou_otid", "=", $value['i_id'])
                                ->where("ou.ou_type", "=", "1")->select();
                            break;
                        case "received":
                            $id = $omodel->alias("o")->join("__ORDER_USED__ ou", "o.o_id = ou.ou_oid", "left")
                                ->field("o.o_id")->where("ou.ou_otid", "=", $value['r_id'])
                                ->where("ou.ou_type", "=", "3")->select();
                            break;
                        case "acceptance":
                            $id = $omodel->alias("o")->join("__ORDER_USED__ ou", "o.o_id = ou.ou_oid", "left")
                                ->field("o.o_id")->where("ou.ou_otid", "=", $value['a_id'])
                                ->where("ou.ou_type", "=", "2")->select();
                            break;
                    }
                    $begin += 1;
                    foreach ($tmp[$type] as $k => $val) {
                        $v = "";
                        if (isset($value[$val])) {
                            switch ($val) {
                                case "p_date":
                                    $v = date("Y-m-d", $value[$val]);
                                    break;
                                case "c_date":
                                    $v = date("Y-m-d", $value[$val]);
                                    break;
                                case "c_type":
                                    $v = getTypeList2($value[$val]);
                                    break;
                                case "i_date":
                                    $v = date("Y-m-d", $value[$val]);
                                    break;
                                case "i_type":
                                    $v = getTypeList2($value[$val]);
                                    break;
                                case "i_tax":
                                    $v = getTaxList($value[$val]);
                                    break;
                                case "r_date":
                                    $v = date("Y-m-d", $value[$val]);
                                    break;
                                case "r_type":
                                    $v = getTypeList2($value[$val]);
                                    break;
                                case "a_date":
                                    $v = date("Y-m-d", $value[$val]);
                                    break;
                                case "a_type":
                                    $v = getTypeList2($value[$val]);
                                    break;
                                default:
                                    $v = $value[$val];
                                    break;
                            }
                        } else {
                            switch ($val) {
                                case "p_ileft":
                                    if (isset($clist[$value['p_id']][1])) {
                                        $v = $value['p_income'] - $clist[$value['p_id']][1];
                                    }
                                    break;
                                case "p_pleft":
                                    if (isset($clist[$value['p_id']][2])) {
                                        $v = $value['p_pay'] - $clist[$value['p_id']][2];
                                    }
                                    break;
                                case "p_total":
                                    if (isset($cclist[$value['p_id']])) {
                                        $intmp = isset($cclist[$value['p_id']][1]) ? $cclist[$value['p_id']][1] : 0;
                                        $outmp = isset($cclist[$value['p_id']][2]) ? $cclist[$value['p_id']][2] : 0;
                                        $v = $intmp - $outmp;
                                    }
                                    break;
                            }
                        }
                        $activeSheet->setCellValue($k . $begin, $v);
                    }
                    if (is_array($id) AND isset($tmp["orders"])) {
                        $count = $begin = $begin + 1;
                        $in = false;
                        foreach ($id as $ii) {
                            $in = true;
                            $count = $model->reportList($tmp["orders"], $activeSheet, $type, $begin, $ii->o_id);
                        }
                        if($count === false OR !$in){
                            $begin = $begin -1;
                        }else{
                            $begin = $count;
                        }
                    } elseif(isset($tmp["orders"])) {
                        $begin = $begin + 1;
                        $count = $model->reportList($tmp["orders"], $activeSheet, $type, $begin, $id);
                        if($count === false){
                            $begin = $begin -1;
                        }else{
                            $begin = $count;
                        }
                    }
                }
                break;
        }
        $activeSheet->setTitle($title);
        $fileName = $title . '-' . date('ymdHis');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    private function getColsName($col)
    {
        $asc = 65;
        $round = ceil($col / 26);
        $left = $col % 26;
        $r = [];
        for ($i = 0; $i < $round; $i++) {
            $count = $col < 26 ? $col : ($i == ($round - 1) ? ($round == 1 ? 26 : $left) : 26);
            $before = $i == 0 ? "" : chr($asc + ($i - 1));
            for ($j = 0; $j < $count; $j++) {
                $r[] = $before . chr($asc + $j);
            }
        }
        return $r;
    }

    private function switchTitle($type)
    {
        $title = "";
        switch ($type) {
            case "project":
                $title = "项目汇总报表";
                break;
            case "orders":
                $title = "订单汇总报表";
                break;
            case "contract":
                $title = "合同汇总报表";
                break;
            case "invoice":
                $title = "发票汇总报表";
                break;
            case "received":
                $title = "收付款汇总报表";
                break;
            case "acceptance":
                $title = "交付汇总报表";
                break;
        }
        return $title;
    }
}