<?php

namespace app\bo\model;

use app\bo\libs\BoModel;
use app\bo\libs\CustomUtils;
use think\Exception;

class Project extends BoModel
{
    protected $pk = 'p_id';

    protected $searchable = array(
        "p_no" => array(
            "name" => "项目编号",
            "type" => "text",
            "operators" => array(
                "like" => "包含"
            ),
        ),
        "p_name" => array(
            "name" => "项目名称",
            "type" => "text",
            "operators" => array(
                "like" => "包含"
            ),
        ),
        'p_mname' => [
            'name' => '责任人',
            'type' => 'text',
            'operators' => ['like'=>'包含']
        ],
        'p_dname' => [
            'name' => '立项部门',
            'type' => 'text',
            'operators' => ['like'=>'包含']
        ],
        'p_income' => [
            'name' => '预计总收入',
            'type' => 'price',
            'operators' => [
                'between' => '介于',
                '=' => '等于',
                '>' => '大于',
                '<' => '小于'
            ]
        ],
        'p_pay' => [
            'name' => '预计总支出',
            'type' => 'price',
            'operators' => [
                'between' => '介于',
                '=' => '等于',
                '>' => '大于',
                '<' => '小于'
            ]
        ],
        'p_date' => [
            'name' => '立项日期',
            'type' => 'date',
            'operators' => [
                'between' => '介于',
                '=' => '等于',
                '>' => '大于',
                '<' => '小于'
            ]
        ]
    );

    public function getList($search = array(), $limit = 20)
    {
        $member = $this->getCurrent();
        $this->alias('p');
        if ($member->m_isAdmin == "2") {
            $this->join('__CIRCULATION__ c', "p.p_id = c.ci_otid AND c.ci_type = 'project'", 'left');
            $this->where("c.ci_mid|p.p_mid", "=", $member->m_id)->group('p.p_id');
        }
        $this->field("p.*");
        foreach ($search as $key => $value) {
            $this->where('p.' . $value['field'], $value['opt'], $value['val']);
        }
        if ($limit==false) {
            $list = $this->select();
        } else {
            $list = $this->paginate($limit);
        }
        return $list;
    }

    /**
     * @param $id
     * @return array
     */
    public function getCodeAndNameById($id)
    {
        $data = $this->getDataById($id);
        return ['code' => $data['p_no'], 'name' => $data['p_name']];
    }

    /**
     * @param $dataset
     * @return array|bool
     */
    protected function doImport($dataset)
    {
        $mModel = new Member();
        $dModel =new Department();

        foreach ($dataset as $key => $data) {
            if ($data['p_type'] != '项目编号') {
                unset($dataset[$key]);
                continue;
            } else {
                unset($data['p_type']);
            }

            $m = $mModel->getMemberByName($data['p_mname'],$data['p_dname']);

            if(!empty($m))
                $data['p_mid'] = $m->m_id;
            else{
                CustomUtils::writeImportLog('Member ID is null - '.serialize($data),strtolower($this->name));
                unset($dataset[$key]);
                continue;
            }

            $did = $dModel->getDepartmentIdByName($data['p_dname']);
            if(empty($did)){
                CustomUtils::writeImportLog('Department ID is null - '.serialize($data),strtolower($this->name));
                unset($dataset[$key]);
                continue;
            }else{
                $data['p_did'] = $did;
            }

            $data['p_income'] = floatval($data['p_income']);
            $data['p_pay'] = floatval($data['p_pay']);

            $data['p_createtime'] = $data['p_updatetime'] = time();

            $dataset[$key] = $data;

        }

        return $this->insertDuplicate($dataset);

    }

    public function getProject($no, $name = false)
    {
        $model = new self();
        if (empty($no)) {
            $project = false;
        } else {
            $project = $model->where('p_no', '=', $no)->find();
            if (empty($project)) {
                $pid = $model->insertGetId(['p_no' => $no, 'p_name' => $name]);
                $project = new static(['p_id' => $pid, 'p_no' => $no, 'p_name' => $name]);
            }
        }

        return $project;

    }

}