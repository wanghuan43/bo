<?php
namespace app\bo\controller;

use think\Controller;
use think\Request;

class Contract extends Controller
{
    var $limit = 20;

    function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function searchContract()
    {
        $contractModel = new \app\bo\model\Contract();
        $this->assign("type", "contract");
        $post = Request::instance()->param();
        $search = array();
        if (isset($post['fields'])) {
            foreach ($post['fields']['contract'] as $key => $value) {
                $val = count($post['values']['contract'][$key]) > 1 ? $post['values']['contract'][$key] : trim($post['values']['contract'][$key][0]);
                $opt = trim($post['operators']['contract'][$key]);
                $val = is_array($val) ? ((empty($val['0']) AND empty($val['1'])) ? "" : $val) : $val;
                if (!empty($val)) {
                    if ($opt == "between") {
                        if(!is_array($val)){
                            $val = explode(" ~ ", $val);
                            $val[0] = strtotime($val[0]);
                            $val[1] = strtotime($val[1]);
                        }
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