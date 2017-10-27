<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/10/24
 * Time: 下午2:09
 */

return [
    'boExcel' => [
        'orders' => [
            'A' => ['title'=>'部门','key'=>'o_dname'],
            'B' => ['title'=>'责任人','key'=>'o_mname'],
            'C' => ['title'=>'项目号','key'=>'p_no'],
            'D' => ['title'=>'项目名称','key'=>'o_pname'],
            'E' => ['title'=>'对方名称','key'=>'o_coname'],
            'F' => ['title'=>'关键字','key'=>'tags'],
            'G' => ['title'=>'内部','key'=>'o_lie'],
            'H' => ['title'=>'赢单机会','key'=>'cs_name'],
            'I' => ['title'=>'商机状态','key'=>'o_status'],
            'J' => ['title'=>'行动日','key'=>'o_date'],
            'K' => ['title'=>'购销','key'=>'o_type'],
            'L' => ['title'=>'发生金额','key'=>'op_used'],
            'M' => ['title'=>'发生日(计划)','key'=>'op_date'],
            'N' => ['title'=>'商务阶段','key'=>'op_type']
        ],
        'orders-contract' => [
            'A' => ['title'=>'部门','key'=>'o_dname'],
            'B' => ['title'=>'责任人','key'=>'o_mname'],
            'C' => ['title'=>'项目号','key'=>'p_no'],
            'D' => ['title'=>'项目名称','key'=>'o_pname'],
            'E' => ['title'=>'对方名称','key'=>'o_coname'],
            'F' => ['title'=>'关键字','key'=>'tags'],
            'G' => ['title'=>'内部','key'=>'o_lie'],
            'H' => ['title'=>'赢单机会','key'=>'cs_name'],
            'I' => ['title'=>'商机状态','key'=>'o_status'],
            'J' => ['title'=>'行动日','key'=>'o_date'],
            'K' => ['title'=>'购销','key'=>'o_type'],
            'L' => ['title'=>'发生金额','key'=>'ou_used'],
            'M' => ['title'=>'发生日','key'=>'ou_date'],
            'N' => ['title'=>'备注','key'=>'c_bakup'],
            'O' => ['title'=>'商务阶段','key'=>'ou_type'],
            //'P' => ['title'=>'原定日','key'=>''],
            //'Q' => ['title'=>'记账月','key'=>''],
            'P' => ['title'=>'合同号','key'=>'c_no'],
            'Q' => ['title'=>'合同名称','key'=>'c_name']
        ],
        'project' => [
            'A' => ['title'=>'项目编号','key'=>'p_no'],
            'B' => ['title'=>'项目名称','key'=>'p_name']
        ],
        'contract' => [
            'A' => ['title'=>'项目名称','key'=>'c_pname'],
            'B' => ['title'=>'合同编号','key'=>'c_no'],
            'C' => ['title'=>'合同名','key'=>'c_name'],
            'D' => ['title'=>'合同标的额（元）','key'=>'c_money'],
            'E' => ['title'=>'供应商/客户','key'=>'c_coname'],
            'F' => ['title'=>'商务工作流类别','key'=>'c_type','type'=>'type'],
            'G' => ['title'=>'合同签订时间','key'=>'c_date','type'=>'date'],
            'H' => ['title'=>'责任人','key'=>'c_mname']
        ],
        'acceptance' => [ //验收单
            'A' => ['title'=>'','key'=>'']
        ],
        'received' => [ //付款单

        ],
        'invoice' => [

        ]
    ],
];