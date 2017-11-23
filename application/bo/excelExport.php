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
            'C' => ['title'=>'订单号','key'=>'o_no'],
            'D' => ['title'=>'项目号','key'=>'p_no'],
            'E' => ['title'=>'项目名称','key'=>'o_pname'],
            'F' => ['title'=>'对方名称','key'=>'o_coname'],
            'G' => ['title'=>'关键字','key'=>'tags'],
            'H' => ['title'=>'内部','key'=>'o_lie'],
            'I' => ['title'=>'赢单机会','key'=>'cs_name'],
            'J' => ['title'=>'商机状态','key'=>'o_status'],
            'K' => ['title'=>'行动日','key'=>'o_date'],
            'L' => ['title'=>'购销','key'=>'o_type'],
            'M' => ['title'=>'发生金额','key'=>'op_used'],
            'N' => ['title'=>'发生日(计划)','key'=>'op_date'],
            'O' => ['title'=>'商务阶段','key'=>'op_type']
        ],
        'orders-contract' => [
            'A' => ['title'=>'部门','key'=>'o_dname'],
            'B' => ['title'=>'责任人','key'=>'o_mname'],
            'C' => ['title'=>'订单号','key'=>'o_no'],
            'D' => ['title'=>'项目号','key'=>'p_no'],
            'E' => ['title'=>'项目名称','key'=>'o_pname'],
            'F' => ['title'=>'对方名称','key'=>'o_coname'],
            'G' => ['title'=>'关键字','key'=>'tags'],
            'H' => ['title'=>'内部','key'=>'o_lie'],
            'I' => ['title'=>'赢单机会','key'=>'cs_name'],
            'J' => ['title'=>'商机状态','key'=>'o_status'],
            'K' => ['title'=>'行动日','key'=>'o_date'],
            'L' => ['title'=>'购销','key'=>'o_type'],
            'M' => ['title'=>'发生金额','key'=>'ou_used'],
            'N' => ['title'=>'发生日','key'=>'ou_date'],
            'O' => ['title'=>'备注','key'=>'c_bakup'],
            'P' => ['title'=>'商务阶段','key'=>'ou_type'],
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
            'D' => ['title'=>'合同标的额（元）','key'=>'c_money','type'=>'money'],
            'E' => ['title'=>'供应商/客户','key'=>'c_coname'],
            'F' => ['title'=>'商务工作流类别','key'=>'c_type','type'=>'type'],
            'G' => ['title'=>'合同签订时间','key'=>'c_date','type'=>'date'],
            'H' => ['title'=>'责任人','key'=>'c_mname']
        ],
        'acceptance' => [ //验收单
            'A' => ['title'=>'验收单号','key'=>'a_no'],
            'B' => ['title'=>'责任人','key'=>'a_mname'],
            'C' => ['title'=>'描述','key'=>'a_content'],
            'D' => ['title'=>'类型','key'=>'a_type','type'=>'type'],
            'E' => ['title'=>'对方公司','key'=>'a_coname'],
            'F' => ['title'=>'总金额','key'=>'a_money','type'=>'money'],
            'G' => ['title'=>'已使用金额','key'=>'a_used','type'=>'money'],
            'H' => ['title'=>'未使用金额','key'=>'a_noused','type'=>'money'],
            'I' => ['title'=>'日期','key'=>'a_date','type'=>'date']
        ],
        'received' => [ //付款单
            'A' => ['title'=>'付款单号','key'=>'r_no'],
            'B' => ['title'=>'责任人','key'=>'r_mname'],
            'C' => ['title'=>'类型','key'=>'r_type','type'=>'type'],
            'D' => ['title'=>'对方公司','key'=>'r_coname'],
            'E' => ['title'=>'总金额','key'=>'r_money','type'=>'money'],
            'F' => ['title'=>'已使用金额','key'=>'r_used','type'=>'money'],
            'G' => ['title'=>'未使用金额','key'=>'r_noused','type'=>'money'],
            'H' => ['title'=>'日期','key'=>'r_date','type'=>'date']
        ],
        'invoice' => [ //发票
            'A' => ['title'=>'发票号','key'=>'i_no'],
            'B' => ['title'=>'责任人','key'=>'i_mname'],
            'C' => ['title'=>'类型','key'=>'i_type','type'=>'type'],
            'D' => ['title'=>'对方公司','key'=>'i_coname'],
            'E' => ['title'=>'总金额','key'=>'i_money','type'=>'money'],
            'F' => ['title'=>'已使用金额','key'=>'i_used','type'=>'money'],
            'G' => ['title'=>'未使用金额','key'=>'i_noused','type'=>'money'],
            'H' => ['title'=>'日期','key'=>'i_date','type'=>'date'],
            'I' => ['title'=>'描述','key'=>'i_content']
        ],
        'department' => [
            'A' => ['title'=>'成本中心编码','key'=>'d_code'],
            'B' => ['title'=>'科室名称','key'=>'d_name'],
            'C' => ['title'=>'公司名称','key'=>'d_cname'],
            'D' => ['title'=>'科室领导','key'=>'m_name'],
            'E' => ['title'=>'领导编码','key'=>'m_code']
        ],
        'member' => [
            'A' => ['title'=>'员工编码','key'=>'m_code'],
            'B' => ['title'=>'姓名','key'=>'m_name'],
            'C' => ['title'=>'邮箱','key'=>'m_email'],
            'D' => ['title'=>'手机','key'=>'m_phone'],
            'E' => ['title'=>'公司','key'=>'m_cname'],
            'F' => ['title'=>'部门','key'=>'m_office'],
            'G' => ['title'=>'科室','key'=>'m_department'],
            'H' => ['title'=>'管理员','key'=>'m_isAdmin','type'=>['0'=>'否','1'=>'是']]
        ],
        'company-1' => [ //供应商
            'A' => ['title'=>'编码','key'=>'co_code'],
            'B' => ['title'=>'名称','key'=>'co_name'],
            'C' => ['title'=>'内部供应商','key'=>'co_internal_flag','type'=>['0'=>'否','1'=>'是']],
            'D' => ['title'=>'助记码','key'=>'co_mnemonic_code'],
            'E' => ['title'=>'行业','key'=>'co_mnemonic_code'],
            'F' => ['title'=>'地址','key'=>'co_address'],
            'G' => ['title'=>'税务登记号','key'=>'co_tax_id'],
            'H' => ['title'=>'工商注册号','key'=>'co_reg_id'],
            'I' => ['title'=>'法人代表','key'=>'co_lr'],
            'J' => ['title'=>'状态','key'=>'co_status','type'=>['0'=>'禁用','1'=>'核准']],
            'K' => ['title'=>'集团内公司名称','key'=>'co_internal_name'],
            'L' => ['title'=>'创建管理单元','key'=>'co_create_org'],
            'M' => ['title'=>'创建时间','key'=>'co_create_time','type'=>'date'],
            'N' => ['title'=>'简称','key'=>'co_remark'],
            'O' => ['title'=>'委外商','key'=>'co_flag','type'=>['0'=>'否','1'=>'是']]
        ],
        'company-2' => [ //客户
            'A' => ['title'=>'编码','key'=>'co_code'],
            'B' => ['title'=>'名称','key'=>'co_name'],
            'C' => ['title'=>'内部供应商','key'=>'co_internal_flag','type'=>['0'=>'否','1'=>'是']],
            'D' => ['title'=>'助记码','key'=>'co_mnemonic_code'],
            'E' => ['title'=>'行业','key'=>'co_mnemonic_code'],
            'F' => ['title'=>'地址','key'=>'co_address'],
            'G' => ['title'=>'税务登记号','key'=>'co_tax_id'],
            'H' => ['title'=>'工商注册号','key'=>'co_reg_id'],
            'I' => ['title'=>'法人代表','key'=>'co_lr'],
            'J' => ['title'=>'状态','key'=>'co_status','type'=>['0'=>'禁用','1'=>'核准']],
            'K' => ['title'=>'集团内公司名称','key'=>'co_internal_name'],
            'L' => ['title'=>'创建组织','key'=>'co_create_org'],
            'M' => ['title'=>'创建时间','key'=>'co_create_time','type'=>'date'],
            'N' => ['title'=>'简称','key'=>'co_remark'],
        ]
    ],
];