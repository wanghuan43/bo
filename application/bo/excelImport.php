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
                'p_pay' => 'E',
                'p_income' => 'F',
                'p_date' => 'G',
                'p_content'=>'H'
            ],
            'dateFields' => ['p_date'],
            'desFields' => ['p_content'],
            'moneyFields' => ['p_income', 'p_pay']
        ],
        'member' => [
            'file' => 'uploads/xlsx/人事员工.xlsx',
            'index' => 6,
            'fields' => [
                'm_code' => 'A',
                'm_name' => 'B',
                'm_phone' => 'C',
                'm_email' => 'D',
                'm_cname' => 'E',
                'm_office' => 'F',
                'm_department' => 'G',
                'm_isAdmin' => 'H'
            ],
            'enumFields' => [
                'm_isAdmin' => [
                    '是' => '1',
                    'default' => '2'
                ]
            ]
        ],
        'supplier' => [
            'file' => 'uploads/xlsx/主数据.xlsx',
            'model' => 'company',
            'index' => 3,
            'fields' => [
                'co_code' => 'A',
                'co_name' => 'B',
                'co_tax_id' => 'C', //税务登记号
                'co_address' => 'D',
                'co_phone' => 'E',
                'co_bank' => 'F',
                'co_bank_card' => 'G',
                'co_invoice_type' => 'H',
                'co_invoice_address' => 'I',
                'co_invoice_recipient' => 'J',//发票接收人L
                'co_invoice_phone' => 'K', //发票接收人电话
                'co_remark' => 'L',
                'co_mnemonic_code' => 'M', //助记码
                'co_industry' => 'N', //行业
                'co_internal_flag' => 'O', //内部
                'co_reg_id' => 'P', //工商注册号
                'co_lr' => 'Q', //法人代表
                'co_status' => 'R', //状态 0 => 禁用，1=>核准
                'co_internal_name' => 'S', //集团内公司名称
                'co_create_time' => 'T',
                'co_flag' => 'U', //委外商 0=>否,1=>是
                'co_create_org' => 'V' //创建管理单元/创建组织
            ],
            'dateFields' => ['co_create_time'],
            'enumFields' => [
                'co_internal_flag' => [
                    '是' => 1,
                    'default' => 0
                ],
                'co_status' => [
                    '禁用' => 0,
                    'default' => 1
                ],
                'co_flag' => [
                    '是' => 1,
                    'default' => 0
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
                'co_tax_id' => 'C', //税务登记号
                'co_address' => 'D',
                'co_phone' => 'E',
                'co_bank' => 'F',
                'co_bank_card' => 'G',
                'co_invoice_type' => 'H',
                'co_invoice_address' => 'I',
                'co_invoice_recipient' => 'J',//发票接收人L
                'co_invoice_phone' => 'K', //发票接收人电话
                'co_remark' => 'L',
                'co_mnemonic_code' => 'M', //助记码
                'co_industry' => 'N', //行业
                'co_internal_flag' => 'O', //内部
                'co_reg_id' => 'P', //工商注册号
                'co_lr' => 'Q', //法人代表
                'co_status' => 'R', //状态 0 => 禁用，1=>核准
                'co_create_org' => 'S', //创建管理单元/创建组织
                'co_create_time' => 'T'
            ],
            'dateFields' => ['co_create_time'],
            'enumFields' => [
                'co_internal_flag' => [
                    '是' => 1,
                    'default' => 0
                ],
                'co_status' => [
                    '禁用' => 0,
                    'default' => 1
                ]
            ],
            'defaultFields' => [
                'co_type' => 2,
                'co_flag' => 0
            ]
        ],
        'contract' => [ //主数据 - 销售合同
            'file' => 'uploads/default/contract1202.xlsx',
            'model' => 'contract',
            'index' => 0,
            'fields' => [
                'c_no' => 'A',
                'c_dname' => 'B',
                'c_mname' => 'C',
                'c_name' => 'D',
                'c_type' => 'E',
                'c_coname' => 'F',
                'c_date' => 'G',
                'c_money' => 'H',
                'c_accdate' => 'I',
                'c_bakup' => 'J'
            ],
            'dateFields' => ['c_date'],
            'moneyFields' => ['c_money'],
            'desFields' => ['c_bakup'],
            'enumFields' => [
                'c_type' => [
                    '收入' => 1,
                    '支出' => 2,
                    'default' => 1
                ]
            ]
        ],
        'acceptance' => [
            'file' => 'uploads/xlsx/acceptance20171208.xlsx',
            'model' => 'acceptance',
            'index' => 0,
            'fields' => [
                'a_no' => 'A',
                'a_dname' => 'B',
                'a_mname' => 'C',
                'a_subject' => 'D',
                'a_type' => 'E',
                'a_coname' => 'F',
                'a_date' => 'G',
                'a_money' => 'H',
                'a_accdate' => 'I',
                'a_content' => 'J'
            ],
            'dateFields' => ['a_date'],
            'moneyFields' => ['a_money'],
            'desFields' => ['a_content'],
            'enumFields' => [
                'a_type' => [
                    '收入' => 1,
                    '支出' => 2,
                    'default' => 1
                ]
            ]
        ],
        'received' => [ //import new received
            'file' => 'uploads/default/received20171216.xlsx',
            'model' => 'received',
            'index' => 1,
            'fields' => [
                'r_no' => 'A',
                'r_mname' => 'C',
                'r_coname' => 'F',
                'r_type' => 'E',
                'r_money' => 'H',
                'r_date' => 'G',
                'r_subject' => 'D',
                'r_dname' => 'B',
                'r_accdate' => 'I',
                'r_content' => 'J'
            ],
            'dateFields' => ['r_date'],
            'moneyFields' => ['r_money'],
            'desFields' => ['r_content'],
            'enumFields' => [
                'r_type' => [
                    '收入' => 1,
                    '支出' => 2
                ]
            ]
        ],
        'invoice' => [
            'file' => 'uploads/xlsx/invoice20171208.xlsx',
            'model' => 'invoice',
            'index' => 0,
            'fields' => [
                'i_no' => 'A',
                'i_dname' => 'B',
                'i_mname' => 'C',
                'i_subject' => 'D',
                'i_type' => 'E',
                'i_coname' => 'F',
                'i_date' => 'G',
                'i_money' => 'H',
                'i_tax' => 'I',
                'i_accdate' => 'J',
                'i_content' => 'K'
            ],
            'dateFields' => ['i_date'],
            'moneyFields' => ['i_money'],
            'desFields' => ['i_content'],
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
        'received171216' => [ //update r_content
            'file' => 'uploads/default/received20171216.xlsx',
            'model' => 'received',
            'index' => 0,
            'fields' => [
                'r_no' => 'A',
                'r_mname' => 'C',
                'r_coname' => 'F',
                'r_type' => 'E',
                //'r_money' => 'H',
                'r_date' => 'G',
                'r_subject' => 'D',
                'r_content' => 'K',
                'r_dname' => 'B',
                'r_accdate' => 'J'
            ],
            'dateFields' => ['r_date'],
            'moneyFields' => ['r_money'],
            'desFields' => ['r_content'],
            'enumFields' => [
                'r_type' => [
                    '收入' => 1,
                    '支出' => 2
                ]
            ]
        ],

        'acceptance20171208' => [
            'file' => 'uploads/xlsx/acceptance20171208.xlsx',
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
        'invoice20171220' => [
            'file' => 'uploads/default/invoice20171220.xlsx',
            'model' => 'invoice',
            'index' => 0,
            'fields' => [
                'i_no' => 'A',
                'i_date' => 'B',
                'i_content' => 'C'
            ],
            'dateFields' => ['i_date']
        ],

        'orders20171208' => [
            'file' => 'uploads/default/orders20171214.xlsx',
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

        'orderstags' => [
            'file' => 'uploads/default/orders20171214.xlsx',
            'model' => 'taglink',
            'index' => 0,
            'fields' => [
                'o_no' => 'A',
                'tags' => 'N',
            ],
            'defaultFields' => [
                'model' => 'orders'
            ]
        ],

        'ordersforeign' =>[
            'file' => 'uploads/default/orders20171214.xlsx',
            'model' => 'orders',
            'index' =>0,
            'fields' => [
                'o_no' => 'A',
                'o_foreign' => 'Q'
            ]
        ]

    ],

];