<?php

namespace app\bo\model;

use app\bo\libs\BoModel;
use app\bo\libs\CustomUtils;
use think\Request;

class Company extends BoModel
{
    protected $pk = 'co_id';

    protected $searchable = array(
        "co_name" => array(
            "name" => "公司名称",
            "type" => "text",
            "operators" => array(
                "like" => "包含"
            ),
        ),
        "co_code" => array(
            "name" => "公司代码",
            "type" => "text",
            "operators" => array(
                "like" => "包含"
            ),
        )
    );

    public function getList($search, $limit)
    {
        $member = $this->getCurrent();
        $this->alias('co');
        if ($member->m_isAdmin == "2") {
            $this->join('__CIRCULATION__ c', "co.co_id = c.ci_otid", 'left');
            $this->where("c.ci_mid", "=", $member->m_id)->where('c.ci_type', "=", "company")
                ->group('co.co_id');
        }
        $this->field("co.*");
        foreach ($search as $key => $value) {
            $this->where("co." . $value['field'], $value['opt'], $value['val']);
        }
        if ($limit === false) {
            $list = $this->select();
        } else {
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
    public function getCompany($code = false, $name = false, $type = false)
    {


        if (!$code && !$name) {
            $res = false;
        } else {
            $this->where('co_status', '=', 1);

            if ($code) {
                $this->where('co_code', '=', $code);
            } elseif ($name) {
                $this->where('co_name', '=', $name);
            }

            if ($type) {
                $this->where('co_type', '=', $type);
            }

            $res = $this->find();
        }

        return $res;

    }

    protected function doImport($dataset, &$result, $forceUpdate)
    {

        foreach ($dataset as $key=>$data) {

            if (!$forceUpdate) {
                $coName = $data['co_name'];
                $coCode = $data['co_code'];
                $coStatus = isset($data['co_status']) && $data['co_status'] ? $data['co_status'] : 1;
                $coType = isset($data['co_type']) && $data['co_type'] ? $data['co_type'] : 1;
                $t = $coType == 1 ? '供应商' : '客户';
                $res = $this->where('co_name', '=', $coName)->where('co_code', '=', $coCode)
                    ->where('co_status', '=', $coStatus)->where('co_type', '=', $coType)->find();
                if ($res) {
                    CustomUtils::writeImportLog('Failed for unique key - ' . serialize($data), strtolower($this->name));
                    $result['failed'][] = array_merge($data, ['error' => $t . '已存在']);
                    unset($dataset[$key]);
                    continue;
                }

            }

            if(!isset($data['co_create_time']) || empty($data['co_create_time'])){
                $data['co_create_time'] = time();
            }
            $data['co_updatetime'] = time();

            $dataset[$key] = $data;

        }
        $result['success'] = $dataset;
        return $this->insertDuplicate($dataset);
    }

}