<?php
namespace app\bo\libs;

use think\Config;
use think\Controller;
use think\Request;
use think\Url;

class BoController extends Controller
{
    protected $limit = 20;
    protected $mem;
    protected $current = false;

    /**
     * BoController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $nowAction = $request->action();
        $this->current = getLoginMember();
        if(!$this->current AND  $nowAction != "login"){
            $this->redirect(Url::build('/dashboard/login', "", false));
        }
    }

    protected function search($model, $file="common/poplayer", $colspan = "3")
    {
        $post = Request::instance()->param();
        $name = get_class($model);
        $name = strtolower(substr($name, strripos($name, "\\")+1));
        $search = array();
        if (isset($post['fields'])) {
            foreach ($post['fields'][$name] as $key => $value) {
                $val = count($post['values'][$name][$key]) > 1 ? $post['values'][$name][$key] : trim($post['values'][$name][$key][0]);
                $opt = trim($post['operators'][$name][$key]);
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
        $list = $model->getList($search, $this->limit);
        $this->assign("lists", $list);
        $this->assign("empty", '<tr><td colspan="' + $colspan + '">暂无数据</td></tr>');
        if (Request::instance()->isAjax()) {
            if (count($post) > 0) {
                $content = $this->fetch("list");
            } else {
                $this->assign("searchable", $model->getSearchable());
                $content = $this->fetch($file);
            }
            return array("content" => $content);
        } else {
            $this->assign("searchable", $model->getSearchable());
            return $this->fetch($file);
        }
        return "";
    }
}