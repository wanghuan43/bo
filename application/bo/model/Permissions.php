<?php
namespace app\bo\model;

use think\Model;

class Permissions extends Model
{
    protected $pk = "id";

    public function getMenuList($member_id=""){
        $tmp = $this->field("m.*")->alias("p")->join("__MENU__ m","p.menu_id = m.id")
                ->where("m.is_show", "=", "1")->order("m.id", "ASC")->select();
        $menus = array();
        foreach($tmp as $key=>$value){
            $value = $value->toArray();
            if(empty($value['parent_id'])){
                $value['childrenList'] = array();
                $menus[$value['id']] = $value;
            }elseif(isset($menus[$value['parent_id']])){
                $menus[$value['parent_id']]['childrenList'][] = $value;
            }
        }
        return $menus;
    }
}