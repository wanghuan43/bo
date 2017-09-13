<?php
namespace app\bo\controller;

use think\Controller;
use app\bo\model\Menu as Model;

class Menu extends Controller
{
    protected $model;
    
    public function __construct()
    {
        $this->model = new Model();
        parent::__construct();
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
        return $this->fetch();    
    }
    
    public function update( $id = false ){
        
        if( $_POST['id'] ){
            $id = $_POST['id'];
        }
        $url = trim($_POST['url']);
        $isShow = trim($_POST['is_show']);
        $listOrder = intval(trim($_POST['list_order']));
        $name = trim($_POST['name']);
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
            
            $res = $this->model->save($data, [
                    'id' => $id
            ]);
            
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
    
}