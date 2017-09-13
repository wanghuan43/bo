<?php
namespace app\bo\model;

use app\bo\libs\BoModel;

class Menu extends BoModel
{
    protected $pk = "id";

    public function getChilde()
    {
        return $this->hasMany("Menu", "parent_id", "id")->field("id,name,url,parent_id,flag");
    }

    public function getList()
    {
        $tmp = $this->order('list_order', 'ASC')->select();
        $menus = array();
        foreach ($tmp as $key => $value) {
            $value = $value->toArray();
            if (empty($value['parent_id'])) {
                $value['childrenList'] = array();
                $menus[$value['id']] = $value;
            } elseif (isset($menus[$value['parent_id']])) {
                $menus[$value['parent_id']]['childrenList'][] = $value;
            }
        }
        return $menus;
    }
}