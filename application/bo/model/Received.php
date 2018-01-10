<?php

namespace app\bo\model;

use app\bo\libs\BoModel;
use app\bo\libs\CustomUtils;

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
        ),
        'r_mname' => array(
            'name' => '责任人',
            'type' => 'text',
            'operators' => array(
                'like' => '包含'
            )
        ),
        'r_subject' => array(
            'name' => '摘要',
            'type' => 'text',
            'operators' => array(
                'like' => '包含'
            )
        )
    );

    public function getList($search = array(), $limit = false,$aType=false,$trashed=2)
    {
        $member = $this->getCurrent();
        $this->alias('r');
        if ($member->m_isAdmin == "2") {
            $this->join('__CIRCULATION__ c', "r.r_id = c.ci_otid",'left');
            $this->where("(r.r_id IN (SELECT ci_otid FROM kj_circulation WHERE ci_mid = ".$member->m_id." and ci_type = 'received') OR r.r_mid = ".$member->m_id.")")
                ->group('r.r_id');
        }
        $this->field("r.*");
        foreach ($search as $key => $value) {
            $this->where("r." . $value['field'], $value['opt'], $value['val']);
        }
        $this->where('r_trashed','=',$trashed);
        if ($limit === false) {
            $list = $this->select();
        } else {
            $list = $this->paginate($limit);
        }
        return $list;
    }

    public function checkUsed($id, $money, $op = '-')
    {
        $tmp = $this->find($id);
        if (!$tmp) {
            return true;
        } else {
            $tmp = $tmp->toArray();
        }
        $return = true;
        if ($op == "-") {
            $noused = ($tmp['r_noused'] - $money);
            $used = ($tmp['r_used'] + $money);
        }else{
            $noused = ($tmp['r_noused'] + $money);
            $used = ($tmp['r_used'] - $money);
        }
        $this->update(['r_noused' => $noused, 'r_used' => $used], ["r_id" => $id]);
        return $return;
    }

    public function getCodeAndNameById($id)
    {
        $data = $this->getDataById($id);
        return ['code' => $data['r_no'], 'name' => '付款单' . $data['r_no']];
    }

    public function doImport($dataset = false,&$result,$forceUpdate)
    {
        $mMember = new Member();
        $mCompany = new Company();
        $mDepartment = new Department();

        foreach ($dataset as $key => $data) {

            if(empty($forceUpdate)){
                if($this->where('r_no','=',$data['r_no'])->find()){
                    $result['failed'][] = array_merge($data,['error'=>'付款单已存在']);
                }
            }

            $did = $mDepartment->getDepartmentIdByName($data['r_dname']);
            if(empty($did)){
                CustomUtils::writeImportLog('Department ID is null - '.serialize($data),strtolower($this->name));
                $data['error'] = '部门没找到';
                $result['failed'][] = $data;
                unset($dataset[$key]);
                continue;
            }else{
                $data['r_did'] = $did;
            }

            $member = $mMember->getMemberByName($data['r_mname'], $data['r_dname']);

            if (empty($member)) {
                CustomUtils::writeImportLog('Member ID is null - ' . serialize($data), strtolower($this->name));
                $data['error'] = '员工未找到';
                $result['failed'][] = $data;
                unset($dataset[$key]);
                continue;
            } else {
                $data['r_mid'] = $member->m_id;
            }

            $type = $data['r_type'] == 2 ? 1 : 2;
            $c = $mCompany->getCompany(false, $data['r_coname'], $type);
            if ($c) {
                $data['r_coid'] = $c->co_id;
            } else {
                CustomUtils::writeImportLog('Company ID is null - '.serialize($data),strtolower($this->name));
                $data['error'] = '对方公司未找到';
                $result['failed'][] = $data;
                unset($dataset[$key]);
                continue;
            }
            if(isset($data['r_money'])) {
                if(empty($data['r_money'])){
                    $result['warnings'][] = array_merge($data,['warning'=>'付款单金额为0']);
                }
                $data['r_noused'] = $data['r_money'];
                $data['r_createtime'] = $data['r_updatetime'] = time();
            }else{
                $data['r_updatetime'] = time();
            }

            if(empty($data['r_date'])){
                $result['warnings'][] = array_merge($data,['warning'=>'付款单日期为空']);
            }

            $dataset[$key] = $data;

        }

        $result['success'] = $dataset;

        return $this->insertDuplicate($dataset);

    }

    public function getTrashedField()
    {
        return 'r_trashed';
    }

}