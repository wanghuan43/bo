<?php
namespace app\bo\controller;

use think\Controller;
use think\Request;

class Received extends Controller
{
    var $limit = 20;

    public function searchReceived()
    {
        $receivedModel = new \app\bo\model\Received();
        $this->assign("type", "received");
        $post = Request::instance()->param();
        $search = array();
        if (isset($post['fields'])) {
            foreach ($post['fields']['received'] as $key => $value) {
                $val = count($post['values']['received'][$key]) > 1 ? $post['values']['received'][$key] : trim($post['values']['received'][$key][0]);
                $opt = trim($post['operators']['received'][$key]);
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
        $list = $receivedModel->getReceivedList($search, $this->limit);
        $this->assign("lists", $list);
        $this->assign("empty", '<tr><td colspan="3">暂无数据</td></tr>');
        if (Request::instance()->isAjax()) {
            if (count($post) > 0) {
                $content = $this->fetch("list");
            } else {
                $this->assign("searchable", $receivedModel->getSearchable());
                $content = $this->fetch("common/poplayer");
            }
            return array("content" => $content);
        } else {
            $this->assign("searchable", $receivedModel->getSearchable());
            return $this->fetch("common/poplayer");
        }
    }
}