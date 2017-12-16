<?php

namespace app\bo\libs;


use think\Config;

class DataImport
{

    const CONFIG_ROOT = 'boExcel';

    protected $cols = ['A','B','C','D','E','F','G',
                        'H','I','J','K','L','M','N',
                        'O','P','Q','R','S','T',
                        'U','V','W','X','Y','Z',
                        'AA','AB','AC','AD','AE','AF'];

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

        $config = Config::load(APP_PATH.'bo'.DS.'excelImport.php',self::CONFIG_ROOT);
        $configRoot = $config[self::CONFIG_ROOT];

        $config = $this->getConfig($type,$configRoot);

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
                $data[$key] = addslashes(trim($row[$cols[$val]]));
                if(isset($config['dateFields']) && in_array($key,$config['dateFields'])){
                    if($data[$key]) {
                        $data[$key] = strtotime($data[$key]);
                        if(!$data[$key]) {
                            throw new \Exception('日期列 '.$val.' 格式不对');
                        }
                    }
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
                if(isset($config['desFields']) && in_array($key,$config['desFields'])){
                    $data[$key] = str_replace('\\\\','\\',$data[$key]);
                    $data[$key] = str_replace('\\','\r\n',$data[$key]);
                }
            }

            if(isset($config['validate'])){
                foreach( $config['validate'] as $field=>$val ){
                    if( is_array($val) ){
                        if(!in_array($data[$field],$val)){
                            $data = false;
                        }
                    }else{
                        if($data[$field]!=$val){
                            $data = false;
                        }
                    }
                    unset($data[$field]);
                }
            }

            if( $data )
                $dataset[] = $data;

        }

        $res = null;

        if( isset($config['model']) ){
            $modelName = ucfirst($config['model']);
        }else{
            $modelName = ucfirst($type);
        }

        $class = '\\app\\bo\\model\\'.$modelName;

        $model = new $class();

        CustomUtils::writeImportLog('IMPORT START',$modelName);

        if($modelName == 'Orders' && isset($data['o_foreign'])){
            $res = $model->updateForeign($dataset);
        }else {
            $res = $model->import($dataset);
        }

        CustomUtils::writeImportLog('IMPORT END',$modelName);

        return $res;

    }

    protected function getConfig($name,$allConfig){
        if(isset($allConfig[$name])) {
            $config = $allConfig[$name];
            if (isset($config['extends'])) {
                $extends = $this->getConfig($config['extends'], $allConfig);
                $bak = $config;
                $config = $extends;
                foreach ($bak as $key => $value) {
                    if(is_array($value)){
                        $config[$key] = $value + $config[$key];
                        if(isset($config[$key]['unextends'])){
                            foreach ($config[$key]['unextends'] as $i){
                                unset($config[$key][$i]);
                            }
                            unset($config[$key]['unextends']);
                        }
                    }else {
                        $config[$key] = $value;
                    }
                }
            }
            return $config;
        }else{
            throw new \Exception('相关Excel配置错误');
        }
    }

}