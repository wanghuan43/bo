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
                "like" => "包含",
                "=" => "等于",
            ),
        ),
        "co_code" => array(
            "name" => "公司代码",
            "type" => "text",
            "operators" => array(
                "like" => "包含",
                "=" => "等于",
            ),
        ),
        'co_type' => array(
            'name' => '类型',
            'type' => 'select',
            'operators' => array(
                '=' => '等于'
            ),
            'options' => array(
                '0' => '-请选择-',
                '1' => '供应商',
                '2' => '客户'
            )
        )
    );

    public function getList($search, $limit)
    {
        $member = $this->getCurrent();
        $this->alias('co');
        if (!$member->m_isAdmin) {
            $this->join('__CIRCULATION__ c', "co.co_id = c.ci_otid AND c.ci_type = 'company'");
            $this->where("c.ci_mid", "=", $member->m_id);
        }
        $this->field("co.*");
        foreach ($search as $key => $value) {
            $this->where("co." . $value['field'], $value['opt'], $value['val']);
        }
        if( $limit===false ){
            $list = $this->select();
        }else {
            $list = $this->paginate($limit, false, array("query" => ["c_type" => Request::instance()->get("c_type")]));
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
            $model = $this->where('co_status', '=', 1);

            if ($code) {
                $model = $model->where('co_code', '=', $code);
            } elseif ($name) {
                $model = $model->where('co_name', '=', $name);
            }

            if ($type) {
                $model = $model->where('co_type', '=', $type);
            }

            $res = $model->find();
        }

        return $res;

    }

}