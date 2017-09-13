<?php
namespace app\bo\model;

use app\bo\libs\BoModel;

class Logs extends BoModel
{
    public function saveLogs($content, $id, $model)
    {
        $member = $this->getCurrent();
        if (empty($content) OR empty($id) OR empty($model) OR !$member) {
            return false;
        }
        $data = [
            "l_otid" => $id,
            "l_mid" => $member->m_id,
            "l_mname" => $member->m_name,
            "l_content" => serialize($content),
            "l_model" => $model,
            "l_isadmin" => $member->m_isAdmin,
            "l_createtime" => time(),
        ];
        return $this->save($data);
    }
}