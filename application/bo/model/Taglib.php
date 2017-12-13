<?php

namespace app\bo\model;

use app\bo\libs\BoModel;

class Taglib extends BoModel
{
    protected $pk = 'tl_id';
    protected $searchable = [
        'tl_name' => [
            'name' => '标签名称',
            'type' => 'text',
            'operators' => [
                'like' => '包含'
            ]
        ]
    ];

    public function getList($search = array(), $limit = 20)
    {
        $member = $this->getCurrent();
        $this->alias('t');
        if ($member->m_isAdmin == "2") {
            $this->join('__CIRCULATION__ c', "t.tl_id = c.ci_otid AND c.ci_type = 'taglib'", "left");
            $this->where("c.ci_mid", "=", $member->m_id);
        }
        $this->field("t.*");
        foreach ($search as $key => $value) {
            $this->where("t." . $value['field'], $value['opt'], $value['val']);
        }
        $list = $this->paginate($limit);
        return $list;
    }

    public function getTagByName($tagName)
    {
        $res = $this->where('tl_name','=',$tagName)->find();
        if(empty($res)){
            $data = ['tl_name'=>$tagName,'tl_times'=>0];
            $data['tl_id'] = $this->insertGetId($data);
        }else{
            $data = $res->getData();
        }
        return $data;
    }

    public function getTagIdByName($tagName)
    {
        $data = $this->getTagByName($tagName);
        return $data['tl_id'];
    }

}