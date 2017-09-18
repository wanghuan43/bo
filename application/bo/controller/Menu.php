<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use app\bo\model\Menu as Model;
use think\Request;

class Menu extends BoController
{
    protected $model;

    /**
     * Menu constructor.
     */
    public function __construct(Request $request)
    {
        $this->model = new Model();
        parent::__construct($request);
    }
    
    public function index()
    {
        $tree = $this->model->getList();
        $tree = array_values($tree);
        $this->assign('source',json_encode($tree));
        $this->assign('tree',$tree);
        return $this->fetch();           
    }
    
    public function add( $pid = 0 )
    {
        if( $this->request->isPost() ){
 
            return $this->doAdd();
            
        }else{
            $pid = $this->request->get('pid')?:$pid;
            $this->assign('tree',$this->model->getList());
            $this->assign('pid',$pid);
            return $this->fetch();  
        }
    }
    
    public function update( $id = false ){
        
        $id = trim($this->request->post('id'))?:$id;
        $url = trim($this->request->post('url'));
        $isShow = $this->request->post('is_show');
        $listOrder = intval(trim($this->request->post('list_order')));
        $name = trim($this->request->post('name'));
        
        $msg = [];
        if( $listOrder < 0 ){
            $msg[] = '排序只能为非零整数';
        }
        
        if( $name == '' ){
            $msg[] = '菜单名称不能为空';
        }
        
        if( !!$msg ){
            
            $ret = [
                    'flag' => 0,
                    'msg' => implode(',', $msg)
            ];
            
        } else {
            
            $data = [
                    'url' => $url,
                    'is_show' => $isShow,
                    'list_order' => $listOrder,
                    'name' => $name
            ];
            
            $res = $this->model->save($data, ['id' => $id]);
            
            if ($res == 1) {
                $ret = [
                        'flag' => 1,
                        'msg' => '修改成功！'
                ];
            } else {
                $ret = [
                        'flag' => 0,
                        'msg' => '修改失败，请确认你做过修改！'
                ];
            }
        }
        
        return $ret;
        
    }
    
    public function delete($id=FALSE,$isAjax=TRUE){
        
        $id = $this->request->post('id')?:$id;
        
        $ret = [];
        
        if( empty($id) ){
            $ret = ['flag'=>0,'msg'=>'ID为空'];
        }else{
            $res = $this->model->isUpdate(true,'id = '.$id.' OR parent_id = '.$id)->delete();
            if( $res > 0 ){
                $ret = ['flag'=>1,'msg'=>'删除成功'];
            }else{
                $ret = ['flag'=>0, 'msg'=>'发生错误，删除失败'];
            }
        }
        
        return $ret;
        
    }
    
    protected function doAdd($isAjax=true)
    {
        $data = [
                'parent_id' => $this->request->post('parent_id'),
                'name' => trim($this->request->post('name')),
                'url' => trim($this->request->post('url')),
                'list_order' => trim($this->request->post('list_order')),
                'is_show' => $this->request->post('is_show')?:0
        ];
        
        if( $data['parent_id'] == 0 ){
            $data['url'] = '';
        }
        
        $ret = [];
        
        if( empty($data['name']) ){
            $ret = [
                    'flag' => 0,
                    'msg' => '菜单名称不能为空'
            ];    
        }elseif( intval($data['list_order']) < 0 ){
            $ret =[
                    'flag' => 0,
                    'msg' => '排序只能是非负整数'
            ];
        }else{
            
            $res = $this->model->where('parent_id','=',$data['parent_id'])->where('name','=',$data['name'])->find();
            if( $res ){
                $ret = [
                        'flag' => 0,
                        'msg' => '菜单名重复'
                ];              
            }else{
                $res = $this->model->save($data);              
                if( $res ){
                    $ret = [ 'flag'=>1,'msg'=>'添加成功'];
                }else{
                    $ret = ['flag'=>0,'msg'=>'添加失败'];
                }
            }
            
        }
            
        
        return $ret;
    }
    
}