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
}