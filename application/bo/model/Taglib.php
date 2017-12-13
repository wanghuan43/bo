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
        $this->alias('t');
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