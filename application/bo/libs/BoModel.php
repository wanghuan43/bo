<?php

namespace app\bo\libs;

use think\Model;

abstract class BoModel extends Model
{
    protected $searchable;

    protected $uploadFileValidateRule = [
        'size' => '2048000',
        'ext' => 'jpg,gif,jpeg,png,zip,rar,pdf,ppt,pptx,csv,xls,xlsx,doc,docx'
    ];

    public function getCurrent()
    {
        return getLoginMember();
    }

    /**
     * @return array
     */
    public function getSearchable()
    {
        return $this->searchable;
    }

    public function getUploadFileValidateRule()
    {
        return $this->uploadFileValidateRule;
    }

    public function getSearchableByKey($key)
    {
        if(empty($key)){
            return false;
        }
        if(!array_key_exists($key, $this->searchable)){
            return false;
        }
        return $this->searchable[$key];
    }

    public function __call($method, $args)
    {
        return parent::__call($method, $args); // TODO: Change the autogenerated stub
    }

    public function getCodeAndNameById($id)
    {
        return ['code' => '', 'name' => ''];
    }

    public function getDataById($id)
    {
        $pk = $this->pk ?: $this->getPk();
        return $this->where($pk, '=', $id)->find()->getData();
    }

    public function import($dataset){

        $validateClass = '\\app\\bo\\validate\\'.$this->name;

        if(class_exists($validateClass)){
            $validate = new $validateClass();
        }else{
            $validate = false;
        }

        $res = [];

        if($validate){
            foreach ($dataset as $key=>$data){
                if(!$validate->scene('import')->check($data)) {
                    $res[$key]['data'] = $data;
                    $res[$key]['msg'] = $validate->getError();
                    CustomUtils::writeImportLog('FAILED  - '.serialize($res[$key]),strtolower($this->name));
                    unset($dataset[$key]);
                }
            }
        }

        $ret['validate'] = $res;

        $ret['res'] = $this->doImport($dataset);

    }

    public function insertDuplicate($dataset=false)
    {

        if(empty($dataset)){
            return false;
        }
        $fields = $this->getFieldsType();
        $numCols = [];
        $numTypes = ['tinyint','smallint','mediumint','int','bigint','float','double','decimal'];
        foreach ($fields as $field=>$type) {
            $match = [];
            if(preg_match('/\w+/',$type,$match)) {
                $type = $match[0];
                if( in_array($type,$numTypes) ){
                    $numCols[] = $field;
                }
            }
        }
        $cols = array_keys($dataset[0]);
        $sqlPrev = "INSERT INTO `".$this->getTable()."` (".implode(',',$cols).") VALUES ";

        foreach($cols as $key => $col){
            $values[$key] = $col.'=VALUES('.$col.')';
        }
        $sqlSuffix = " ON DUPLICATE KEY UPDATE ".implode(',',$values);

        $i = 0;
        $values = '';

        $db = $this->getQuery();
        $db->startTrans();
        try {
            foreach ($dataset as $data) {

                if (!empty($values))
                    $values .= ",";

                $values .= "(" ;

                $vals = [];

                foreach($cols as $col){
                    if(isset($data[$col]) && in_array($col,$numCols)){
                        $vals[] = $data[$col];
                    }elseif (isset($data[$col])){
                        $vals[] = "'".$data[$col]."'";
                    }else{
                        $vals[] = "default";
                    }
                }
                $values .= implode(',',$vals);

                $values .= ")";
                $i++;
                if ($i%1000 == 0) { //1000条数据操作一次
                    $sql = $sqlPrev . $values . $sqlSuffix;
                    var_dump($sql);
                    CustomUtils::writeImportLog('SQL - '.$sql,strtolower($this->name));
                    $res[] = $db->query($sql);
                    $i = 0;
                    $values = '';
                }

            }

            if (!empty($values)) {
                $sql = $sqlPrev . $values . $sqlSuffix;//var_dump($sql);//die;
                var_dump($sql);
                CustomUtils::writeImportLog('SQL - '.$sql,strtolower($this->name));
                $res[] = $db->query($sql);
            }
            $db->commit();
            return $res;

        }catch (\Exception $e){
            $db->rollback();
            $log = 'Exception - '. $e->getMessage();
            CustomUtils::writeImportLog($log,strtotime($this->name));
            return false;
        }

    }

    public function getModelName(){
        return $this->name;
    }

}