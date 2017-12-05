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
                'p_no' => 'A',
                'p_name' => 'B',
                'p_mname' => 'C',
                'p_dname' => 'D',
                'p_income' => 'E',
                'p_pay' => 'F',
                'p_date' => 'G'
            ],
            'dateFields' => ['p_date'],
            'moneyFields' => ['p_income','p_pay'],
            'defaultFields' => ['p_type'=>'项目编号']
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
        'contract' => [ //主数据 - 销售合同
            'file' => 'uploads/default/contract1202.xlsx',
            'model' => 'contract',
            'index' => 0,
            'fields' => [
                'c_money' => 'H',
                'c_coname' => 'F',
                'c_mname' => 'C',
                'c_date' => 'G',
                'c_no' => 'A',
                'c_name' => 'D',
                'c_type' => 'E',
                'c_accdate' => 'I',
                'c_dname' => 'B'
            ],
            'dateFields' => ['c_date'],
            'moneyFields' => ['c_money'],
            'enumFields' => [
                'c_type' => [
                    '收入' => 1,
                    '支出' => 2
                ]
            ]
        ],
        'received171202' => [
            'file' => 'uploads/xlsx/received.xlsx',
            'model' => 'received',
            'index' => 0,
            'fields' => [
                'r_no' => 'A',
                'r_mname' => 'C',
                'r_coname' => 'F',
                'r_type' => 'E',
                'r_money' => 'H',
                'r_date' => 'G',
                'r_subject' => 'D',
                'r_content' => 'K',
                'r_dname' => 'B',
                'r_accdate' => 'J'
            ],
            'dateFields' => ['r_date'],
            'moneyFields' => ['r_money'],
            'enumFields' => [
                'r_type' => [
                    '收入' => 1,
                    '支出' => 2
                ]
            ]
        ],

        'acceptance171202' => [
            'file' => 'uploads/default/acceptance.xlsx',
            'model' => 'acceptance',
            'index' => 0,
            'fields' => [
                'a_no' => 'A',
                'a_mname' => 'C',
                'a_subject' => 'D',
                //'a_content' => 'K',
                'a_coname' => 'F',
                'a_money' => 'H',
                'a_date' => 'G',
                'a_dname' => 'B',
                'a_type' => 'E',
                'a_accdate' => 'J'
            ],
            'dateFields' => ['a_date'],
            'moneyFields' => ['a_money'],
            'enumFields' => [
                'a_type' => [
                    '收入' => 1,
                    '支出' => 2
                ]
            ]
        ],
        'invoice171202' => [
            'file' => 'uploads/xlsx/invoice.xlsx',
            'model' => 'invoice',
            'index' => 0,
            'fields' => [
                'i_no' => 'A',
                'i_subject' => 'D',
                'i_coname' => 'F',
                'i_money' => 'H',
                'i_date' => 'G',
                'i_mname' => 'C',
                'i_type' => 'E',
                'i_tax' => 'I',
                'i_accdate' => 'J',
                'i_dname' => 'B'
            ],
            'dateFields' => ['i_date'],
            //'defaultFields' => ['co_code'=>''],
            'moneyFields' => ['i_money'],
            'enumFields' => [
                'i_type' => [
                    '收入' => 1,
                    '支出' => 2
                ],
                'i_tax' => [
                    '3%' => 1,
                    '5%' => 2,
                    '6%' => 3,
                    '11%' => 4,
                    '13' => 5,
                    '17%' => 6,
                    'default' => 7
                ]
            ]
        ],

        'orders171202' =>[
            'file' => 'uploads/default/orders.xlsx',
            'model' => 'orders',
            'index' => 0,
            'fields' => [
                'o_no' => 'A',
                'o_mname' => 'C',
                'p_no' => 'O',
                'o_subject' => 'D',
                'o_type' => 'E',
                'o_lie' => 'G',
                'o_coname' => 'F',
                'o_dname' => 'B',
                'o_date' => 'H',
                'o_money' => 'I',
                'o_num' => 'J',
                'o_deal' => 'L',
                'o_status' => 'M',
                'o_tax' => 'K'
            ],
            'dateFields' => ['o_date'],
            'moneyFields' => ['o_money'],
            'defaultFields' => ['o_pname' => ''],
            'enumFields' => [
                'o_type' => [
                    '收入' => 1,
                    'default' => 2
                ],
                'o_lie' => [
                    '内部' => 1,
                    'default' => 2
                ],
                'o_status' => [
                    '1接洽' => 1,
                    '2意向' => 2,
                    '3立项' => 3,
                    '4招标' => 4,
                    '5定标' => 5,
                    '6合同' => 5,
                    'default' => 1,
                ],
                'o_tax' => [
                    '3%' => 1,
                    '5%' => 2,
                    '6%' => 3,
                    '11%' => 4,
                    '13' => 5,
                    '17%' => 6,
                    'default' => 7
                ]
            ]
        ],

    ],

];