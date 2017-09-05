<?php
namespace app\bo\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use app\bo\model\Project;
use app\bo\libs\DataImport as LibImport;

class DataImport extends Command
{
    
    private $file = ROOT_PATH . 'uploads/xlsx/default.xlsx';
    private $index = 4;
    
    protected function configure()
    {
        $this->setName('import')->setDescription('从Excel (.xlsx) 导入数据到数据库.');
        $this->addArgument('table',Argument::REQUIRED,'project,');
        $this->addArgument('file', Argument::OPTIONAL);
        $this->addArgument('index', Argument::OPTIONAL);
        $this->addArgument('arg0',Argument::OPTIONAL);
    }
    
    protected function execute(Input $input, Output $output)
    {
        if( $fileName = $input->getArgument('file') ){
            $this->file = $fileName;
        }
        
        if( $index = $input->getArgument('index') ){
            $this->index = $index;
        }
        
        $tableName = $input->getArgument('table');
        
        $arg0 = $input->getArgument('arg0')?:FALSE;
        
        LibImport::excelImport($tableName, $this->file,$this->index,$arg0) ;

    }
    
}