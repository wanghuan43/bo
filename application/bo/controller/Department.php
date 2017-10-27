<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/9/26
 * Time: 下午2:24
 */

namespace app\bo\controller;


use app\bo\libs\BoController;
use think\Request;

class Department extends BoController
{

    public function __construct(Request $request)
    {
        $this->model = new \app\bo\model\Department();
        parent::__construct($request);
    }

    public function searchDepartment()
    {
        $this->assign("type", "department");
        return $this->search($this->model);
    }

    public function doAdd()
    {

        $post = $this->request->post();
        $data['d_name'] = $post['name'];
        $data['d_code'] = $post['code'];
        $data['m_name'] = $post['mname'];
        $data['m_code'] = $post['mcode'];

        $validate = new \app\bo\validate\Department();

        if ($validate->check($data)) {
            if ($this->model->insert($data)) {
                $ret = ['flag' => 1, 'msg' => '添加成功'];
            } else {
                $ret = ['flag' => 0, 'msg' => '添加失败'];
            }
        } else {
            $ret = ['flag' => 0, 'msg' => $validate->getError()];
        }

        return $ret;

    }

    public function detail($id)
    {
        $data = $this->model->getDataById($id);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function update()
    {
        $post = $this->request->post();

        $data = [
            'd_name' => $post['name'],
            'd_code' => $post['code'],
            'm_name' => $post['mname'],
            'm_code' => $post['mcode']
        ];

        if ( $this->model->save($data, ['d_id'=>$post['id']]) ) {
            $ret = ['flag' => 1, 'msg' => '更新成功'];
        } else {
            $ret = ['flag' => 0, 'msg' => '更新失败'];
        }

        return $ret;
    }

    public function export()
    {
        return $this->doExport();
    }

}