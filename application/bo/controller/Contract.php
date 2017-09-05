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
        $this->assign("type", "contract");
        $post = Request::instance()->param();
        $search = array();
        if (count($post) > 0) {
            foreach ($post['fields'] as $key => $value) {
                $val = count($post['values'][$key]) > 1 ? $post['values'][$key] : trim($post['values'][$key][0]);
                $opt = trim($post['operators'][$key]);
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
        $list = $contractModel->getContractList($search, $this->limit);
        $this->assign("lists", $list);
        $this->assign("empty", '<tr><td colspan="3">暂无数据</td></tr>');
        if (Request::instance()->isAjax()) {
            if (count($post) > 0) {
                $content = $this->fetch("list");
            } else {
                $this->assign("searchable", $contractModel->getSearchable());
                $content = $this->fetch("common/poplayer");
            }
            return array("content" => $content);
        } else {
            $this->assign("searchable", $contractModel->getSearchable());
            return $this->fetch("common/poplayer");
        }
    }
}