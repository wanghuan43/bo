<?php
namespace app\bo\model;

use app\bo\libs\BoModel;
use think\Config;

class Member extends BoModel
{
    protected $pk = 'm_id';

    protected $searchable = array(
        "m_code" => array(
            "name" => "用户编号",
            "type" => "text",
            "operators" => array(
                "like" => "包含",
                "=" => "等于",
            ),
        ),
        "m_name" => array(
            "name" => "用户名",
            "type" => "text",
            "operators" => array(
                "like" => "包含",
                "=" => "等于",
            ),
        ),
        "m_email" => array(
            "name" => "用户邮箱",
            "type" => "text",
            "operators" => array(
                "like" => "包含",
                "=" => "等于",
            ),
        ),
        "m_department" => array(
            "name" => "用户部门",
            "type" => "text",
            "operators" => array(
                "like" => "包含",
                "=" => "等于",
            ),
        ),
    );

    public function loginMember($data)
    {
        $permissionsModel = new Permissions();
        $menuModel = new Menu();
        $data['email'] = strtoupper($data['email']);
        $member = $this->where("m_email", "=", $data['email'])->find();
        $array = ["code" => 0, "member" => ""];
        if ($member) {
            $pwd = encryptPassword($data['password']);
            if ($member->m_password == $pwd) {
                if (empty($member->m_isAdmin)) {
                    $member->menu = $permissionsModel->getList($member->m_id);
                } else {
                    $menus = $menuModel->getList();
                    Config::load(APP_PATH . "bo" . DS . "commonField.php", "", "commonField");
                    $return = Config::get("permissionsMenu", "commonField");
                    foreach ($return as $v) {
                        $menus[] = $v;
                    }
                    $member->menu = $menus;
                }
                $array['member'] = $member;
            } else {
                $array['code'] = 2;
            }
        } else {
            $array['code'] = 1;
        }
        return $array;
    }

    public function getList($search = array(), $limit = 10)
    {
        $limit = 10;
        foreach( $search as $key => $value ){
            $this->where($value['field'],$value['opt'],$value['val']);
        }
        $list = $this->paginate($limit);
        return $list;
    }
}