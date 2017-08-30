<?php
/**
 * Created by PhpStorm.
 * User: jerry
 * Date: 2017/8/23
 * Time: 15:51
 */

use think\Config;

function getTypeList($key = "")
{
    Config::load(APP_PATH . "bo" . DS . "commonField.php", "", "commonField");
    $return = Config::get("type", "commonField");
    if (!empty($key)) {
        $return = $return[$key];
    }
    return $return;
}

function getTaxList($key = "")
{
    Config::load(APP_PATH . "bo" . DS . "commonField.php", "", "commonField");
    $return = Config::get("tax", "commonField");
    if (!empty($key)) {
        $return = $return[$key];
    }
    return $return;
}

function getLieList($key = "")
{
    Config::load(APP_PATH . "bo" . DS . "commonField.php", "", "commonField");
    $return = Config::get("lie", "commonField");
    if (!empty($key)) {
        $return = $return[$key];
    }
    return $return;
}


function getStatusList($key = "")
{
    Config::load(APP_PATH . "bo" . DS . "commonField.php", "", "commonField");
    $return = Config::get("status", "commonField");
    if (!empty($key)) {
        $return = $return[$key];
    }
    return $return;
}