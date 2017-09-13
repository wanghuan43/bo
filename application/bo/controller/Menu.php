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
        //print_r($tree);die;
        $this->assign('tree',$tree);
        return $this->fetch();           
    }
    
    public function add( $parentID = 0 )
    {
        return $this->fetch();    
    }
    
}