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
     * 批量添加
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

    /**
     * 增量增加
     * @param bool $type
     * @param bool $id
     */
    public function addMembers( $type=false, $id=false)
    {
        $mids = $this->request->post('mids/a');

        if( !$type || !$id || !$mids ){
            $ret = ['flag' => 0 , 'msg'=>'参数错误'];
        }else{
            $res = $this->model->where('ci_otid','=',$id)->where("ci_type","=",$type)->field('ci_mid')->select();
            $smids = [];
            foreach( $res as $i ){
                $smids[] = $i->ci_mid ;
            }
            foreach ( $mids as $mid ){
                if( !in_array($mid,$smids) )
                    $data[] = ['ci_mid' => $mid,'ci_otid'=>$id,'ci_type'=>$type];
            }
            if( !!$data ) {
                $this->model->saveAll($data);
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

        $typeModel = \model(ucfirst($type));

        $res = $typeModel->getCodeAndNameById($id);

        $this->assign('code', $res['code']);
        $this->assign('name', $res['name']);
        $this->assign('type',$type);
        $this->assign('id',$id);
        $this->assign('empty','<tr><td colspan="7">没有数据</td></tr>');

        $this->assign('lists', $lists);
        return $this->fetch();
    }

    public function delAll($type=false)
    {
        if($this->current->m_isAdmin != 1){
            return ['flag'=>0,'msg'=>'无权限操作'];
        }

        if(empty($type)){
            return ['flag'=>0,'msg'=>'参数错误'];
        }

        $params = $this->request->param();

        if(empty($params['mids']) || empty($params['ids'])){
            return ['flag'=>0,'msg'=>'参数错误'];
        }

        $this->model->where('ci_type','=',$type)->whereIn('ci_mid',$params['mids'])->whereIn('ci_otid',$params['ids'])->delete();

        return ['flag'=>0,'msg'=>'操作成功'];

    }

    protected function deleteCheck($ids)
    {
        $ret = true;

        /*if(empty($ids)){
            $ret = ['flag'=>0,'msg'=>'参数错误'];
        }elseif($this->current->m_isAdmin!=1){
            $res = $this->model->whereIn('ci_id',$ids)->select();
            $type = $otid = false;
            foreach($res as $item){
                if(empty($type)){
                    $type = $item->ci_type;
                    $otid = $item->ci_otid;
                }elseif($type != $item->ci_type || $otid != $item->ci_otid){
                    $ret = ['flag'=>0,'msg'=>'参数错误'];
                    break;
                }
            }
            $model = \model(ucfirst($type));
            $res = $model->where($model->getPk(),'=',$otid)->find();
            if(empty($res)){
                $ret = ['flag'=>0,'msg'=>'系统错误，请联系管理员'];
            }else{
                switch ($type){
                    case 'orders':
                        $mid = $res->o_mid;
                        break;
                    case 'project':
                        $mid = $res->p_mid;
                        break;
                    case 'contract':
                        $mid = $res->c_mid;
                        break;
                    case 'invoice':
                        $mid = $res->i_mid;
                        break;
                    case 'acceptance':
                        $mid = $res->a_mid;
                        break;
                    case 'received':
                        $mid = $res->r_mid;
                        break;
                    case 'company':
                        $mid = $res->co_mid;
                        break;
                }
                if($mid != $this->current->m_id){
                    $ret = ['flag'=>0,'msg'=>'您无此操作权限'];
                }
            }
        }*/
        return $ret;
    }

}