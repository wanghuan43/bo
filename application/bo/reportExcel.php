<?php
/**
 * Created by PhpStorm.
 * User: jerry
 * Date: 2017/11/15
 * Time: 21:55
 */

return [
    "project" => [
        "项目号"=>["col"=>"p_no","by"=>"project"],
        "项目名称"=>["col"=>"p_name","by"=>"project"],
        "立项部门"=>["col"=>"p_dname","by"=>"project"],
        "立项责任人"=>["col"=>"p_mname","by"=>"project"],
        "立项日期"=>["col"=>"p_date","by"=>"project"],
        "立项预算总收入"=>["col"=>"p_income","by"=>"project"],
        "立项预算总支出"=>["col"=>"p_pay","by"=>"project"],
        "立项预算收入差额"=>["col"=>"p_ileft","by"=>"project"],
        "立项预算支出结余"=>["col"=>"p_pleft","by"=>"project"],
        "滚动预算利润(不含税)"=>["col"=>"p_total","by"=>"project"],
        "订单号"=>["col"=>"o_no","by"=>"orders"],
        "订单部门"=>["col"=>"o_dname","by"=>"orders"],
        "订单责任人"=>["col"=>"o_mname","by"=>"orders"],
        "内/外"=>["col"=>"o_lie","by"=>"orders"],
        "订单主题"=>["col"=>"o_subject","by"=>"orders"],
        "订单对方名称"=>["col"=>"o_coname","by"=>"orders"],
        "订单税率"=>["col"=>"o_tax","by"=>"orders"],
        "订单预计收入(含税)"=>["col"=>"o_money1_tax","by"=>"orders"],
        "订单预计支出(含税)"=>["col"=>"o_money2_tax","by"=>"orders"],
        "已签约收入(含税)"=>["col"=>"o_cmoney1_tax","by"=>"orders"],
        "已签约支出(含税)"=>["col"=>"o_cmoney2_tax","by"=>"orders"],
        "已签约收入(不含税)"=>["col"=>"o_cmoney1","by"=>"orders"],
        "已签约支出(不含税)"=>["col"=>"o_cmoney2","by"=>"orders"],
        "实际交付收入(含税)"=>["col"=>"o_amoney1_tax","by"=>"orders"],
        "实际交付支出(含税)"=>["col"=>"o_amoney2_tax","by"=>"orders"],
        "实际交付收入(不含税)"=>["col"=>"o_amoney1","by"=>"orders"],
        "实际交付支出(不含税)"=>["col"=>"o_amoney2","by"=>"orders"],
        "实际开票(含税)"=>["col"=>"o_imoney1_tax","by"=>"orders"],
        "实际回票(含税)"=>["col"=>"o_imoney2_tax","by"=>"orders"],
        "实际开票(不含税)"=>["col"=>"o_imoney1","by"=>"orders"],
        "实际回票(不含税)"=>["col"=>"o_imoney2","by"=>"orders"],
        "实际收款(含税)"=>["col"=>"o_rmoney1_tax","by"=>"orders"],
        "实际付款(含税)"=>["col"=>"o_rmoney2_tax","by"=>"orders"],
        "待签约收入(含税)"=>["col"=>"o_wmoney1","by"=>"orders"],
        "待签约支出(含税)"=>["col"=>"o_wmoney2","by"=>"orders"],
        "未实现开票(含税)"=>["col"=>"o_wimoney1","by"=>"orders"],
        "未实现回票(含税)"=>["col"=>"o_wimoney2","by"=>"orders"],
        "应收帐款(含税)"=>["col"=>"o_money1_tax","by"=>"orders"],
        "应付帐款(含税)"=>["col"=>"o_money2_tax","by"=>"orders"],
    ],
    "orders" => [
        "订单号"=>["col"=>"o_no","by"=>"orders"],
        "订单部门"=>["col"=>"o_dname","by"=>"orders"],
        "订单责任人"=>["col"=>"o_mname","by"=>"orders"],
        "所属项目名称"=>["col"=>"o_pname","by"=>"orders"],
        "内/外"=>["col"=>"o_lie","by"=>"orders"],
        "订单主题"=>["col"=>"o_subject","by"=>"orders"],
        "订单对方名称"=>["col"=>"o_coname","by"=>"orders"],
        "订单税率"=>["col"=>"o_tax","by"=>"orders"],
        "订单预计收入(含税)"=>["col"=>"o_money1_tax","by"=>"orders"],
        "订单预计支出(含税)"=>["col"=>"o_money2_tax","by"=>"orders"],
        "签约日期"=>["col"=>"o_date","by"=>"orders"],
        "已签约收入(含税)"=>["col"=>"o_cmoney1_tax","by"=>"orders"],
        "已签约支出(含税)"=>["col"=>"o_cmoney2_tax","by"=>"orders"],
        "已签约收入(不含税)"=>["col"=>"o_cmoney1","by"=>"orders"],
        "已签约支出(不含税)"=>["col"=>"o_cmoney2","by"=>"orders"],
        "最近交付日期"=>["col"=>"o_cr_date","by"=>"orders"],
        "实际交付收入(含税)"=>["col"=>"o_amoney1_tax","by"=>"orders"],
        "实际交付支出(含税)"=>["col"=>"o_amoney2_tax","by"=>"orders"],
        "实际交付收入(不含税)"=>["col"=>"o_amoney1","by"=>"orders"],
        "实际交付支出(不含税)"=>["col"=>"o_amoney2","by"=>"orders"],
        "最近开/回票日期"=>["col"=>"o_ci_date","by"=>"orders"],
        "实际开票(含税)"=>["col"=>"o_imoney1_tax","by"=>"orders"],
        "实际回票(含税)"=>["col"=>"o_imoney2_tax","by"=>"orders"],
        "实际开票(不含税)"=>["col"=>"o_imoney1","by"=>"orders"],
        "实际回票(不含税)"=>["col"=>"o_imoney2","by"=>"orders"],
        "最近收/付款日期"=>["col"=>"o_ca_date","by"=>"orders"],
        "实际收款(含税)"=>["col"=>"o_rmoney1_tax","by"=>"orders"],
        "实际付款(含税)"=>["col"=>"o_rmoney2_tax","by"=>"orders"],
        "待签约收入(含税)"=>["col"=>"o_wmoney1","by"=>"orders"],
        "待签约支出(含税)"=>["col"=>"o_wmoney2","by"=>"orders"],
        "未实现开票(含税)"=>["col"=>"o_wimoney1","by"=>"orders"],
        "未实现回票(含税)"=>["col"=>"o_wimoney2","by"=>"orders"],
        "应收帐款(含税)"=>["col"=>"o_money1_tax","by"=>"orders"],
        "应付帐款(含税)"=>["col"=>"o_money2_tax","by"=>"orders"],
    ],
    "contract" => [
        "合同号"=>["col"=>"c_no","by"=>"contract"],
        "合同名称"=>["col"=>"c_name","by"=>"contract"],
        "合同部门"=>["col"=>"c_dname","by"=>"contract"],
        "合同责任人"=>["col"=>"c_mname","by"=>"contract"],
        "合同对方名称"=>["col"=>"c_coname","by"=>"contract"],
        "合同日期"=>["col"=>"c_date","by"=>"contract"],
        "销售/采购"=>["col"=>"c_type","by"=>"contract"],
        "合同总金额"=>["col"=>"c_money","by"=>"contract"],
        "未关联订单的合同金额"=>["col"=>"c_noused","by"=>"contract"],
        "关联订单号"=>["col"=>"o_no","by"=>"orders"],
        "订单部门"=>["col"=>"o_dname","by"=>"orders"],
        "订单责任人"=>["col"=>"o_mname","by"=>"orders"],
        "内/外"=>["col"=>"o_lie","by"=>"orders"],
        "订单主题"=>["col"=>"o_subject","by"=>"orders"],
        "订单对方名称"=>["col"=>"o_coname","by"=>"orders"],
        "订单税率"=>["col"=>"o_tax","by"=>"orders"],
        "所属项目名称"=>["col"=>"o_pname","by"=>"orders"],
        "订单预计收入(含税)"=>["col"=>"o_money1_tax","by"=>"orders"],
        "最近交付日期"=>["col"=>"o_cr_date","by"=>"orders"],
        "实际交付收入(含税)"=>["col"=>"o_amoney1_tax","by"=>"orders"],
        "实际交付支出(含税)"=>["col"=>"o_amoney2_tax","by"=>"orders"],
        "最近开/回票日期"=>["col"=>"o_ci_date","by"=>"orders"],
        "实际开票(含税)"=>["col"=>"o_imoney1_tax","by"=>"orders"],
        "实际回票(含税)"=>["col"=>"o_imoney2_tax","by"=>"orders"],
        "最近收/付款日期"=>["col"=>"o_ca_date","by"=>"orders"],
        "实际收款(含税)"=>["col"=>"o_rmoney1_tax","by"=>"orders"],
        "实际付款(含税)"=>["col"=>"o_rmoney2_tax","by"=>"orders"],
        "未实现开票(含税)"=>["col"=>"o_wimoney1","by"=>"orders"],
        "未实现回票(含税)"=>["col"=>"o_wimoney2","by"=>"orders"],
        "应收帐款(含税)"=>["col"=>"o_money1_tax","by"=>"orders"],
        "应付帐款(含税)"=>["col"=>"o_money2_tax","by"=>"orders"],
    ],
    "invoice" => [
        "发票号"=>["col"=>"i_no","by"=>"invoice"],
        "发票摘要"=>["col"=>"i_content","by"=>"invoice"],
        "发票部门"=>["col"=>"i_dname","by"=>"invoice"],
        "发票责任人"=>["col"=>"i_mname","by"=>"invoice"],
        "发票对方名称"=>["col"=>"i_coname","by"=>"invoice"],
        "发票日期"=>["col"=>"i_date","by"=>"invoice"],
        "销售/采购"=>["col"=>"i_type","by"=>"invoice"],
        "发票税率"=>["col"=>"i_tax","by"=>"invoice"],
        "发票总金额"=>["col"=>"i_money","by"=>"invoice"],
        "未关联订单的发票金额"=>["col"=>"i_noused","by"=>"invoice"],
        "关联订单号"=>["col"=>"o_no","by"=>"orders"],
        "订单部门"=>["col"=>"o_dname","by"=>"orders"],
        "订单责任人"=>["col"=>"o_mname","by"=>"orders"],
        "订单主题"=>["col"=>"o_subject","by"=>"orders"],
        "订单对方名称"=>["col"=>"o_coname","by"=>"orders"],
        "关联订单金额"=>["col"=>"o_gmoney","by"=>"orders"],
        "所属项目名称"=>["col"=>"o_pname","by"=>"orders"],
    ],
    "received" => [
        "收/付款凭证号"=>["col"=>"r_no","by"=>"received"],
        "收/付款摘要"=>["col"=>"r_content","by"=>"received"],
        "部门"=>["col"=>"r_dname","by"=>"received"],
        "责任人"=>["col"=>"r_mname","by"=>"received"],
        "收/付款对方名称"=>["col"=>"r_coname","by"=>"received"],
        "收/付款日期"=>["col"=>"r_date","by"=>"received"],
        "收入/支出"=>["col"=>"r_type","by"=>"received"],
        "付款方式"=>["col"=>"r_payment","by"=>"received"],
        "收/付款总金额"=>["col"=>"r_money","by"=>"received"],
        "未关联订单的收/付款金额"=>["col"=>"r_noused","by"=>"received"],
        "关联订单号"=>["col"=>"o_no","by"=>"orders"],
        "订单部门"=>["col"=>"o_dname","by"=>"orders"],
        "订单责任人"=>["col"=>"o_mname","by"=>"orders"],
        "订单主题"=>["col"=>"o_subject","by"=>"orders"],
        "订单对方名称"=>["col"=>"o_coname","by"=>"orders"],
        "关联订单金额"=>["col"=>"o_gmoney","by"=>"orders"],
        "订单税率"=>["col"=>"o_tax","by"=>"orders"],
        "所属项目名称"=>["col"=>"o_pname","by"=>"orders"],
    ],
    "acceptance" => [
        "交付凭证号"=>["col"=>"a_no","by"=>"acceptance"],
        "交付摘要"=>["col"=>"a_content","by"=>"acceptance"],
        "交付部门"=>["col"=>"a_dname","by"=>"acceptance"],
        "交付责任人"=>["col"=>"a_mname","by"=>"acceptance"],
        "交付对方名称"=>["col"=>"a_coname","by"=>"acceptance"],
        "交付日期"=>["col"=>"a_date","by"=>"acceptance"],
        "销售/采购"=>["col"=>"a_type","by"=>"acceptance"],
        "交付方式"=>["col"=>"a_atype","by"=>"acceptance"],
        "交付总金额"=>["col"=>"a_money","by"=>"acceptance"],
        "未关联订单的交付金额"=>["col"=>"a_noused","by"=>"acceptance"],
        "关联订单号"=>["col"=>"o_no","by"=>"orders"],
        "订单部门"=>["col"=>"o_dname","by"=>"orders"],
        "订单责任人"=>["col"=>"o_mname","by"=>"orders"],
        "订单主题"=>["col"=>"o_subject","by"=>"orders"],
        "订单对方名称"=>["col"=>"o_coname","by"=>"orders"],
        "关联订单金额"=>["col"=>"o_gmoney","by"=>"orders"],
        "订单税率"=>["col"=>"o_tax","by"=>"orders"],
        "所属项目名称"=>["col"=>"o_pname","by"=>"orders"],
    ]
];