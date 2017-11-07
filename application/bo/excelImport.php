<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/10/8
 * Time: 下午4:41
 */

return [
    'boExcel' => [
        'department' => [
            'file' => 'uploads/xlsx/员工.xlsx',
            'index' => 1,
            'fields' => [
                'd_cname' => 'A',
                'd_name' => 'B',
                'd_code' => 'C',
                'm_name' => 'D',
                'm_code' => 'E'
            ]
        ],
        'project' => [
            'file' => 'uploads/xlsx/主数据.xlsx',
            'index' => 2,
            'fields' => [
                'p_no' => 'B',
                'p_name' => 'C',
                'p_type' => 'D'
            ]
        ],
        'member' => [
            'file' => 'uploads/xlsx/人事员工.xlsx',
            'index' => 6,
            'fields' => [
                'm_code' => 'B',
                'm_department' => 'H',
                'm_office' => 'G',
                'm_name' => 'C',
                'm_phone' => 'D',
                'm_email' => 'E',
                'm_cname' => 'F'
            ]
        ],
        'supplier' => [
            'file' => 'uploads/xlsx/主数据.xlsx',
            'model' => 'company',
            'index' => 3,
            'fields' => [
                'co_code' => 'A',
                'co_name' => 'B',
                'co_remark' => 'C',
                'co_mnemonic_code' => 'D',
                'co_industry' => 'E',
                'co_address' => 'F',
                'co_internal_flag' => 'G',
                'co_tax_id' => 'H',
                'co_reg_id' => 'I',
                'co_lr' => 'J',
                'co_status' => 'K',
                'co_internal_name' => 'L',
                'co_create_time' => 'M',
                'co_flag' => 'N',
                'co_create_org' => 'O'
            ],
            'dateFields' => ['co_create_time'],
            'enumFields' => [
                'co_internal_flag' => [
                    '否' => 0,
                    'default' => 1
                ],
                'co_status' => [
                    '禁用' => 0,
                    'default' => 1
                ],
                'co_flag' => [
                    '否' => 0,
                    'default' => 1
                ]
            ],
            'defaultFields' => [
                'co_type' => 1
            ]
        ],
        'customer' => [
            'file' => 'uploads/xlsx/主数据.xlsx',
            'model' => 'company',
            'index' => 4,
            'fields' => [
                'co_code' => 'A',
                'co_name' => 'B',
                'co_remark' => 'C',
                'co_mnemonic_code' => 'D',
                'co_industry' => 'E',
                'co_address' => 'F',
                'co_internal_flag' => 'G',
                'co_internal_name' => 'H',
                'co_tax_id' => 'I',
                'co_reg_id' => 'J',
                'co_lr' => 'K',
                'co_status' => 'L',
                'co_create_org' => 'M',
                'co_create_time' => 'N'
            ],
            'dateFields' => ['co_create_time'],
            'enumFields' => [
                'co_internal_flag' => [
                    '否' => 0,
                    'default' => 1
                ],
                'co_status' => [
                    '禁用' => 0,
                    'default' => 1
                ]
            ],
            'defaultFields' => [
                'co_type' => 2
            ]
        ],
        'purchase-contract' => [ //采销、采购合同
            'file' => 'uploads/xlsx/采购跟踪表1031最终.xls',
            'model' => 'contract',
            'index' => 2,
            'fields' => [
                //'c_pname' => '',
                'c_money' => 'M',
                'c_coname' => 'E',
                'co_code' => 'X',
                'c_mname' => 'B',
                //'d_name' => 'A',
                'c_date' => 'N',
                'c_no' => 'U',
                'c_name' => 'V',
                'c_type' => 'L',
                'd_name' => 'A',
                'p_no' => 'C',
                'status' => 'P',
            ],
            'dateFields' => ['c_date'],
            'moneyFields' => ['c_money'],
            'enumFields' => [
                'c_type' => [
                    '采购合同' => 2,
                    '销售合同' => 1,
                    '销'      => 1,
                    '购'      => 2,
                    'default' => 0
                ]
            ]
        ],
        'sales-contract' => [ //主数据 - 销售合同
            'file' => 'uploads/xlsx/主数据.xlsx',
            'model' => 'contract',
            'index' => 5,
            'fields' => [
                'c_pname' => 'E',
                'c_money' => 'F',
                'c_coname' => 'H',
                'c_mname' => 'B',
                'c_date' => 'M',
                'c_no' => 'O',
                'c_name' => 'P',
                'c_bakup' => 'L',
                'd_name' => 'C',
                'p_no' => 'D'
            ],
            'dateFields' => ['c_date'],
            'defaultFields' => ['c_type' => 1],
            'moneyFields' => ['c_money']
        ],
        'order-chance' =>[ //主数据商机表
            'file' => 'uploads/xlsx/采购跟踪表1031最终.xls',
            'model' => 'orders',
            'index' => 2,
            'fields' => [
                'o_no' => 'X',
                'o_mname' => 'B',
                'm_department' => 'A',
                'p_no' => 'C',
                'o_pname' => 'D',
                'o_type' => 'L',
                'o_lie' => 'X',
                'o_coname' => 'E',
                'o_dname' => 'A',
                'o_date' => 'N',
                'o_money' => 'M',
                'o_cno'  => 'U',
                //'o_deal' => 'I',
                'o_status' => 'P'
            ],
            'dateFields' => ['o_date'],
            'moneyFields' => ['o_money'],
            'enumFields' => [
                'o_type' => [
                    '销' => 1,
                    'default' => 2
                ],
                'o_lie' => [
                    '1' => 1,
                    'default' => 2
                ],
                'o_status' => [
                    '0接洽' => 1,
                    '1意向' => 2,
                    '2立项' => 3,
                    '3发标' => 4,
                    '4定标' => 5,
                    '5合同' => 6,
                    'C合同' => 6,
                    'default' => 1,
                ],
                'flag' => [
                    'C合同' => 1,
                    'default' => 0
                ]
            ],
            'validate' => [
                'o_status' => 6
            ]
        ],
        'order-purchase-chance' => [ //采购商机表
            'extends' => 'order-purchase',
            'index' => 1,
            'fields' => [
                'unextends' => ['o_cno']
            ]
        ],
        'order-sales-chance' => [ //销售商机表
            'extends' => 'order-purchase-chance',
            'index' => 2
        ],
        'order-purchase' => [ //采购合同跟踪表
            'file' => 'uploads/xlsx/商务合同跟踪表及商机表.xls',
            'extends' => 'order-chance',
            'index' => 0,
            'fields' => [
                'o_mname' => 'B',
                'm_department' => 'A',
                'p_no' => 'C',
                'o_pname' => 'D',
                'o_type' => 'L',
                'o_lie' => 'G',
                'o_coname' => 'E',
                'o_dname' => 'A',
                'o_date' => 'N',
                'o_money' => 'M',
                'o_deal' => 'H',
                'o_status' => 'I',
                'flag' => 'P',
                'o_cno' => 'V',
                'unextends' => ['o_no']
            ],
        ],
        'order-sales' => [ //销售合同跟踪表
            'extends' => 'order-purchase',
            'index' => 3,
            'fields' => [
                'o_cno' => 'U'
            ]
        ],
        'purchase-invoice' => [ //主数据-财务部开票数据
            'file' => 'uploads/xlsx/最终版-主数据.xlsx',
            'model' => 'invoice',
            'index' => 1,
            'fields' => [
                'i_no' => 'X',
                'i_mname' => 'T',
                'i_content' => 'J',
                'i_coname' => 'D',
                'co_code' => 'C',
                'i_money' => 'F',
                'i_date' => 'Y',
                'p_no' => 'A',
                'c_no' => 'AB',
                'c_name' => 'AC'
            ],
            'dateFields' => ['i_date'],
            'defaultFields' => ['i_type'=>1,'o_csid'=>6,'o_csname'=>'C合同'],
            'moneyFields' => ['i_money']
        ],
        'invoice1107' => [ //11月3号导入发票
            'file' => 'uploads/xlsx/采购跟踪表1031最终.xls',
            'model' => 'invoice',
            'index' => 1,
            'fields' => [
                'i_no' => 'Y',
                'i_mname' => 'B',
                'i_content' => 'K',
                'i_coname' => 'D',
                //'co_code' => 'C',
                'i_money' => 'M',
                'i_date' => 'N',
                //'d_no' => 'A',
                'p_no' => 'C',
                'flag1' => 'P',
                'flag2' => 'Q'
            ],
            'dateFields' => ['i_date'],
            'defaultFields' => ['i_type'=>2,'i_tax'=>1,'co_code'=>''],
            'moneyFields' => ['i_money'],
            'validate' => [
                'flag1' => 'I开票',
                'flag2' => '1'
            ]
        ],
        'acceptance1107' => [ //11月3号导入发票
            'file' => 'uploads/xlsx/采购跟踪表1031最终.xls',
            'model' => 'acceptance',
            'index' => 1,
            'fields' => [
                'a_no' => 'Y',
                'a_mname' => 'B',
                'a_content' => 'K',
                'a_coname' => 'D',
                'a_money' => 'M',
                'a_date' => 'N',
                'd_name' => 'A',
                'flag1' => 'P',
                'flag2' => 'Q'
            ],
            'dateFields' => ['a_date'],
            'defaultFields' => ['a_type'=>2],
            'moneyFields' => ['a_money'],
            'validate' => [
                'flag1' => 'D交付',
                'flag2' => '1'
            ]
        ],
        'received' => [ //11月7号导入付款单
            'file' => 'uploads/xlsx/采购跟踪表1031最终.xls',
            'model' => 'received',
            'index' => 1,
            'fields' => [
                'r_no' => 'Y',
                'r_mname' => 'B',
                'r_coname' => 'D',
                'r_money' => 'M',
                'r_date' => 'N',
                'd_name' => 'A',
                'flag1' => 'P',
                'flag2' => 'Q'
            ],
            'dateFields' => ['r_date'],
            'defaultFields' => ['r_type'=>2],
            'moneyFields' => ['r_money'],
            'validate' => [
                'flag1' => 'P付款',
                'flag2' => '1'
            ]
        ],
    ],
];