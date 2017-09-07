<?php
namespace app\bo\controller;

use think\Controller;
use think\Request;
use app\bo\libs\DataImport;

class Data extends Controller
{
    
    public function import( $type=FALSE )
    {
        if( is_string($type) ){
            $type = strtolower($type);
        }
     
        switch ($type){
            case FALSE:
                return $this->index();
                break;
            case 'department':
                return $this->importDepartment();
                break;
            case 'member':
                return $this->importMember();
                break;
        }
        
    }
    
    public function index()
    {
        return $this->fetch('index');
    }
    
    protected function importDepartment()
    {

        if( Request::instance()->isPost() ){
            $file = request()->file('file');
            $info = $file->validate(['size'=>2000000,'ext'=>'xlsx'])->move(ROOT_PATH . 'uploads' . DS . 'xlsx');
            
            if($info){
                $xlsx = ROOT_PATH . 'uploads' . DS . 'xlsx' . DS . $info->getSaveName();
                
                $res = DataImport::excelImport('department', $xlsx );
                
                $this->assign('msgs',['导入结束']);

            }else{
                $this->assign('errors', [$file->getError()]);
            }
            return $this->fetch('data/import/message');
        }else{
            return $this->fetch('data/import/department');
        }
    }
    
    protected function importMember()
    {
        return $this->fetch('data/import/member'); 
    }
    
    public function test()
    {
        echo 'aaa';die;
    }
    
}