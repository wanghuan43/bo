<?php

namespace app\bo\controller;

use app\bo\libs\BoController;
use think\Request;

class Favorite extends BoController
{
    protected $fmodel;

    function __construct(Request $request)
    {
        parent::__construct($request);
        $this->fmodel = new \app\bo\model\Favorite();
    }

    public function saveFavorite($opID)
    {
        $val = Request::instance()->post("save");
        $mid = $this->current->m_id;
        if (!empty($val)) {
            $rs = $this->fmodel->where("f_oid", "=", $opID)->where("f_mid", "=", $mid)->find()->delete();
            $message = "取消收藏成功";
        } else {
            $c = $this->fmodel->where("f_oid", "=", $opID)->where("f_mid", "=", $mid)->count();
            if (empty($c)) {
                $rs = $this->fmodel->save(["f_oid" => $opID, "f_mid" => $mid]);
            }
            $message = "收藏成功";
        }
        return ["status" => $rs, "message" => $message];
    }
}