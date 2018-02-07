<?php

namespace app\api\controller;

use app\api\helper\ParameterCheck;
use app\api\libs\ApiController;
use think\Request;

class Attachment extends ApiController
{
    private $check = [
        'download' => [
            'must' => ['attachment'],
        ],
    ];

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->controllers = "attachment";
    }

    public function download()
    {
        $this->functions = "download";
        $post = Request::instance()->post();
        $return = ParameterCheck::checkPost($post, $this->check['download']);
        if (empty($return['status'])) {
            $file = $_SERVER['DOCUMENT_ROOT'] . $post['attachment'];
            if (file_exists($file)) {
                $info = pathinfo($file);
                $fp = fopen($file, "r");
                //输入文件标签
                Header("Content-type: application/octet-stream");
                Header("Accept-Ranges: bytes");
                Header("Accept-Length: " . filesize($file));
                Header("Content-Disposition: attachment; filename=" . $info['basename']);
                //输出文件内容
                //读取文件内容并直接输出到浏览器
                echo fread($fp, filesize($file));
                fclose($fp);
                exit ();
            } else {
                $status = "10";
                $data = "无此文件";
            }
        } else {
            $status = $return['status'];
            $data = $return['data'];
        }
        $this->returnData($data, $status);
    }
}