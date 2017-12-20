<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/10/11
 * Time: 下午5:28
 */

namespace app\bo\libs;


use think\Exception;

class CustomUtils
{

    const BASE_DIR = RUNTIME_PATH . 'customlog';

    /**
     * @param string $log  日志内容
     * @param bool $folder 相对目录
     * @param bool $fileName 日志文件名
     * @throws Exception
     */
    public static function writeLog($log='',$folder=false,$fileName=false){

        if(empty($fileName)){
            $fileName = date('Ymd') . '.log';
        }

        if($folder){
            $folder = self::BASE_DIR . DS . $folder ;
        }else{
            $folder = self::BASE_DIR;
        }

        if(!is_dir($folder) && !self::mkdir_p($folder) ){
            throw new Exception('创建文件夹失败');
        }

        if(!is_string($log)){
            $log = serialize($log);
        }

        $fileName = $folder . DS . $fileName ;

        $log = '[ '.date('Y-m-d H:i:s').' ]  ' . $log . "\n";

        file_put_contents($fileName,$log,FILE_APPEND);

    }

    public static function mkdir_p($dir,$dirmode=0755){
        $path = explode('/',str_replace('\\','/',$dir));
        $depth = count($path);
        for($i=$depth;$i>0;$i--){
            if(file_exists(implode('/',array_slice($path,0,$i)))){
                break;
            }
        }
        for($i;$i<$depth;$i++){
            if($d= implode('/',array_slice($path,0,$i+1))){
                if(!is_dir($d)) mkdir($d,$dirmode);
            }
        }
        return is_dir($dir);
    }

    public static function writeImportLog($log,$modelName){

        $folderName = 'import' . DS . strtolower($modelName);
        self::writeLog($log,$folderName);

    }


}