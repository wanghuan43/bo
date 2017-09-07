<?php
namespace app\bo\model;

use think\Model;

class Orders extends Model
{
    protected $pk = 'o_id';

    public function getOrderNO($p_id)
    {
        $projectModel = new Project();
        $project = $projectModel->get($p_id);
        $c = $this->where("o_pid", "=", $p_id)->count();
        return $project->p_no . "-" . str_pad(($c+1), 6, "0", STR_PAD_LEFT);
    }
}