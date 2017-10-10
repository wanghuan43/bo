<?php

namespace app\bo\libs;


use think\Config;

class DataImport
{

    protected $cols = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF'];

    /**
     * @param $type
     * @param bool $filePath
     * @param bool $sheetIndex
     * @return bool
     * @throws \Exception
     */
    public function excelImport($type, $filePath = false, $sheetIndex = false)
    {

        $type = strtolower($type);

        $config = Config::load(APP_PATH.'bo'.DS.'excelImport.php','boExcel');
        $config = $config['boExcel'];

        if(isset($config[$type])){
            $config = $config[$type];
        }else{
            throw new \Exception('没有相关Excel配置');
        }

        if(empty($filePath)){
            $filePath = $config['file'];
            $sheetIndex = $config['index'];
        }

        if (file_exists($filePath)) {
            $excelFile = $filePath;
        } elseif (file_exists(ROOT_PATH . $filePath)) {
            $excelFile = ROOT_PATH . $filePath;
        } else {
            throw new \Exception('Excel 文件不存在.');
        }

        $res = \PHPExcel_IOFactory::load($excelFile)->getSheet($sheetIndex)->toArray();

        unset($res[0]);

        $dataset = [];

        $cols = array_flip($this->cols);

        $cnt = 0;

        foreach ($res as $row) {

            if(empty($row[0])){
                $cnt ++ ;
            }else{
                $cnt = 0;
            }

            if($cnt>5){
                break;
            }

            $data = [];

            if(isset($config['defaultFields'])){
                $data = $config['defaultFields'];
            }

            foreach ($config['fields'] as $key=>$val){
                $data[$key] = trim($row[$cols[$val]]);
                if(isset($config['dateFields']) && in_array($key,$config['dateFields'])){
                    $data[$key] = strtotime($data[$key]);
                }
                if(isset($config['moneyFields']) && in_array($key,$config['moneyFields'])){
                    $data[$key] = floatval(str_replace(',', '', $data[$key]));
                }
                if(isset($config['enumFields'][$key])){
                    $enum = $config['enumFields'][$key];
                    if(isset($enum[$data[$key]])){
                        $data[$key] = $enum[$data[$key]];
                    }else{
                        $data[$key] = $enum['default'];
                    }
                }
            }

            $dataset[] = $data;

        }

        $res = null;

        if($type == 'supplier' || $type == 'customer'){
            $class = '\\app\\bo\\model\\Company';
        }elseif ($type == 'purchase-contract' || $type == 'sales-contract'){
            $class = '\\app\\bo\\model\\Contract';
        }elseif ($type == 'purchase-invoice' || $type == 'sales-invoice'){
            $class = '\\app\\bo\\model\\Invoice';
        }else {
            $class = '\\app\\bo\\model\\' . ucfirst($type);
        }

        $model = new $class();

        return $model->import($dataset);

    }

}