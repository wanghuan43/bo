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

class Circulation extends BoController
{

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

            Db::startTrans();
            try{

                foreach ($mids as $mid){
                    foreach ($ids as $id){
                        $data = [
                            'ci_mid'=>$mid,
                            'ci_otid'=>$id,
                            'ci_type' => $type
                        ];
                        $res = Db::table('__CIRCULATION__')->where($data)->find();

                        if( empty($res) ){
                            $res = Db::table('__CIRCULATION__')->insert($data);
                        }
                    }
                }
                Db::commit();
                $ret = ['flag'=>1,'msg'=>'操作成功'];

            }catch (Exception $e){
                $ret = ['flag'=>0,'msg'=>$e->getMessage()];
                Db::rollback();
            }

        }

        return $ret;

    }

}