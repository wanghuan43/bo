<?php
namespace app\bo\model;

use app\bo\libs\BoModel;

class Logs extends BoModel
{
    protected $pk = "l_id";

    public function saveLogs($new, $old, $id, $model, $opt="add")
    {
        $member = $this->getCurrent();
        if (empty($new) OR empty($id) OR empty($model) OR !$member) {
            return false;
        }
        $l_panding = $opt == "add" ? 2 : 0;
        $data = [
            "l_otid" => $id,
            "l_mid" => $member->m_id,
            "l_mname" => $member->m_name,
            "l_opt" => $opt,
            "l_new" => serialize($new),
            "l_old" => serialize($old),
            "l_model" => $model,
            "l_isadmin" => $member->m_isAdmin,
            "l_panding" => $l_panding,
            "l_createtime" => time(),
        ];
        return $this->save($data);
    }
}