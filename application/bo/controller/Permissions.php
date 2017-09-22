<?php

namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\model\Member;
use app\bo\model\Menu;
use think\Request;

class Permissions extends BoController
{
    public function index()
    {
        $memberModel = new Member();
        $filters = Request::instance()->session('filtersPermissions', array());
        $params = Request::instance()->param();
        unset($params['page']);
        $params = array_merge($filters, $params);
        foreach ($params as $key => $value) {
            $memberModel->where($key, $value);
        }
        session("filtersPermissions", $params);
        $lists = $memberModel->paginate($this->limit);
        $this->assign("memberList", $lists);
        $this->assign("empty", '<tr><td colspan="4">无用户权限数据.</td></tr>');
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
        $post['ids'] = $post['ids'] == "del" ? array() : (is_array($post['ids']) ? $post['ids'] : array($post['ids']));
        if (!empty($post['isAdmin'])) {
            $post['ids'] = array();
        }
        Member::update(["m_isAdmin" => $post['isAdmin']], ["m_id" => $post['mid']]);
        $data = array();
        foreach ($post['ids'] as $val) {
            $data[] = [
                "menu_id" => $val,
                "member_id" => $post['mid'],
                "opt" => 1,
            ];
        }
        $ps = new \app\bo\model\Permissions();
        \app\bo\model\Permissions::destroy(["member_id" => $post['mid']]);
        if(count($data) > 0){
            $ps->saveAll($data);
        }
        return ["status" => true, "message" => "保存成功"];
    }
}