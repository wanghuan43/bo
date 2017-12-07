<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\model\Logs;
use app\bo\model\Orders;
use app\bo\model\Project as ModelProject;
use think\File;
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
        $post = $this->request->post();
        $no = strtoupper(trim($this->request->post('no')));
        $name = trim($this->request->post('name'));

        $validate = validate('Project');

        $data = [
            'p_no'=>$no,
            'p_name'=>$name,
            'p_date' => trim($post['date']),
            'p_did' => $post['did'],
            'p_dname' => $post['dname'],
            'p_income' => trim($post['income']),
            'p_pay' => trim($post['pay']),
            'p_mname'=>$this->current->m_name,
            'p_mid'=>$this->current->m_id,
            'p_content' => trim($post['content']),
            'p_createtime'=>time(),
            'p_updatetime'=>time()
        ];

        if( $validate->check($data) ) {

            if(!empty($data['p_date'])){
                $data['p_date'] = strtotime($data['p_date']);
            }

            $file = $this->request->file('attachment');

            $res = $this->uploadFile($file);

            if($res['flag']===0){
                $ret = $res;
            }else {

                if($res['flag'] === 1){
                    $data['p_attachment'] = $res['name'];
                }

                if ($res = $this->model->save($data)) {

                    $ret = ['flag' => 1, 'msg' => '添加成功'];

                } else {
                    $ret = ['flag' => 0, 'msg' => '添加失败'];
                }
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
        $this->setUpdateParams($data['p_mid']);
        if(!empty($orders)){
            $this->assign('readonly','true');
        }
        $mimeType = false;
        if($data['p_attachment']){
            $mimeType = $this->getAttachmentMimeType($data['p_attachment']);
        }
        $this->assign('aMimeType',$mimeType);
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
            'p_no' => strtoupper(trim($post['no'])),
            'p_name' => trim($post['name']),
            'p_mname' => trim($post['mname']),
            'p_mid' => $post['mid'],
            'p_did' => $post['did'],
            'p_dname' => $post['dname'],
            'p_income' => trim($post['income']),
            'p_pay' => trim($post['pay']),
            'p_updatetime' => time(),
            'p_date' => trim($post['date']),
            'p_content' => trim($post['content'])
        ];

        $validate = validate('Project');

        if($validate->check($data)) {
            if(!empty($data['p_date'])){
                $data['p_date'] = strtotime($data['p_date']);
            }

            $file = $this->request->file('attachment');

            $res = $this->uploadFile($file);

            if($res['flag']===0){
                $ret = $res;
            }else {

                if($res['flag'] === 1){
                    $data['p_attachment'] = $res['name'];
                }
                $old = $this->model->getDataById($data['p_id']);
                if ($res = $this->model->save($data,$data['p_id'])) {
                    $logModel = new Logs();
                    $logModel->saveLogs($data,$old,$data['p_id'],'project');
                    $ret = ['flag' => 1, 'msg' => '更新成功'];
                    if(isset($data['p_attachment']) && $data['p_attachment']){
                        if($this->getAttachmentMimeType($data['p_attachment']) == 'image'){
                            $ret['image'] = $data['p_attachment'];
                        }else{
                            $ret['file'] = $data['p_attachment'];
                        }
                    }
                } else {
                    $ret = ['flag' => 0, 'msg' => '更新失败'];
                }
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