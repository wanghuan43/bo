<?php
namespace app\bo\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use app\bo\libs\DataImport as LibImport;

class DataImport extends Command
{
    
    protected function configure()
    {
        $this->setName('import')->setDescription('从Excel (.xlsx) 导入数据到数据库.');
        $this->addArgument('type',Argument::REQUIRED);
    }
    
    protected function execute(Input $input, Output $output)
    {

        set_time_limit(0);
        ini_set("memory_limit", "1024M");
       
        $type = $input->getArgument('type');

        $import = new LibImport();

        $result = false;

        $import->excelImport($type,false,false,$result);

    }
    
}