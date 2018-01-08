<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2018/1/8
 * Time: 下午2:22
 */

namespace app\bo\command;


use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
use think\Exception;

class Orders extends Command
{
    protected function configure()
    {
        $this->setName('orders')->setDescription('关于Orders的脚本');
        $this->addArgument('operator',Argument::OPTIONAL);
    }

    protected function execute(Input $input, Output $output)
    {

        set_time_limit(0);
        ini_set("memory_limit", "1024M");
        $operator = $input->getArgument('operator');

        if(empty($operator))
            $operator = 'formatOrderNo';

        if($operator == 'formatOrderNo'){
            $this->formatOrderNo();
        }

    }

    protected function formatOrderNo()
    {
        $model = new \app\bo\model\Orders();
        $res = $model->select();
        if(!empty($res)){
            foreach ($res as $order){
                $arr = explode('-',$order->o_no);
                $prefix = $arr[0];
                $suffix = $arr[1];
                if(strlen($suffix) == 4){
                    $suffix = substr($suffix,0,1).'0'.substr($suffix,1);
                    $oNo = $prefix.'-'.$suffix;
                    $res = $model->where('o_no','=',$oNo)->find();
                    if($res){
                        $oNo = $model->getOrderNO($order->o_pid,$order->o_type);
                    }
                    try {
                        if ($res = $model->save(['o_no' => $oNo, 'o_id' => $order->o_id,'o_date'=>$order->o_date], ['o_id' => $order->o_id])) {
                            echo "更新成功，".$arr[0]."-".$arr[1]."、".$oNo."\n";
                        }else{
                            echo "\n更新失败，".$arr[0]."-".$arr[1]."、".$oNo."\n";
                        }
                    }catch (Exception $e){
                        echo "\n";
                        echo $e->getMessage();
                        echo "\n";
                    }

                }
            }
        }
    }

}