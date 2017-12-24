<?php

namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\model\Member;
use app\bo\model\Menu;
use think\Request;

class Permissions extends BoController
{

    /**
     * Menu constructor.
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }
    public function index()
    {
        $menuModel = new Menu();
        $menuList = $menuModel->getList();
        $this->assign("menuList", json_encode(array_values($menuList)));
        return $this->fetch("permissions/index");
    }

    public function setPermissions($mid)
    {
        $menuModel = new Menu();
        $menuList = $menuModel->getList();
        $memberList = \app\bo\model\Permissions::all(["member_id" => $mid]);
        $tmp = [];
        foreach ($memberList as $val) {
            $tmp[] = $val['menu_id'];
        }
        $this->assign("mid", $mid);
        $this->assign("member", json_encode(Member::get(['m_id' => $mid])));
        $this->assign("memberList", "," . implode(",", $tmp) . ",");
        $this->assign("menuList", json_encode(array_values($menuList)));
        return $this->fetch("permissions/opt");
    }

    public function save()
    {
        $post = Request::instance()->post();
        $tmp = [];
        $permissions = new \app\bo\model\Permissions();
        foreach($post['menuids'] as $val){
            $tmp[] = ["menu_id"=>$val,"member_id"=>"","opt"=>"2"];
        }
        $permissions->where("member_id", "in", $post['mids'])->delete();
        if(count($tmp) > 0){
            $all = [];
            foreach($post['mids'] as $val){
                foreach($tmp as $k=>$v){
                    $v["member_id"] = $val;
                    $all[] = $v;
                }
            }
            $permissions->saveAll($all);
        }
        return ["status" => true, "message" => "保存成功"];
    }
}