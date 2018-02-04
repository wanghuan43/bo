<?php
/**
 * Created by PhpStorm.
 * User: jerry
 * Date: 2018/2/4
 * Time: 05:34
 */

namespace app\api\helper;

use think\Db;

class ParameterCheck
{
    public static function checkPost(&$parameter, $check)
    {
        $return = [
            'status' => 0,
            'data' => ""
        ];
        if (isset($check['must']) AND is_array($check['must'])) {
            foreach ($check['must'] as $value) {
                if (!isset($parameter[$value])) {
                    $return['status'] = 90;
                    $return['data'] = $value . "为必填参数";
                }
            }
        }
        if (isset($check['default']) AND is_array($check['default'])) {
            foreach ($check['default'] as $key => $value) {
                if (!isset($parameter[$key])) {
                    $parameter[$key] = $value;
                }
            }
        }
        return $return;
    }

    public static function checkCollect($id, $mid, $type = "orders")
    {
        $temp = Db::name('favorite')->where("f_oid = " . $id . " AND f_mid = " . $mid)->find();
        return count($temp) <= 0 ? 0 : 1;
    }

    public static function getCirculations($id, $type = "orders")
    {
        return Db::name('circulation')
            ->alias('ci')
            ->field('m.m_code AS code,m.m_name AS name')
            ->join('__MEMBER__ m', 'ci.ci_mid = m.m_id', 'LEFT')
            ->where("ci.ci_otid = " . $id . " AND ci.ci_type = '" . $type . "'" )
            ->select();
    }

    public static function getTags($id, $type = "orders")
    {
        $temp = Db::name('taglink')
            ->alias('tl')
            ->field('t.tl_name')
            ->join('__TAGLIB__ t', 'tl.tl_id = t.tl_id', 'LEFT')
            ->where("tl.ot_id = " . $id . " AND tl.model = '" . $type . "'")
            ->select();
        $data = [];
        foreach ($temp as $value) {
            $data[] = $value['tl_name'];
        }
        return join(",", $data);
    }

    public static function getChance($id, $type = "orders")
    {
        return Db::name('chances')
            ->field("cs_name")
            ->where("cs_id = " . $id)
            ->find()['cs_name'];
    }
}