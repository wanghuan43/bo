<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\model\Orders;
use app\bo\model\Project as ModelProject;
use think\Request;


class Project extends BoController
{

    public function __construct(Request $request)
    {
        $this->model = new ModelProject();
        parent::__construct($request);
    }

    public function searchProject()
    {
        $this->assign("type", "project");
        return $this->search($this->model);
    }

    public function searchProjectNoList()
    {
        $this->assign("type", "project");
        $this->other = "main-pannel";
        return $this->search($this->model);
    }

    public function all()
    {
        $this->assign('empty','<tr><td colspan="3">没有数据</td></tr>');
        $this->assign('stype','project');
        $this->assign('title','所有项目');
        return parent::all();
    }

    /**
     * 添加
     * @return array
     */
    protected function doAdd()
    {
        $no = trim($this->request->post('no'));
        $name = trim($this->request->post('name'));

        $validate = validate('Project');

        $data = [
            'p_no'=>$no,
            'p_name'=>$name,
            'p_mname'=>$this->current->m_name,
            'p_mid'=>$this->current->m_id,
            'p_createtime'=>time()
        ];

        if( $validate->check($data) ) {

            if($this->model->save($data)){
                $ret = ['flag'=>1,'msg'=>'添加成功'];
            }else{
                $ret = ['flag'=>0,'msg'=>'添加失败'];
            }

        }else{
            $ret = ['flag'=>0,'msg'=>$validate->getError()];
        }

        return $ret;

    }

    public function detail($id)
    {
        $data = $this->model->getDataById($id);
        $mOrders = new Orders();
        $orders = $mOrders->where('o_pid','=',$id)->select();
        if(empty($orders)){
            $readonly = false;
        }else{
            $readonly = true;
        }
        $this->assign('readonly',$readonly);
        $this->assign('orders',$orders);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function update()
    {

        $post = $this->request->post();

        $id = $post['id'];

        $data = [
            'p_id' => $id,
            'p_no' => trim($post['no']),
            'p_name' => trim($post['name'])
        ];

        $validate = validate('Project');

        if($validate->check($data)) {
            $res = $this->model->save($data,['p_id'=>$id]);
            if ($res) {
                $ret = ['flag' => 1, 'msg' => '更新成功'];
            } else {
                $ret = ['flag' => 0, 'msg' => '更新失败'];
            }
        }else{
            $ret = ['flag'=>0,'msg'=>$validate->getError()];
        }

        return $ret;
    }

    public function export()
    {
        return $this->doExport();
    }

}