<?php
namespace app\bo\controller;

use think\Controller;
use think\Request;

class Company extends Controller
{

    var $limit = 20;

    function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function searchCompany()
    {
        $companyModel = new \app\bo\model\Company();
        $this->assign("type", "company");
        $post = Request::instance()->param();
        $search = array();
        if (count($post) > 0) {
            foreach ($post['fields']['company'] as $key => $value) {
                $val = count($post['values']['company'][$key]) > 1 ? $post['values']['company'][$key] : trim($post['values']['company'][$key][0]);
                $opt = trim($post['operators']['company'][$key]);
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
        $search[] = array(
            "field" => "co_status",
            "opt" => "=",
            "val" => "1"
        );
        $list = $companyModel->getCompanyList($search, $this->limit);
        $this->assign("lists", $list);
        $this->assign("empty", '<tr><td colspan="3">暂无数据</td></tr>');
        if (Request::instance()->isAjax()) {
            if (count($post) > 0) {
                $content = $this->fetch("list");
            } else {
                $this->assign("searchable", $companyModel->getSearchable());
                $content = $this->fetch("common/poplayer");
            }
            return array("content" => $content);
        } else {
            $this->assign("searchable", $companyModel->getSearchable());
            return $this->fetch("common/poplayer");
        }
    }
}