<?php

namespace app\api\libs;

use app\bo\libs\CustomUtils;
use think\Controller;
use think\Request;

class ApiController extends Controller
{
    public $controllers;
    public $functions;
    private $returnString;
    private $startTime;
    private $endTime;
    private $beginTime;

    public function __construct(Request $request)
    {
        $this->beginTime = date("Y-m-d H:i:s", time());
        $this->startTime = explode(' ', microtime());
        parent::__construct($request);
        $check = Request::instance()->isPost();
        if (!$check) {
            $this->returnData("只能以POST方式提交", "80");
        }
    }

    public function returnData($data, $status = "0", $type = "json")
    {
        switch ($type) {
            case "json":
                $this->returnJson($data, $status);
                break;
        }
        $this->endTime = explode(' ', microtime());
        $this->writeLog();
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET,POST,PUT,DELETE,OPTIONS");
        header("Access-Control-Allow-Headers:Accept, Origin, XRequestedWith, Content-Type, LastModified");
        echo $this->returnString;
        exit;
    }

    private function returnJson($data, $status = "0")
    {
        $this->returnString = json_encode(array("status" => $status, "data" => $data), JSON_UNESCAPED_UNICODE);
    }

    private function writeLog()
    {
        $useTime = $this->endTime[0] + $this->endTime[1] - ($this->startTime[0] + $this->startTime[1]);
        $useTime = round($useTime, 3);
        $base = RUNTIME_PATH . 'api';
        if (!is_dir($base)) {
            CustomUtils::mkdir_p($base);
        }
        $fileName = $this->controllers . date("Y-m-d") . ".log";
        $fullName = $base . DS . $fileName;
        $post = json_encode(Request::instance()->post());
        $message = <<<EOT
        
time:{$this->beginTime}
uri:{$this->controllers}/{$this->functions}
post:{$post}
return:{$this->returnString}
usetime:{$useTime}

EOT;
        file_put_contents($fullName, $message, FILE_APPEND);
    }
}