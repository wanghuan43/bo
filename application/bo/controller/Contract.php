<?php
namespace app\bo\controller;

use think\Controller;
use think\Request;
use app\bo\model\Contract;

class Contract extends Controller
{
    var $limit = 20;

    function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function searchContract()
    {
        $contractModel = new Contract();
        if (Request::instance()->isAjax()) {
            $post = Request::instance()->param();
            if (count($post) > 0) {
                $search = array();
                foreach ($post['fields'] as $key => $value) {
                    $val = count($post['values'][$key]) > 1 ? $post['values'][$key] : trim($post['values'][$key][0]);
                    $opt = trim($post['operators'][$key]);
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
                $list = $contractModel->getContractList($search, $this->limit);
                $this->assign("lists", $list);
                $this->assign("empty", '<tr><td colspan="7">暂无数据</td></tr>');
                $content = $this->fetch("list");
            } else {
                $list = $contractModel->getContractList(array(), $this->limit);
                $this->assign("lists", $list);
                $this->assign("empty", '<tr><td colspan="7">暂无数据</td></tr>');
                $this->assign("searchable", $contractModel->getSearchable());
                $content = $this->fetch("poplayer");
            }
            return array("content" => $content);
        } else {
            $this->assign("searchable", $contractModel->getSearchable());
            return $this->fetch("poplayer");
        }
    }
}