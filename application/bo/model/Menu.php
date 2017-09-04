<?php
namespace app\bo\model;

use think\Model;

class Menu extends Model
{
    protected $pk = "id";

    public function getChilde()
    {
        return $this->hasMany("Menu", "parent_id", "id")->field("id,name,url,parent_id,flag");
    }
}