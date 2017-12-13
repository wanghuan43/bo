<?php

namespace app\bo\model;

use app\bo\libs\BoModel;
use app\bo\libs\CustomUtils;

class Invoice extends BoModel
{
    protected $pk = "i_id";

    protected $searchable = array(
        'i_no' => array(
            'name' => '发票号',
            'type' => 'text',
            'operators' => array(
                'like' => '包含'
            )
        ),
        'i_coname' => array(
            'name' => '对方名称',
            'type' => 'text',
            'operators' => array(
                'like' => '包含'
            )
        ),
        'i_subject' => array(
            'name' => '摘要',
            'type' => 'text',
            'operators' => array(
                'like' => '包含'
            )
        ),
        "i_date" => array(
            "name" => "发票日期",
            "type" => "date",
            "operators" => array(
                "between" => "介于",
                "=" => "等于",
                ">" => "大于",
                "<" => "小于",
            ),
        ),
        "i_money" => array(
            "name" => "金额",
            "type" => "price",
            "operators" => array(
                "between" => "介于",
                "=" => "等于",
                ">" => "大于",
                "<" => "小于",
            ),
        ),
        'i_noused' => array(
            'name' => '未对应订单金额',
            'type' => 'price',
            'operators' => array(
                'between' => '介于',
                '=' => '等于',
                '>' => '大于',
                '<' => '小于'
            )
        ),
        'i_tax' => array(
            'name' => '税率',
            'type' => 'select',
            'operators' => array(
                '=' => '等于',
            ),
            'options' => array(
                '0' => '-请选择-',
                '1' => '3%',
                '2' => '5%',
                '3' => '6%',
                '4' => '11%',
                '5' => '13%',
                '6' => '17%',
                '7' => '普票'
            )
        ),
        'i_type' => array(
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
        )
    );

    public function getList($search, $limit)
    {
        $member = $this->getCurrent();
        if ($member->m_isAdmin == "2") {
            $c = new Circulation();
            $c->alias("c")->field('i.*')->join("__INVOICE__ i", "c.ci_otid = i.i_id", "LEFT")
                ->where("c.ci_type", "=", "invoice")->where("c.ci_mid|i.i_mid", "=", $member->m_id);
            foreach ($search as $key => $value) {
                $c->where("i." . $value['field'], $value['opt'], $value['val']);
            }
            if ($limit === false) {
                $list = $c->select();
            } else {
                $list = $c->paginate($limit);
            }
        } else {
            $this->alias("i")->field("i.*");
            foreach ($search as $key => $value) {
                $this->where("i." . $value['field'], $value['opt'], $value['val']);
            }
            if ($limit === false) {
                $list = $this->select();
            } else {
                $list = $this->paginate($limit);
            }
        }
        return $list;
    }

    public function checkUsed($id, $money, $op = "-")
    {
        $tmp = $this->find($id);
        if (!$tmp) {
            return true;
        } else {
            $tmp = $tmp->toArray();
        }
        $return = true;
        if ($op == "-") {
            if ($tmp['i_noused'] - $money < 0) {
                $return = false;
            } else {
                $this->save(['i_noused' => ($tmp['i_noused'] - $money), 'i_used' => ($tmp['i_used'] + $money)], ["i_id" => $id]);
            }
        } else {
            $this->save(['i_noused' => ($tmp['i_noused'] + $money), 'i_used' => ($tmp['i_used'] - $money)], ["i_id" => $id]);
        }
        return $return;
    }

    public function getCodeAndNameById($id)
    {
        $data = $this->getDataById($id);
        return ['code' => $data['i_no'], 'name' => '发票' . $data['i_no']];
    }

    /**
     * @param $dataset
     * @return array|bool
     */
    protected function doImport($dataset)
    {

        $mCompany = new Company();
        $mMember = new Member();
        $mDepartment = new Department();

        foreach ($dataset as $key => $data) {

            $did = $mDepartment->getDepartmentIdByName($data['i_dname']);
            if (empty($did)) {
                CustomUtils::writeImportLog('Department ID is null - ' . serialize($data), strtolower($this->name));
                unset($dataset[$key]);
                continue;
            } else {
                $data['i_did'] = $did;
            }

            $type = $data['i_type'] == 1 ? 2 : 1;

            $company = $mCompany->getCompany(false, $data['i_coname'], $type);

            if ($company) {
                $data['i_coid'] = $company->co_id;
            } else {
                CustomUtils::writeImportLog('Company ID is null - ' . serialize($data), strtolower($this->name));
                unset($dataset[$key]);
                continue;
            }

            $member = $mMember->getMemberByName($data['i_mname'], $data['i_dname']);

            if ($member) {
                $data['i_mid'] = $member->m_id;
            } else {
                CustomUtils::writeImportLog('Member ID is null - ' . serialize($data), strtolower($this->name));
                unset($dataset[$key]);
                continue;
            }

            $data['i_noused'] = $data['i_money'];
            $data['i_createtime'] = $data['i_updatetime'] = time();

            $dataset[$key] = $data;

        }

        return $this->insertDuplicate($dataset);

    }

}