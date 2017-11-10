<?php
/**
 * Created by PhpStorm.
 * User: jerry
 * Date: 2017/8/23
 * Time: 16:18
 */

return [
    "type" => [
        "1" => "收入",
        "2" => "支出",
    ],

    "tax" => [
        "1" => "3%",
        "2" => "5%",
        "3" => "6%",
        "4" => "11%",
        "5" => "13%",
        "6" => "17%",
        "7" => "普票"
    ],

    "status" => [
        "1" => "1接洽",
        "2" => "2意向",
        "3" => "3立项",
        "4" => "4招标",
        "5" => "5定标",
        "6" => "6合同",
    ],

    "lie" => [
        "2" => "外部",
        "1" => "内部",
    ],

    "month" => [
        "12 M",
        "11 M",
        "10 M",
        "9 M",
        "8 M",
        "7 M",
        "6 M",
        "5 M",
        "4 M",
        "3 M",
        "2 M",
        "1 M",
    ],
    "baseKey" => md5("xzy"),
    "permissionsMenu" => array(
        array(
            "name" => "权限",
            "flag" => "fa-cogs",
            "url" => "",
            "children" => array(
                array(
                    "name" => "设置权限",
                    "url" => "permissions/index",
                ),
            ),
        ),
        array(
            "name" => "菜单管理",
            "flag" => "fa-list",
            "url" => "",
            "children" => array(
                array(
                    "name" => "菜单列表",
                    "url" => "menu/index",
                ),
                array(
                    "name" => "添加菜单",
                    "url" => "menu/add",
                ),
            ),
        ),
    )
];