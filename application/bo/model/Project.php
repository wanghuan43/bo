<?php

namespace app\bo\model;

use app\bo\libs\BoModel;
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
    );

    public function getList($search = array(), $limit = 20)
    {
        $member = $this->getCurrent();
        $this->alias('p');
        if ($member->m_isAdmin == "2") {
            $this->join('__CIRCULATION__ c', "p.p_id = c.ci_otid AND c.ci_type = 'project'");
            $this->where("c.ci_mid", "=", $member->m_id)->whereOr('p.p_mid','=',$member->m_id);
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

    public function import($dataset)
    {
        foreach ($dataset as $key => $data) {
            if ($data['p_type'] != '项目编号') {
                unset($dataset[$key]);
                continue;
            } else {
                unset($data['p_type']);
            }
            $mModel = new Member();
            $dModel =new Department();
            $m = $mModel->getMemberByName($data['p_mname']);

            if(!empty($m))
                $data['p_mid'] = $m->m_id;
            else
                $data['p_mid'] = 0;

            $did = $dModel->getDepartmentIdByName($data['p_dname']);
            if(empty($d)){
                $data['p_did'] = 0;
            }else{
                $data['p_did'] = $did;
            }

            $data['p_income'] = floatval($data['p_income']);
            $data['p_pay'] = floatval($data['p_pay']);

            $data['p_date'] = strtotime($data['p_date']);
            $data['p_createtime'] = $data['p_updatetime'] = time();

            $dataset[$key] = $data;

        }

        return $this->insertDuplicate($dataset);

    }

    /**
     * @param $dataset
     * @param bool $forceUpdate
     * @return int|void
     */
    protected function doImport($dataset, $forceUpdate = true)
    {

        if (empty($dataset)) return;

        if ($forceUpdate) {
            $sqlPrev = "INSERT INTO `kj_project` (`p_no`,`p_name`,`p_mid`,`p_mname`,`p_did`,`p_dname`,`p_income`,`p_pay`,`p_date`,`p_createtime`,`p_updatetime`) VALUES ";
            $sqlSuffix = " ON DUPLICATE KEY UPDATE `p_name`=VALUES(`p_name`,`p_mid`,`p_mname`,`p_did`,`p_dname`,`p_income`,`p_pay`,`p_date`,`p_createtime`,`p_updatetime`)";
        } else {
            $sqlPrev = "INSERT IGNORE INTO `kj_project` (`p_no`,`p_name`) VALUES ";
            $sqlSuffix = "";
        }

        $i = 0;
        $values = '';

        $db = $this->getQuery();
        $db->startTrans();
        try {
            foreach ($dataset as $data) {

                if (!empty($values))
                    $values .= ",";

                $values .= "('" . $data['p_no'] . "','" . $data['p_name'] . "',".$data['p_mid'].",'".$data['p_mname']."',".")";
                $i++;
                if ($i%1000 == 0) { //1000条数据操作一次
                    $sql = $sqlPrev . $values . $sqlSuffix;
                    $res[] = $db->query($sql);
                    $i = 0;
                    $values = '';
                }

            }

            if (!empty($values)) {
                $sql = $sqlPrev . $values . $sqlSuffix; //var_dump($sql);die;
                $res[] = $db->query($sql);
            }
            $db->commit();
            return $res;

        }catch (\Exception $e){
            $db->rollback();
            $log = 'Exception - '. $e->getMessage();
            CustomUtils::writeImportLog($log,'project');
        }

    }

    public function getProject($no, $name = false)
    {

        if (empty($no)) {
            $project = false;
        } else {
            $project = $this->where('p_no', '=', $no)->find();
            if (empty($project)) {
                $pid = $this->insertGetId(['p_no' => $no, 'p_name' => $name]);
                $project = new static(['p_id' => $pid, 'p_no' => $no, 'p_name' => $name]);
            }
        }

        return $project;

    }

}