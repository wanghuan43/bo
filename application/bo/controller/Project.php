<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\model\Project as ModelProject;
use think\Request;


class Project extends BoController
{
    protected $model;

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

    public function all()
    {
        $this->assign('empty','<tr><td colspan="3">没有数据</td></tr>');
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

        $res = $this->model->whereOr('p_no','=',$no)->whereOr('p_name','=',$name)->find();

        $ret = ['flag'=>0,'msg'=>''];

        if($res){
            $data = $res->getData();
            if( $no == $data['p_no'] && $name == $data['p_name'] ){
                $ret['msg'] = '该项目已存在';
            }elseif( $no == $data['p_no']){
                $ret['msg'] = '该项目编号已存在';
            }else{
                $ret['msg'] = '该项目名已存在';
            }
        }else{
            $res = $this->model->insert(['p_no'=>$no,'p_name'=>$name]);
            if($res){
                $ret['flag'] = 1;
                $ret['msg'] = '添加成功';
            }else{
                $ret['msg'] = '添加失败';
            }
        }

        return $ret;

    }

}