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
        $tmp = $this->order('parent_id', 'ASC')->order('list_order', 'ASC')->select();
        $menus = array();
        foreach ($tmp as $key => $value) {
            $value = $value->toArray();
            $value['title'] = $value['name'];
            if (empty($value['parent_id'])) {
                if(isset($menus[$value['id']])){
                    $value['children'] = $menus[$value['id']]['children'];
                }else{
                    $value['children'] = array();
                    $value['folder'] = true;
                    $value['expanded'] = false;
                }
                $menus[$value['id']] = $value;
            } else {
                $menus[$value['parent_id']]['children'][] = $value;
            }
        }
        return $menus;
    }
}