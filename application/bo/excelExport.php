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
            'D' => ['title'=>'项目名称','key'=>'o_pname'],
            'E' => ['title'=>'对方名称','key'=>'o_coname'],
            'F' => ['title'=>'关键字','key'=>'tags'],
            'G' => ['title'=>'内部','key'=>'o_lie'],
            'H' => ['title'=>'赢单机会','key'=>'cs_name'],
            'I' => ['title'=>'商机状态','key'=>'o_status'],
            'J' => ['title'=>'行动日','key'=>'o_date'],
            'K' => ['title'=>'收支','key'=>'o_type'],
            'L' => ['title'=>'发生金额','key'=>'op_used'],
            'M' => ['title'=>'发生日(计划)','key'=>'op_date'],
            'N' => ['title'=>'备注','key'=>'c_bakup'],
            'O' => ['title'=>'商务阶段','key'=>'op_type'],
            'P' => ['title'=>'发生','key'=>'flag1'],
            'Q' => ['title'=>'合同号','key'=>'c_no'],
            'R' => ['title'=>'合同名称','key'=>'c_name'],
            'S' => ['title'=>'验收单号/发票号/付款号','key'=>'b_no']
        ],
        'orders-contract' => [
            'A' => ['title'=>'部门','key'=>'o_dname'],
            'B' => ['title'=>'责任人','key'=>'o_mname'],
            'C' => ['title'=>'订单号','key'=>'o_no'],
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
            'A' => ['title'=>'项目号','key'=>'p_no'],
            'B' => ['title'=>'项目名称','key'=>'p_name'],
            'C' => ['title'=>'责任人','key'=>'p_mname'],
            'D' => ['title'=>'立项部门','key'=>'p_dname'],
            'E' => ['title'=>'预计总收入','key'=>'p_income','type'=>'money'],
            'F' => ['title'=>'预计总支出','key'=>'p_pay','type'=>'money'],
            'G' => ['title'=>'立项时间','key'=>'p_date','type'=>'date'],
            'H' => ['title'=>'创建时间','key'=>'p_createtime','type'=>'date']
        ],
        'contract' => [
            'A' => ['title'=>'合同号','key'=>'c_no'],
            'B' => ['title'=>'部门','key'=>'c_dname'],
            'C' => ['title'=>'责任人','key'=>'c_mname'],
            'D' => ['title'=>'合同名','key'=>'c_name'],
            'E' => ['title'=>'类型','key'=>'c_type','type'=>'type'],
            'F' => ['title'=>'对方公司','key'=>'c_coname'],
            'G' => ['title'=>'签约日期','key'=>'c_date','type'=>'date'],
            'H' => ['title'=>'合同标的额（元）','key'=>'c_money','type'=>'money'],
            'I' => ['title'=>'未对应订单金额','key'=>'c_noused','type'=>'money'],
            'J' => ['title'=>'记账月','key'=>'c_accdate'],
            'K' => ['title'=>'说明','key'=>'c_bakup']
        ],
        'acceptance' => [ //验收单
            'A' => ['title'=>'验收单号','key'=>'a_no'],
            'B' => ['title'=>'责任人','key'=>'a_mname'],
            'C' => ['title'=>'摘要','key'=>'a_subject'],
            'D' => ['title'=>'类型','key'=>'a_type','type'=>'type'],
            'E' => ['title'=>'对方公司','key'=>'a_coname'],
            'F' => ['title'=>'总金额','key'=>'a_money','type'=>'money'],
            'G' => ['title'=>'已使用金额','key'=>'a_used','type'=>'money'],
            'H' => ['title'=>'未使用金额','key'=>'a_noused','type'=>'money'],
            'I' => ['title'=>'日期','key'=>'a_date','type'=>'date'],
            'J' => ['title'=>'记账月','key'=>'a_accdate']
        ],
        'received' => [ //付款单
            'A' => ['title'=>'付款单号','key'=>'r_no'],
            'B' => ['title'=>'摘要','key'=>'r_subject'],
            'C' => ['title'=>'责任人','key'=>'r_mname'],
            'D' => ['title'=>'类型','key'=>'r_type','type'=>'type'],
            'E' => ['title'=>'对方公司','key'=>'r_coname'],
            'F' => ['title'=>'总金额','key'=>'r_money','type'=>'money'],
            'G' => ['title'=>'已使用金额','key'=>'r_used','type'=>'money'],
            'H' => ['title'=>'未使用金额','key'=>'r_noused','type'=>'money'],
            'I' => ['title'=>'日期','key'=>'r_date','type'=>'date'],
            'J' => ['title'=>'记账月','key'=>'r_accdate'],
            'K' => ['title'=>'说明','key'=>'r_content']
        ],
        'invoice' => [ //发票
            'A' => ['title'=>'发票号','key'=>'i_no'],
            'B' => ['title'=>'部门','key'=>'i_dname'],
            'C' => ['title'=>'责任人','key'=>'i_mname'],
            'D' => ['title'=>'摘要','key'=>'i_subject'],
            'E' => ['title'=>'类型','key'=>'i_type','type'=>'type'],
            'F' => ['title'=>'对方公司','key'=>'i_coname'],
            'G' => ['title'=>'开票日期','key'=>'i_date','type'=>'date'],
            'H' => ['title'=>'总金额','key'=>'i_money','type'=>'money'],
            'I' => ['title'=>'已使用金额','key'=>'i_used','type'=>'money'],
            'J' => ['title'=>'未使用金额','key'=>'i_noused','type'=>'money'],
            'K' => ['title'=>'税率','key'=>'i_tax','type'=>'tax'],
            'L' => ['title'=>'记账月','key'=>'i_accdate'],
            'M' => ['title'=>'说明','key'=>'i_content']
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
            'H' => ['title'=>'管理员','key'=>'m_isAdmin','type'=>['2'=>'否','1'=>'是']]
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