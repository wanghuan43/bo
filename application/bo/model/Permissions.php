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
                ->where("m.is_show", "=", "1")->order("m.parent_id", "ASC")->order("m.list_order", "ASC")
                ->order('m.id','ASC')->select();
        $menus = array();
        foreach($tmp as $key=>$value){
            $value = $value->toArray();
            if(empty($value['parent_id'])){
                if(isset($menus[$value['id']])){
                    $value['children'] = $menus[$value['id']]['children'];
                }else{
                    $value['children'] = array();
                }
                $menus[$value['id']] = $value;
            }else{
                $menus[$value['parent_id']]['children'][] = $value;
            }
        }
        return $menus;
    }
}