<?php

namespace app\bo\model;

use app\bo\libs\BoModel;

class Received extends BoModel
{

    protected $pk = "r_id";

    protected $searchable = array(
        'r_no' => array(
            'name' => '付/回款单号',
            'type' => 'text',
            'operators' => array(
                'like' => '包含'
            )
        ),
        'r_coname' => array(
            'name' => '对方名称',
            'type' => 'text',
            'operators' => array(
                'like' => '包含'
            )
        ),
        "r_date" => array(
            "name" => "付/回款日期",
            "type" => "date",
            "operators" => array(
                "between" => "介于",
                "=" => "等于",
                ">" => "大于",
                "<" => "小于",
            ),
        ),
        "r_money" => array(
            "name" => "付/回款金额",
            "type" => "price",
            "operators" => array(
                "between" => "介于",
                "=" => "等于",
                ">" => "大于",
                "<" => "小于",
            ),
        ),
        'r_type' => array(
            'name' => '类型',
            'type' => 'select',
            'operators' => array(
                '=' => '等于'
            ),
            'options' => array(
                '0' => '-请选择-',
                '1' => '收入',
                '2' => '支出'
            )
        ),
        'r_accdate' => array(
            'name' => '记账月',
            'type' => 'text',
            'operators' => array(
                'like' => '包含'
            )
        ),
        'r_noused' => array(
            'name' => '未对应订单金额',
            'type' => 'price',
            'operators' => array(
                'between' => '介于',
                '=' => '等于',
                '>' => '大于',
                '<' => '小于'
            )
        )
    );

    public function getList($search = array(), $limit = 20)
    {
        $member = $this->getCurrent();
        $this->alias('r');
        if ($member->m_isAdmin == "2") {
            $this->join('__CIRCULATION__ c', "r.r_id = c.ci_otid AND c.ci_type = 'received'");
            $this->where("c.ci_mid|i.i_mid", "=", $member->m_id);
        }
        $this->field("r.*");
        foreach ($search as $key => $value) {
            $this->where("r." . $value['field'], $value['opt'], $value['val']);
        }
        if($limit===false){
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
            if ($tmp['r_noused'] - $money < 0) {
                $return = false;
            } else {
                $this->save(['r_noused' => ($tmp['r_noused'] - $money), 'r_used' => ($tmp['r_used'] + $money)], ["r_id" => $id]);
            }
        } else {
            $this->save(['r_noused' => ($tmp['r_noused'] + $money), 'r_used' => ($tmp['r_used'] - $money)], ["r_id" => $id]);
        }
        return $return;
    }

    public function getCodeAndNameById($id)
    {
        $data = $this->getDataById($id);
        return ['code'=>$data['r_no'],'name'=>'付款单'.$data['r_no']];
    }

    public function doImport($dataset=false)
    {
        $mMember = new Member();
        $mCompany = new Company();
        foreach ($dataset as $key=>$data){

            if(isset($data['r_mname']) && isset($data['d_name'])){
                $member = $mMember->getMemberByName($data['r_mname'],$data['d_name']);
                if($member) $data['r_mid'] = $member->m_id;
                unset($data['d_name']);
            }elseif (isset($data['r_mname'])){
                $member = $mMember->getMemberByName($data['r_mname']);
                if($member) $data['r_mid'] = $member->m_id;
            }

            if(isset($data['r_coname']) && $data['r_coname']){
                $type = $data['r_type'] == 2 ? 1 : 2;
                $c = $mCompany->getCompany(false,$data['r_coname'],$type);
                if($c){
                    $data['r_coid'] = $c->co_id;
                }else{
                    $data['r_coid'] = 0;
                }
            }

            $data['r_noused'] = $data['r_money'];
            $dataset[$key] = $data;
        }
        return $this->insertDuplicate($dataset);
    }

}