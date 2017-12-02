<?php

namespace app\bo\model;

use app\bo\libs\BoModel;

class Acceptance extends BoModel
{

    protected $pk = "a_id";

    protected $searchable = array(
        'a_no' => array(
            'name' => '验收单号',
            'type' => 'text',
            'operators' => [
                'like' => '包含'
            ]
        ),
        'a_coname' => array(
            'name' => '对方公司',
            'type' => 'text',
            'operators' => [
                'like' => '包含'
            ]
        ),
        "a_date" => array(
            "name" => "验收日期",
            "type" => "date",
            "operators" => array(
                "between" => "介于",
                "=" => "等于",
                ">" => "大于",
                "<" => "小于",
            ),
        ),
        "a_money" => array(
            "name" => "金额",
            "type" => "price",
            "operators" => array(
                "between" => "介于",
                "=" => "等于",
                ">" => "大于",
                "<" => "小于",
            ),
        ),
        'a_subject' => array(
            'name' => '摘要',
            'type' => 'text',
            'operators' => array(
                'like' => '包含'
            )
        ),
        'a_noused' => array(
            'name' => '未对应订单金额',
            'type' => 'price',
            'operators' => array(
                'between' => '介于',
                '=' => '等于',
                '>' => '大于',
                '<' => '小于'
            )
        ),
        'a_type' => array(
            'name' => '类型',
            'type' => 'select',
            'operators' => array(
                '=' => '等于'
            ),
            'options' => array(
                '0' => '-请选择-',
                '1' => '销售',
                '2' => '采购'
            )
        ),
        'a_accdate' => array(
            'name' => '记账月',
            'type' => 'text',
            'operators' => array(
                'like' => '包含'
            )
        )
    );

    public function getList($search, $limit)
    {
        $member = $this->getCurrent();
        $this->alias('a');
        if ($member->m_isAdmin == "2") {
            $this->join('__CIRCULATION__ c', "a.a_id = c.ci_otid AND c.ci_type = 'acceptance'", "left");
            $this->where("c.ci_mid", "=", $member->m_id)->whereOr("a.a_mid", "=", $member->m_id);
        }
        $this->field("a.*");
        foreach ($search as $key => $value) {
            $this->where('a.' . $value['field'], $value['opt'], $value['val']);
        }
        if( $limit === false ){
            $list = $this->select();
        }else {
            $list = $this->paginate($limit);
        }
        return $list;
    }

    public function checkUsed($id, $money, $op = '-')
    {
        $tmp = $this->find($id);
        if(!$tmp){
            return true;
        }else{
            $tmp = $tmp->toArray();
        }
        $return = true;
        if ($op == "-") {
            if ($tmp['a_noused'] - $money < 0) {
                $return = false;
            } else {
                $this->save(['a_noused' => ($tmp['a_noused'] - $money), 'a_used' => ($tmp['a_used'] + $money)], ["a_id" => $id]);
            }
        } else {
            $this->save(['a_noused' => ($tmp['a_noused'] + $money), 'a_used' => ($tmp['a_used'] - $money)], ["a_id" => $id]);
        }
        return $return;
    }

    public function getCodeAndNameById($id)
    {
        $data = $this->getDataById($id);
        return ['code'=>$data['a_no'],'name'=>'验收单'.$data['a_no']];
    }

    public function doImport($dataset=false)
    {
        $mCompany = new Company();
        foreach ($dataset as $key=>$data){
            if(isset($data['a_mname']) && isset($data['d_name'])){
                $model = new Member();
                $member = $model->getMemberByName($data['a_mname'],$data['d_name']);
                if($member) $data['a_mid'] = $member->m_id;
                unset($data['d_name']);
            }elseif (isset($data['a_mname'])){
                $model = new Member();
                $member = $model->getMemberByName($data['a_mname']);
                if($member) $data['a_mid'] = $member->m_id;
            }

            if(isset($data['a_coname']) && $data['a_coname']){
                $type = $data['a_type'] == 2 ? 1 : 2;
                $c = $mCompany->getCompany(false,$data['a_coname'],$type);
                if($c){
                    $data['a_coid'] = $c->co_id;
                }else{
                    $data['a_coid'] = 0;
                }
            }

            $data['a_noused'] = $data['a_money'];
            $dataset[$key] = $data;
        }
        return $this->insertDuplicate($dataset);
    }

}