<?php
namespace app\bo\controller;

use think\Controller;
use think\Request;
use think\Session;

class Project extends Controller
{
    var $limit = 20;

    public function searchProject()
    {
        $projectModel = new \app\bo\model\Project();
        $this->assign("type", "project");
        $post = Request::instance()->post();
        $session = Session::get("projectSearch");
        $page = Request::instance()->get("page");
        $search = array();
        if (count($post) > 0) {
            foreach ($post['fields']['project'] as $key => $value) {
                $val = count($post['values']['project'][$key]) > 1 ? $post['values']['project'][$key] : trim($post['values']['project'][$key][0]);
                $opt = trim($post['operators']['project'][$key]);
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
            Session::set("projectSearch", $search);
        }elseif(count($session) > 0){
            $search = $session;
        }
        $list = $projectModel->getProjectList($search, $this->limit);
        $this->assign("lists", $list);
        $this->assign("empty", '<tr><td colspan="3">暂无数据</td></tr>');
        if (Request::instance()->isAjax()) {
            if (count($post) > 0 OR Request::instance()->has("page", "get")) {
                $content = $this->fetch("list");
            } else {
                $this->assign("searchable", $projectModel->getSearchable());
                $content = $this->fetch("common/poplayer");
            }
            return array("content" => $content);
        } else {
            $this->assign("searchable", $projectModel->getSearchable());
            return $this->fetch("common/poplayer");
        }
    }

}