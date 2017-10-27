<?php

namespace app\bo\model;

use app\bo\libs\BoModel;
use app\bo\libs\CustomUtils;

class Invoice extends BoModel
{
    protected $pk = "i_id";

    protected $searchable = array(
        "i_date" => array(
            "name" => "发票日期",
            "type" => "date",
            "operators" => array(
                "between" => "介于",
                "=" => "等于",
                ">" => "大于",
                "<" => "小于",
            ),
        ),
        "i_money" => array(
            "name" => "金额",
            "type" => "price",
            "operators" => array(
                "between" => "介于",
                "=" => "等于",
                ">" => "大于",
                "<" => "小于",
            ),
        ),
    );

    public function getList($search, $limit)
    {
        $member = $this->getCurrent();
        $this->alias('i');
        if (!$member->m_isAdmin) {
            $this->join('__CIRCULATION__ c', "i.i_id = c.ci_otid AND c.ci_type = 'invoice'");
            $this->where("c.ci_mid", "=", $member->m_id);
        }
        $this->field("i.*");
        foreach ($search as $key => $value) {
            $this->where("i." . $value['field'], $value['opt'], $value['val']);
        }
        if( $limit===false ){
            $list = $this->select();
        }else {
            $list = $this->paginate($limit);
        }
        return $list;
    }

    public function checkUsed($id, $money, $op = "-")
    {
        $tmp = $this->find($id)->toArray();
        $return = true;
        if ($op == "-") {
            if ($tmp['i_noused'] - $money < 0) {
                $return = false;
            } else {
                $this->save(['i_noused' => ($tmp['i_noused'] - $money), 'i_used' => ($tmp['i_used'] + $money)], ["i_id" => $id]);
            }
        }else{
            $this->save(['i_noused' => ($tmp['i_noused'] + $money), 'i_used' => ($tmp['i_used'] - $money)], ["i_id" => $id]);
        }
        return $return;
    }

    public function getCodeAndNameById($id)
    {
        $data = $this->getDataById($id);
        return ['code'=>$data['i_no'],'name'=>'发票'.$data['i_no']];
    }

    public function import($dataset)
    {

        $mProject = new Project();
        $mContract = new Contract();
        $mCompany = new Company();
        $mMember = new Member();

        foreach ($dataset as $data){
            $coCode = $data['co_code'];
            unset($data['co_code']);
            $pNo = $data['p_no'];
            unset($data['p_no']);
            $cNo = $data['c_no'];
            unset($data['c_no']);
            //$cName = $data['c_name'];
            unset($data['c_name']);
            $project  = $mProject->where('p_no','=',$pNo)->find();
            if(empty($project)){
                CustomUtils::writeImportLog('ERORR : PROJECT EMPTY'.serialize($data),$this->name);
                continue;
            }
            //$contract = $mContract->where('c_no','=',$cNo)->find();
            //if(empty($contract)) continue;
            $company = $mCompany->where('co_name','=',$data['i_coname'])->where('co_type','=',$data['i_type'])
                                ->where('co_status','=',1)->find();
            if(empty($company)){
                $company = $mCompany->where('co_code','=','EA'.$coCode)
                                    ->where('co_type','=',$data['i_type'])
                                    ->where('co_status','=',1)->find();
            }
            if($company){
                $data['i_coname'] = $company->co_name;
                $data['i_coid'] = $company->co_id;
            }else{
                $data['i_coid'] = 0;
            }

            $member = $mMember->where('m_name','=',$data['i_mname'])->find();

            if($member){
                $data['i_mid'] = $member->m_id;
            }

            if($data['i_money']<0){
                $data['i_money'] = 0 - $data['i_money'];
            }

            $data['i_noused'] = $data['i_money'];

            /*$orderModel = new Orders();

            $order['o_cid'] = $contract->c_id;
            $order['o_cno'] = $contract->c_no;
            $order['o_mid'] = $data['i_mid'];
            $order['o_mname'] = $data['i_mname'];
            $order['o_pname'] = $project->p_name;
            $order['o_pid'] = $project->p_id;
            $order['o_type'] = $data['i_type'];
            $order['o_no'] = $orderModel->getOrderNO($project->p_id,$order['o_type']);
            $order['o_subject'] = $contract->c_name . '-' . $order['o_no'];
            $order['o_coid'] = $data['i_coid'];
            $order['o_coname'] = $data['i_coname'];

            if($company){

                if($company->co_internal_flag == 1){
                    $order['o_lie'] = 1;
                }elseif( $company->co_internal_flag == 2){
                    $order['o_lie'] = 2;
                }

            }

            if($member){
                $order['o_did'] = $member->m_did;
                $order['o_dname'] = $member->m_department;
            }

            $order['o_date'] = $contract->c_date;
            $order['o_money'] = $contract->c_money;
            $order['o_status'] = 6;
            $order['o_createtime'] = $order['o_updatetime'] = time();
            $order['o_csname'] = $data['o_csname'];
            $order['o_deal'] = $order['o_csid'] = $data['o_csid'];*/
            if(isset($data['o_csid']))
                unset($data['o_csid']);

            if(isset($data['o_csname']))
                unset($data['o_csname']);

            $iid = $this->insertGetId($data);

            /*$oid = $orderModel->insertGetId($order);

            $ou = [
                'ou_oid' => $oid,
                'ou_otid' => $iid,
                'ou_used' => $data['i_money'],
                'ou_date' => $data['i_date'],
                'ou_type' => 1
            ];

            $ouModel = new OrderUsed();

            $ouModel ->insert( $ou );*/

        }

    }

}