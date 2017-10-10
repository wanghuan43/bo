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
            'index' => 5,
            'fields' => [
                'p_no' => 'B',
                'p_name' => 'C',
                'p_type' => 'D'
            ]
        ],
        'member' => [
            'file' => 'uploads/xlsx/主数据.xlsx',
            'index' => 8,
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
            'file' => 'uploads/xlsx/商务合同跟踪表及商机表.xls',
            'index' => 0,
            'fields' => [
                'c_pname' => 'D',
                'c_money' => 'M',
                'c_coname' => 'E',
                'c_mname' => 'B',
                'c_date' => 'N',
                'c_no' => 'V',
                'c_name' => 'W',
                'd_name' => 'A',
                'p_no' => 'C'
            ],
            'dateFields' => ['c_date'],
            'defaultFields' => ['c_type' => 2],
            'moneyFields' => ['c_money']
        ],
        'sales-contract' => [ //销售合同
            'file' => 'uploads/xlsx/商务合同跟踪表及商机表.xls',
            'index' => 3,
            'fields' => [
                'c_pname' => 'D',
                'c_money' => 'M',
                'c_coname' => 'E',
                'c_mname' => 'B',
                'c_date' => 'N',
                'c_no' => 'U',
                'c_name' => 'V',
                'd_name' => 'A',
                'p_no' => 'C'
            ],
            'dateFields' => ['c_date'],
            'defaultFields' => ['c_type' => 1],
            'moneyFields' => ['c_money']
        ],
        'purchase-invoice' => [ //采购发票
            'file' => 'uploads/xlsx/主数据.xlsx',
            'index' => 1,
            'fields' => [
                'i_no' => 'Y',
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
        'sales-invoice' => [ //销售发票

        ]
    ],
];