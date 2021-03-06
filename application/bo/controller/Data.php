<?php

namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\libs\DataImport;

class Data extends BoController
{

    protected $types = ['department', 'member', 'supplier', 'customer', 'project', 'contract','invoice','received','acceptance'];

    protected $typeNames = [
        'department' => '部门',
        'member' => '员工',
        'supplier' => '供应商',
        'customer' => '客户',
        'project' => '项目',
        'contract' => '合同',
        'invoice' => '发票',
        'received' => '付款单',
        'acceptance' => '验收单'
    ];

    public function import($type = FALSE)
    {

        set_time_limit(0);
        ini_set("memory_limit", "1024M");

        if (is_string($type)) {
            $type = strtolower($type);
        }

        if ($this->checkType($type)) {

            if ($this->request->isPost()) {

                $this->doImport($type);

                return $this->fetch('data' . DS . 'import' . DS . 'message');

            } else {
                $this->assign('type', $type);
                $this->assign('typeName', $this->typeNames[$type]);
                return $this->fetch('data' . DS . 'import' . DS . 'default');
            }

        } else {
            return $this->index();
        }

    }

    public function download($type = false)
    {

        if ($this->checkType($type)) {

            $filename = ROOT_PATH . 'uploads' . DS . 'default' . DS . $type . '.xlsx';

            $f = fopen($filename, "r");   // 以读取文件的方式 打开文件
            $str = fread($f, filesize($filename));  // 读取输入流中的数据

            fclose($f);   // 关闭文件
            // 设置浏览器 不以text/html的方式 解析响应数据
            // 设置浏览器 以输出流的方式 解析响应数据
            // 设置浏览器 以输出流的方式 解析响应数据
            Header("Content-type: application/octet-stream");
            // 设置浏览器下载文件的接收单位
            Header("Accept-Ranges: bytes");
            // 设置浏览器下载文件的长度
            Header("Accept-Length: " . filesize($filename));
            // 设置浏览器下载文件时的默认保存文件名
            Header("Content-Disposition: attachment; filename=" . $type . '.xlsx');

            echo $str;
            exit();

        } else {
            return '<h2 class="error">参数错误</h2>';
        }

    }

    public function index()
    {
        return $this->fetch('index');
    }

    /**
     * 执行上传并导入操作.
     * @param String $type
     * @return boolean
     */
    protected function doImport(String $type)
    {

        $file = $this->request->file('file');

        $info = $file->validate(['size' => 2000000, 'ext' => 'xlsx'])->move(ROOT_PATH . 'uploads' . DS . 'xlsx');

        $result = ['success'=>[],'failed'=>[],'warnings'=>[]];

        $errors = [];
        $warnings = [];

        if ($info) {

            $xlsx = ROOT_PATH . 'uploads' . DS . 'xlsx' . DS . $info->getSaveName();

            $import = new DataImport();

            try {

                $import->excelImport($type, $xlsx, 0, $result,false);

                if(!empty($result['failed'])) {
                    foreach ($result['failed'] as $res) {
                        $msg = $res['error'];
                        unset($res['error']);
                        $msg .= ' - ' . implode(',', $res);
                        $errors[] = $msg;
                    }
                }

                if(!empty($result['warnings'])) {
                    foreach ($result['warnings'] as $res) {
                        $msg = $res['warning'];
                        unset($res['warning']);
                        $msg .= ' - ' . implode(',', $res);
                        $warnings[] = $msg;
                    }
                }

            }catch (\Exception $e){

                $errors[] = $e->getMessage();

            }

        } else {

            $errors[] = $info->getError();

        }

        $sCnt = isset($result['success'])?count($result['success']):0;
        $fCnt = isset($result['failed'])?count($result['failed']):0;

        $this->assign('msgs',["导入结束! 成功 $sCnt 失败 $fCnt"]);
        $this->assign('errors', $errors );
        $this->assign('warnings',$warnings);

    }

    /**
     *
     * @param String $type
     * @return boolean
     */
    protected function checkType($type)
    {

        return in_array($type, $this->types);

    }

}