<?php
/**
 * Created by PhpStorm.
 * User: zhichang
 * Date: 2017/9/18
 * Time: 上午11:01
 */

namespace app\bo\controller;


use app\bo\libs\BoController;
use think\Db;
use think\Exception;
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
    public function add($type=false )
    {

        $ids = $this->request->post('ids/a');
        $mids = $this->request->post('mids/a');

        if(!$type || !$ids || !$mids){
            $ret = ['flag'=>0,'msg'=>'参数错误'];
        }else{

            $type = strtolower($type);
            foreach( $ids as $ot_id ){
                $this->model->setCirculation($ot_id,$mids,$type);
            }

            $ret = ['flag'=>1,'msg'=>'操作成功'];

        }

        return $ret;

    }

    public function list()
    {
        $params = $this->request->param();
        $type = $params['type'];
        $id = $params['id'];

        if( $type && $id ){
            $list = $this->model->where("ci_type","=",$type)->where('ci_otid','=',$id)->paginate($this->limit);
        }else{
            $list = $this->model->paginate($this->limit);
        }
        $this->assign('lists',$list);
        return $this->fetch();
    }

}