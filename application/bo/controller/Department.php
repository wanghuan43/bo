<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/9/26
 * Time: 下午2:24
 */

namespace app\bo\controller;


use app\bo\libs\BoController;
use think\Request;

class Department extends BoController
{

    public function __construct(Request $request)
    {
        $this->model = new \app\bo\model\Department();
        parent::__construct($request);
    }

    public function searchDepartment()
    {
        $this->assign("type", "department");
        return $this->search($this->model);
    }

}