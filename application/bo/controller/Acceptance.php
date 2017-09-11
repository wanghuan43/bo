<?php
namespace app\bo\controller;

use think\Controller;
use think\Request;

class Acceptance extends Controller
{
    var $limit = 20;

    public function searchAcceptance()
    {
        $acceptanceModel = new \app\bo\model\Acceptance();
        $this->assign("type", "acceptance");
        $post = Request::instance()->param();
        $search = array();
        if (isset($post['fields'])) {
            foreach ($post['fields']['acceptance'] as $key => $value) {
                $val = count($post['values']['acceptance'][$key]) > 1 ? $post['values']['acceptance'][$key] : trim($post['values']['acceptance'][$key][0]);
                $opt = trim($post['operators']['acceptance'][$key]);
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
        $list = $acceptanceModel->getAcceptanceList($search, $this->limit);
        $this->assign("lists", $list);
        $this->assign("empty", '<tr><td colspan="3">暂无数据</td></tr>');
        if (Request::instance()->isAjax()) {
            if (count($post) > 0) {
                $content = $this->fetch("list");
            } else {
                $this->assign("searchable", $acceptanceModel->getSearchable());
                $content = $this->fetch("common/poplayer");
            }
            return array("content" => $content);
        } else {
            $this->assign("searchable", $acceptanceModel->getSearchable());
            return $this->fetch("common/poplayer");
        }
    }
}