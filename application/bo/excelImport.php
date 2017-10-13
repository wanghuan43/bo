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
            'file' => 'uploads/xlsx/主数据.xlsx',
            'index' => 0,
            'fields' => [
                'd_name' => 'A',
                'd_code' => 'B',
                'm_name' => 'C',
                'm_code' => 'D'
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
            'file' => 'uploads/xlsx/主数据.xlsx',
            'index' => 1,
            'fields' => [
                'm_code' => 'B',
                'm_department' => 'D',
                'm_office' => 'E',
                'm_name' => 'F',
                'm_phone' => 'G',
                'm_email' => 'H'
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
            'file' => 'uploads/xlsx/最终版-主数据.xlsx',
            'model' => 'contract',
            'index' => 6,
            'fields' => [
                //'c_pname' => '',
                'c_money' => 'E',
                'c_coname' => 'G',
                'co_code' => 'F',
                'c_mname' => 'J',
                'd_name' => 'K',
                'c_date' => 'I',
                'c_no' => 'C',
                'c_name' => 'D',
                'c_type' => 'H',
                'd_name' => 'K',
                'p_no' => 'A'
            ],
            'dateFields' => ['c_date'],
            'moneyFields' => ['c_money'],
            'enumFields' => [
                'c_type' => [
                    '采购合同' => 2,
                    '销售合同' => 1,
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
            'file' => 'uploads/xlsx/最终版-主数据.xlsx',
            'model' => 'orders',
            'index' => 7,
            'fields' => [
                'o_no' => 'A',
                'o_mname' => 'C',
                'm_department' => 'B',
                'p_no' => 'D',
                'o_pname' => 'E',
                'o_type' => 'M',
                'o_lie' => 'H',
                'o_coname' => 'F',
                'o_dname' => 'B',
                'o_date' => 'O',
                'o_money' => 'N',
                'o_deal' => 'I',
                'o_status' => 'J'
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
                    'default' => 1,
                ],
                'flag' => [
                    'C合同' => 1,
                    'default' => 0
                ]
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
        'purchase-invoice' => [ //主数据-销售发票
            'file' => 'uploads/xlsx/主数据.xlsx',
            'index' => 1,
            'fields' => [
                'i_no' => 'C',
                'i_mname' => 'T',
                'i_content' => 'J',
                'i_coname' => 'D',
                'co_code' => 'C',
                'i_money' => 'F',
                'i_date' => 'Z',
                'p_no' => 'A',
                'c_no' => 'AC',
                'c_name' => 'AD'
            ],
            'dateFields' => ['i_date'],
            'defaultFields' => ['i_type'=>1,'o_csid'=>6,'o_csname'=>'C合同'],
            'moneyFields' => ['i_money']
        ],
    ],
];