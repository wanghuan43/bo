<?php
namespace app\bo\libs;

use think\Db;

class DataImport
{
    /**
     * 
     * @param string $tableName 表名，带不带前缀皆可
     * @param string $filePath Excel路径，eg：uploads/xlsx/default.xlsx | /opt/sites/bo/uploads/xlsx/default.xlsx
     * @param number $sheetIndex 
     * @param string $arg0 额外参数
     * @throws \Exception
     * @return boolean
     */
    public static function excelImport( $tableName,$filePath,$sheetIndex=0,$arg0=FALSE )
    {
        
        if( file_exists($filePath)){
            $excelFile = $filePath;
        }elseif( file_exists(ROOT_PATH.$filePath) ){
            $excelFile = ROOT_PATH.$filePath;
        }else{
            throw new \Exception('Excel 文件不存在.');
        }
        
        $res  = \PHPExcel_IOFactory::load($excelFile)->getSheet($sheetIndex)->toArray();
        
        $dbPrefix = config('database.prefix');
        
        if( strpos($tableName, $dbPrefix) !== 0 ){
            $tableSuffix = $tableName;
            $tableName = $dbPrefix.$tableName;
        }else{
            $tableSuffix = substr( $tableName, strlen( $dbPrefix ) );
        }
      
        $db = db($tableSuffix);
        
        Db::startTrans();
        
        try{
            foreach( $res as $key => $row ){
                if( $key === 0 ) continue;
                if( $tableSuffix == 'project'){
                    if( trim( $row[3] ) == '项目编号' )
                        $list[] = [ 'p_no'=>trim($row[1]),'p_name'=>trim($row[2]) ];
                }elseif( $tableSuffix == 'contract' ){
                    $data = [
                        'c_pname' => trim($row[6]),
                        'c_no' => trim( $row[1] ),
                        'c_name' => trim( $row[2] ),
                        'c_money' => floatval(trim($row[3])),
                        'c_date' => strtotime(trim($row[9])),
                        'c_coname' => trim( $row[4] )
                    ];
                    $data['c_type'] = 1;
                    if( trim($row[7])=='采购合同' ){
                        $data['c_type'] = 2;
                    }
                    if( isset($pArr[trim($row[5])]) ){
                        $data['c_pid'] = $pArr[trim($row[5])] ;    
                    }else{
                        $proj = db('project')->where('p_no',trim($row[5]))->field('p_id')->find();
                        if( empty($proj) ) continue;
                        $data['c_pid'] = $pArr[trim($row[5])] = $proj['p_id'];
                    }
                    $data['c_coname'] = $companyName = trim( $row[4] );
                    if( isset($companys[$companyName]) ){
                        $data['c_coid'] = $companys[$companyName];
                    }else{
                        $co = db('company')->where('co_name',$companyName)->field('co_id')->find();
                        if( empty($co) ) continue;
                        $data['c_coid'] = $companys[$companyName] = $co['co_id'];
                    }
                    $data['c_createtime'] = $data['c_updatetime'] = $data['c_date'];
                    $list[] = $data;
                }elseif ($tableSuffix=='company'){
                    if( $arg0 == 1 || $arg0 == TRUE ){//客户
                        $data = [
                                'co_type' => 2,
                                'co_code' => trim($row[0]),
                                'co_name' => trim($row[1]),
                                'co_remark' => trim($row[2]),
                                'co_mnemonic_code' => trim($row[3]),
                                'co_industry' => trim($row[4]),
                                'co_address' => trim($row[5]),
                                'co_internal_flag' => trim($row[6]) == '否'?0:1,
                                'co_internal_name' => trim($row[7]),
                                'co_tax_id' => trim($row[8]),
                                'co_reg_id' => trim($row[9]),
                                'co_lr' => trim($row[10]),
                                'co_status' => trim($row[11]) == '禁用'?0:1,
                                'co_create_org' => trim($row[12]),
                                'co_create_time' => strtotime(trim($row[13]))
                        ];     
                    }else{//供应商
                        $data = [
                                'co_code' => trim($row[0]),
                                'co_name' => trim($row[1]),
                                'co_remark' => trim($row[2]),
                                'co_type' => 1,
                                'co_mnemonic_code' => trim($row[3]),
                                'co_industry' => trim($row[4]),
                                'co_address' => trim($row[5]),
                                'co_internal_flag' => trim($row[6]) =='否'?0:1,
                                'co_tax_id' => trim($row[7]),
                                'co_reg_id' => trim($row[8]),
                                'co_lr' => trim($row[9]),
                                'co_status' => trim($row[10]) == '禁用'?0:1,
                                'co_internal_name' => trim($row[11]),
                                'co_flag' => trim($row[13])=='否'?0:1,
                                'co_create_org' => trim($row[14]),
                                'co_create_time' => strtotime(trim($row[12]))
                        ];    
                    }
                    $list[] = $data;
                }else{
                    return FALSE;
                }
            
                if( $key%1000 == 0 ){
                    $db->insertAll( $list );
                    $list = [];
                }elseif( $key == count($res)-1 ){
                    $db->insertAll( $list );
                }
            }
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            var_dump( $e );
            // 回滚事务
            Db::rollback();
        }
    }
    
}