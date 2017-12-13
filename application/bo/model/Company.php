<?php

namespace app\bo\model;

use app\bo\libs\BoModel;
use think\Request;

class Company extends BoModel
{
    protected $pk = 'co_id';

    protected $searchable = array(
        "co_name" => array(
            "name" => "公司名称",
            "type" => "text",
            "operators" => array(
                "like" => "包含"
            ),
        ),
        "co_code" => array(
            "name" => "公司代码",
            "type" => "text",
            "operators" => array(
                "like" => "包含"
            ),
        )
    );

    public function getList($search, $limit)
    {
        $member = $this->getCurrent();
        if ($member->m_isAdmin == "2") {
            $c = new Circulation();
            $c->alias("c")->field('co.*')->join("__COMPANY__ co", "c.ci_otid = co.co_id", "LEFT")
                ->where("c.ci_type", "=", "company")->where("c.ci_mid|co.co_mid", "=", $member->m_id);
            foreach ($search as $key => $value) {
                $c->where("co." . $value['field'], $value['opt'], $value['val']);
            }
            if ($limit === false) {
                $list = $c->select();
            } else {
                $list = $c->paginate($limit);
            }
        }else{
            $this->alias('co')->field("co.*");
            foreach ($search as $key => $value) {
                $this->where("co." . $value['field'], $value['opt'], $value['val']);
            }
            if( $limit===false ){
                $list = $this->select();
            }else {
                $list = $this->paginate($limit, false, array("query" => ["c_type" => Request::instance()->get("c_type")]));
            }
        }
        return $list;
    }

    /**
     * 根据公司编码、公司名、类型获取公司
     * @param bool $code
     * @param bool $name
     * @param bool $type
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getCompany($code=false,$name=false,$type=false){


        if(!$code && !$name){
            $res = false;
        }else {
            $this->where('co_status', '=', 1);

            if ($code) {
                $this->where('co_code', '=', $code);
            } elseif ($name) {
                $this->where('co_name', '=', $name);
            }

            if ($type) {
                $this->where('co_type', '=', $type);
            }

            $res = $this->find();
        }

        return $res;

    }

    protected function doImport($dataset)
    {
        return $this->insertDuplicate($dataset);
    }

}