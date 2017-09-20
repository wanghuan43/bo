<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/9/18
 * Time: 上午11:01
 */

namespace app\bo\controller;


use app\bo\libs\BoController;
use think\Request;

class Circulation extends BoController
{

    public function __construct(Request $request)
    {
        $this->model = new \app\bo\model\Circulation();
        parent::__construct($request);
    }

    /**
     * @param bool $type
     * @return array
     */
    public function add($type = false)
    {

        $ids = $this->request->post('ids/a');
        $mids = $this->request->post('mids/a');

        if (!$type || !$ids || !$mids) {
            $ret = ['flag' => 0, 'msg' => '参数错误'];
        } else {

            $type = strtolower($type);
            foreach ($ids as $ot_id) {
                $this->model->setCirculation($ot_id, $mids, $type);
            }

            $ret = ['flag' => 1, 'msg' => '操作成功'];

        }

        return $ret;

    }

    public function list()
    {
        $params = $this->request->param();
        $type = $params['type'];
        $id = $params['id'];

        $lists = $this->model->getList($id, $type);

        $typeModel = model(ucfirst($type));

        $res = $typeModel->getCodeAndNameById($id);

        $this->assign('code', $res['code']);
        $this->assign('name', $res['name']);
        $this->assign('empty','<tr><td colspan="7">没有数据</td></tr>');

        $this->assign('lists', $lists);
        return $this->fetch();
    }

}