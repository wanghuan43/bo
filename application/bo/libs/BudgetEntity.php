<?php
/**
 * Created by PhpStorm.
 * User: jerry
 * Date: 2017/10/12
 * Time: 10:09
 */

namespace app\bo\libs;

use app\bo\model\BudgetColumn;
use app\bo\model\BudgetPermissions;
use app\bo\model\BudgetTable;
use app\bo\model\BudgetTemplate;
use think\Db;

class BudgetEntity extends BoModel
{
    protected $templateModel;
    protected $columnModel;
    protected $permissionsModel;
    protected $tableModel;
    protected $member;
    protected $searchable = [
        't_title' => [
            'name' => '模板名称',
            'type' => 'text',
            'operators' => [
                'like' => '包含',
                '=' => '等于'
            ]
        ],
        't_mname' => [
            'name' => '责任人',
            'type' => 'text',
            'operators' => [
                'like' => '包含',
                '=' => '等于'
            ]
        ],
        'create_time' => [
            'name' => '创建日期',
            'type' => 'date',
            'operators' => [
                'between' => '介于',
                '=' => '等于',
                '>' => '大于',
                '<' => '小于'
            ]
        ],
        'update_time' => [
            'name' => '修改日期',
            'type' => 'date',
            'operators' => [
                'between' => '介于',
                '=' => '等于',
                '>' => '大于',
                '<' => '小于'
            ]
        ]
    ];

    protected function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->templateModel = new BudgetTemplate();
        $this->columnModel = new BudgetColumn();
        $this->permissionsModel = new BudgetPermissions();
        $this->tableModel = new BudgetTable();
        $this->member = $this->getCurrent();
    }

    function saveTemplate($data)
    {
        $cols = $data['data'];
        $par = [
            "t_title" => $data['t_title'],
            "t_mid" => $this->member->m_id,
            "t_mname" => $this->member->m_name,
            "t_row" => $data['t_row'],
            "t_col" => $data['t_col'],
        ];
        if (!empty($data['tid'])) {
            $this->templateModel->isUpdate(true);
            $par["t_id"] = $data['tid'];
            $par['update_time'] = time();
        } else {
            $par['create_time'] = time();
            $par['update_time'] = $par['create_time'];
        }
        $result = $this->templateModel->save($par);
        if ($result) {
            $result = $this->columnModel->saveAll($this->formatColsToSQL($cols, $this->templateModel->t_id, 1));
        }
        return ($result ? true : false);
    }

    function getTemplateList($limit, $filter = false)
    {
        if ($this->member->m_isAdmin != 1) {
            $this->templateModel->where("t_mid", "=", $this->member->m_id);
        }
        $this->templateModel->order("update_time desc")->order("create_time desc");
        if ($filter !== false) {
            foreach ($filter as $key => $value) {
                $this->templateModel->where($key, $value['op'], $value['val']);
            }
        }
        if ($limit !== false) {
            $list = $this->templateModel->paginate($limit);
        } else {
            $list = $this->templateModel->select();
        }
        return $list;
    }

    function getTemplateByID($id, $needTable = false)
    {
        $model = $this->templateModel->where("t_id", "=", $id)->find();
        if ($needTable && $model) {
            $model->cols = $this->formatColsToJS($this->getColsByTid($model->t_id));
        }
        return $model;
    }

    function getTableList($limit, $filter = false)
    {
        if ($this->member->m_isAdmin != 1) {
            $this->tableModel->where("mid", "=", $this->member->m_id);
        }
        $this->tableModel->order("update_time desc")->order("create_time desc");
        if ($filter !== false) {
            foreach ($filter as $key => $value) {
                $this->tableModel->where($key, $value['op'], $value['val']);
            }
        }
        if ($limit !== false) {
            $list = $this->tableModel->paginate($limit);
        } else {
            $list = $this->tableModel->select();
        }
        return $list;
    }

    function saveTable($data)
    {
        $pcr = json_decode(urldecode($data['pcr']), true);
        unset($data['pcr']);
        if (!empty($data['id'])) {
            $this->tableModel->isUpdate(true);
            $data['update_time'] = time();
        } else {
            unset($data['id']);
            $data['create_time'] = time();
            $data['update_time'] = $data['create_time'];
            $data["mid"] = $this->member->m_id;
            $data["mname"] = $this->member->m_name;
        }
        $result = $this->tableModel->save($data);
        if ($result AND count($pcr) > 0) {
            $result = $this->saveTablePermissions($pcr, $this->tableModel->id, $this->tableModel->tid);
        }
        return ($result ? true : false);
    }

    function saveTablePermissions($data, $id, $tid)
    {
        $checkCols = $this->getColsByTid($id, 0);
        if (count($checkCols) == 0) {
            $sql = "REPLACE INTO __BUDGET_COLUMN__(`c_col`,`c_row`,`c_value`,`c_colspan`
                ,`c_rowspan`,`c_isTemplate`,`c_display`,`c_readonly`,`c_tid`) 
                SELECT `c_col`,`c_row`,`c_value`,`c_colspan`
                ,`c_rowspan`,0,`c_display`,`c_readonly`,'$id' FROM __BUDGET_COLUMN__ WHERE c_tid = " . $tid . " AND c_isTemplate = 1";
            $sql = Db::parseSqlTable($sql);
            Db::execute($sql);
            $cols = $this->getColsByTid($id, 0);
            $check = [];
            foreach ($cols as $val) {
                $key = $val['c_col'] . "-" . $val['c_row'];
                $check[$key] = $val['c_id'];
            }
        }
        $this->permissionsModel->where("tid", "=", $id)->delete();
        $lists = [];
        foreach ($data as $value) {
            $key = $value['col'] . "-" . $value['row'];
            $cid = isset($check[$key]) ? $check[$key] : $value['cid'];
            $tmp = [
                "tid" => $id,
                "mid" => $value['mid'],
                "cid" => $cid,
                "rw" => $value['rw'],
            ];
            $lists[] = $tmp;
        }
        return $this->permissionsModel->saveAll($lists);
    }

    function getTableByID($id, $cols = false)
    {
        $model = $this->tableModel->where("id", "=", $id)->find();
        if ($cols AND $model) {
            $model->cols = $this->formatColsToJS($this->getColsByTid($model->id, 0));
        }
        return $model;
    }

    function getColsByTid($id, $isTemplate = 1)
    {
        return $this->columnModel->where("c_isTemplate", "=", $isTemplate)->where("c_tid", "=", $id)->select();
    }

    function formatColsToSQL($cols, $tid, $isTemplate = 0)
    {
        $colsTmp = [];
        foreach ($cols as $key => $value) {
            foreach ($value as $k => $val) {
                $tmp = [
                    "c_col" => $k,
                    "c_row" => $key,
                    "c_value" => $val['val'],
                    "c_colspan" => $val['colSpan'],
                    "c_rowspan" => $val['rowSpan'],
                    "c_display" => $val['display'],
                    "c_isTemplate" => $isTemplate,
                    "c_readonly" => $val['readonly'],
                    "c_tid" => $tid,
                ];
                if (!empty($val['cid'])) {
                    $tmp["c_id"] = $val['cid'];
                }
                $colsTmp[] = $tmp;
            }
        }
        return $colsTmp;
    }

    function formatColsToJS($cols)
    {
        $colsTmp = [];
        foreach ($cols as $val) {
            $colsTmp[$val['c_row']][] = $val;
        }
        return json_encode(array_values($colsTmp));
    }

    function getTableByTemplate($tid)
    {
        $data['table'] = $this->getTemplateByID($tid, true);
        $data['list'] = $this->tableModel->where("tid", "=", $tid)->select();
        return $data;
    }

    function getPermissionsByTable($id)
    {
        return $this->permissionsModel->where("tid", "=", $id)->select();
    }

    function getTableListByMidFromPermissions($mid, $limit)
    {
        if (empty($mid)) {
            return [];
        }
        $_REQUEST['mid'] = $mid;
        $lists = $this->tableModel->where("status", "=", "0")
            ->where("id", "in", function ($query) {
                $query->table("__BUDGET_PERMISSIONS__")->where("mid", "=", $_REQUEST['mid'])
                    ->field("tid")->group("tid");
            })->paginate($limit);
        return $lists;
    }

    function getColsListByMidFromPermissions($mid, $id, $limit)
    {
        if (empty($mid)) {
            return [];
        }
        $_REQUEST['mid'] = $mid;
        $this->columnModel->alias("kbc")->join('__BUDGET_PERMISSIONS__ kbp', "kbc.c_id = kbp.cid", "LEFT")
            ->where("kbp.mid", "=", $mid)->where("kbp.tid", "=", $id)->field("kbc.c_id,kbc.c_value,kbp.*");
        if ($limit) {
            $lists = $this->columnModel->paginate($limit);
        } else {
            $lists = $this->columnModel->select();
        }
        return $lists;
    }

    function saveColValue($data)
    {
        return $this->columnModel->saveAll($data, true);
    }
}