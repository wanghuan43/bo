<?php
namespace app\bo\model;

use think\Model;

class TagLink extends Model
{
    public function getTagList($ot_id, $model)
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
}