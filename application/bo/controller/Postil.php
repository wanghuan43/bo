<?php

namespace app\bo\controller;

use app\bo\libs\BoController;
use think\Request;

class Postil extends BoController
{
    protected $pmodel;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->pmodel = new \app\bo\model\Postil();
    }

    public function index($opID)
    {
        $list = $this->pmodel->where("p_oid", "=", $opID)->order("p_createtime", "DESC")->paginate($this->limit);
        $this->assign("maxSize", $this->getMaxFileSize());
        $this->assign("extAllowed", implode(",", $this->extAllowed));
        $this->assign("lists", $list);
        $this->assign("empty", '<tr><td colspan="3">无数据</td></tr>');
        $this->assign("opID", $opID);
        return $this->fetch("postil/lists");
    }

    public function save($opID)
    {
        $post = Request::instance()->post();
        $data = [
            "p_oid" => $opID,
            "p_mid" => $this->current->m_id,
            "p_mname" => $this->current->m_name,
            "p_title" => $post['p_title'],
            "p_content" => $post['p_content'],
            "p_createtime" => time(),
        ];
        if ($_FILES['p_attachment']['size'] > 0) {
            $file = $this->saveFile($_FILES['p_attachment']);
            if (!$file['status']) {
                return $file;
            }else{
                $data['p_filename'] = $file['data']['filename'];
                $data['p_attachment'] = $file['data']['path'];
            }
        }
        $this->pmodel->save($data);
        return array("status" => true, "message" => "批注成功");
    }
}