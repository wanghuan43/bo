<?php
namespace app\bo\libs;

use think\Model;
use think\Request;

class BoModel extends Model
{
    public function getCurrent()
    {
        return getLoginMember();
    }

    /**
     * @return array
     */
    public function getSearchable()
    {
        $fields = array();
        $operators = array();
        foreach ($this->searchable as $key => $value) {
            $fields[$key] = array("name" => $value['name'], "type" => $value['type']);
            $operators[$key] = $value['operators'];
        }
        return array("fields" => $fields, "operators" => $operators, "length" => count($fields));
    }
}