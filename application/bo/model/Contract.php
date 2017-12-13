<?php

namespace app\bo\model;

use app\bo\libs\BoModel;
use app\bo\libs\CustomUtils;
use think\Request;

class Contract extends BoModel
{
    protected $pk = "c_id";

    protected $searchable = array(
        "c_name" => array(
            "name" => "合同名称",
            "type" => "text",
            "operators" => array(
                "like" => "包含"
            ),
        ),
        "c_no" => array(
            "name" => "合同号",
            "type" => "text",
            "operators" => array(
                "like" => "包含"
            ),
        ),
        "c_coname" => array(
            "name" => "对方名称",
            "type" => "text",
            "operators" => array(
                "like" => "包含"
            ),
        ),
        "c_money" => array(
            "name" => "合同金额",
            "type" => "price",
            "operators" => array(
                "between" => "介于",
                "=" => "等于",
                ">" => "大于",
                "<" => "小于",
            ),
        ),
        "c_date" => array(
            "name" => "签约日期",
            "type" => "date",
            "operators" => array(
                "between" => "介于",
                "=" => "等于",
                ">" => "大于",
                "<" => "小于",
            ),
        ),
        "c_type" => array(
            "name" => "类型",
            "type" => "select",
            "operators" => array(
                "=" => "等于",
            ),
            "options" => array(
                "0" => "-请选择-",
                "1" => "收入",
                "2" => "支出"
            )
        ),
        'c_accdate' => array(
            'name' => '记账月',
            'type' => 'text',
            'operators' => array(
                'like' => '包含'
            )
        ),
        'c_noused' => array(
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

    public function getList($search, $limit)
    {
        $member = $this->getCurrent();
        $this->alias('ct');
        if ($member->m_isAdmin == "2") {
            $this->join('__CIRCULATION__ c', "ct.c_id = c.ci_otid AND c.ci_type = 'contract'", 'left')
                ->where("c.ci_mid|ct.c_mid", "=", $member->m_id)->group('ct.c_id');
        }
        $this->field("ct.*,p.p_no,cp.co_type");
        $this->join('__PROJECT__ p', "ct.c_pid = p.p_id", 'left');
        $this->join('__COMPANY__ cp', "ct.c_coid = cp.co_id", 'left');
        foreach ($search as $key => $value) {
            $this->where("ct." . $value['field'], $value['opt'], $value['val']);
        }
        if ($limit == false) {
            $list = $this->select();
        } else {
            $list = $this->paginate($limit, false, array("query" => ["c_type" => Request::instance()->get("c_type")]));
        }

        return $list;
    }

    public function getCodeAndNameById($id)
    {
        $data = $this->getDataById($id);
        return ['code' => $data['c_no'], 'name' => $data['c_name']];
    }

    public function doImport($dataset)
    {
        $mProject = new Project();
        $mCompany = new Company();
        $mMember = new Member();
        $mDepartment = new Department();

        foreach ($dataset as $key => $data) {


            if (!empty($data['p_no'])) {
                $data['p_no'] = str_replace('-', '', $data['p_no']);
                $project = $mProject->where('p_no', '=', $data['p_no'])->find();
                if (empty($project) && isset($data['c_pname'])) {
                    $project = $mProject->getProject($data['p_no'], $data['c_pname']);
                }
                if ($project) {
                    $data['c_pid'] = $project->p_id;
                    $data['c_pname'] = $project->p_name;
                }
                unset($data['p_no']);
            }

            $co_type = $data['c_type'] == 1 ? 2 : 1;
            $co_code = isset($data['co_code']) ? $data['co_code'] : false;
            $co_name = isset($data['c_coname']) ? $data['c_coname'] : false;
            $company = $mCompany->getCompany($co_code, $co_name, $co_type);

            if (isset($data['co_code'])) unset($data['co_code']);

            if (!!$company) {
                $data['c_coid'] = $company->co_id;
            } else {
                CustomUtils::writeImportLog('Company ID is null - '.serialize($data),strtolower($this->name));
                unset($dataset[$key]);
                continue;
            }
            $member = $mMember->getMemberByName($data['c_mname'], $data['c_dname']);
            if (!!$member) {
                $data['c_mid'] = $member->m_id;
            }else{
                CustomUtils::writeImportLog('Member ID is null - '.serialize($data),strtolower($this->name));
                unset($dataset[$key]);
                continue;
            }

            $did = $mDepartment->getDepartmentIdByName($data['c_dname']);
            if(empty($did)){
                CustomUtils::writeImportLog('Department ID is null - '.serialize($data),strtolower($this->name));
                unset($dataset[$key]);
                continue;
            }else{
                $data['c_did'] = $did;
            }

            $data['c_createtime'] = $data['c_updatetime'] = time();
            $data['c_noused'] = $data['c_money'];
            $dataset[$key] = $data;
        }

        return parent::insertDuplicate($dataset); // TODO: Change the autogenerated stub
    }

}
