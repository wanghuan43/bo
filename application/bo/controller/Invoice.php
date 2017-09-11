<?php
namespace app\bo\controller;

use think\Controller;
use think\Request;

class Invoice extends Controller
{
    var $limit = 20;

    public function searchInvoice()
    {
        $invoiceModel = new \app\bo\model\Invoice();
        $this->assign("type", "invoice");
        $post = Request::instance()->param();
        $search = array();
        if (isset($post['fields'])) {
            foreach ($post['fields']['invoice'] as $key => $value) {
                $val = count($post['values']['invoice'][$key]) > 1 ? $post['values']['invoice'][$key] : trim($post['values']['invoice'][$key][0]);
                $opt = trim($post['operators']['invoice'][$key]);
                $val = is_array($val) ? ((empty($val['0']) AND empty($val['1'])) ? "" : $val) : $val;
                if (!empty($val)) {
                    if ($opt == "between") {
                        $val = is_array($val) ? $val : explode(" ~ ", $val);
                    } elseif ($opt == "like") {
                        $val = "$val%";
                    }
                    $search[] = array(
                        "field" => $value,
                        "opt" => $opt,
                        "val" => $val
                    );
                }
            }
        }
        $list = $invoiceModel->getInvoiceList($search, $this->limit);
        $this->assign("lists", $list);
        $this->assign("empty", '<tr><td colspan="3">暂无数据</td></tr>');
        if (Request::instance()->isAjax()) {
            if (count($post) > 0) {
                $content = $this->fetch("list");
            } else {
                $this->assign("searchable", $invoiceModel->getSearchable());
                $content = $this->fetch("common/popused");
            }
            return array("content" => $content);
        } else {
            $this->assign("searchable", $invoiceModel->getSearchable());
            return $this->fetch("common/popused");
        }
    }
}