<?php
namespace app\bo\model;

use think\Model;

class Permissions extends Model
{
    protected $pk = "id";

    public function menus()
    {
        return $this->hasOne("Menu", "id", "menu_id")->field("id,name,url,parent_id,flag");
    }

    public function members()
    {
        return $this->hasOne("Member", "m_id", "member_id");
    }

    public function getMenuList($member_id=""){
        $tmp = $this->order("id", "ASC")->select();
        $menus = array();
        foreach($tmp as $key=>$value){
            $t = $value->menus()->where('id', $value->menu_id)->find();
            $t = $t->toArray();
            if(empty($t['parent_id'])){
                $t['childrenList'] = array();
                $menus[$t['id']] = $t;
            }elseif(isset($menus[$t['parent_id']])){
                $menus[$t['parent_id']]['childrenList'][] = $t;
            }
        }
        return $menus;
    }
}