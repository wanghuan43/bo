<?php
namespace app\bo\model;

use app\bo\libs\BoModel;

class Permissions extends BoModel
{
    protected $pk = "id";

    public function getList($member_id=""){
        if(!empty($member_id)){
            $this->where("p.member_id", "=", $member_id);
        }
        $tmp = $this->field("m.*")->alias("p")->join("__MENU__ m","p.menu_id = m.id","LEFT")
                ->where("m.is_show", "=", "1")->order("m.list_order", "ASC")->select();
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