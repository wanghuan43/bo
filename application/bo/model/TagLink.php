<?php
namespace app\bo\model;

use app\bo\libs\BoModel;

class Taglink extends BoModel
{
    public function getList($ot_id, $model = "orders")
    {
        $db = $this->db();
        $list = $db->table('__TAGLIB__')
            ->alias('tl')
            ->join('__TAGLINK__ tk', 'tl.tl_id = tk.tl_id')
            ->where('tk.ot_id', $ot_id)
            ->where('tk.model', $model)
            ->select();
        return $list;
    }

    public function setTagLink($ot_id, $list, $model = "orders")
    {
        $tl = array();
        $tlm = new Taglib();
        foreach ($list as $value) {
            $value = trim($value);
            if (is_numeric($value)) {
                $tlm = Taglib::get($value);
                $tlm->tl_times = intval($tlm->tl_times) + 1;
            } else {
                $tlm = $tlm->where("tl_name","=",$value)->find();
                if (empty($tlm->tl_id)) {
                    $tlm = new Taglib();
                    $tlm->tl_times = 1;
                    $tlm->tl_name = $value;
                } else {
                    $tlm->tl_times = intval($tlm->tl_times) + 1;
                }
            }
            $tlm->save();
            $tl[] = array("ot_id" => $ot_id, "tl_id" => $tlm->tl_id, "model" => $model);
        }
        $this->where("ot_id", "=", $ot_id)->where("model", "=", $model)->delete();
        if (count($tl) > 0) {
            $this->saveAll($tl);
        }
    }
}