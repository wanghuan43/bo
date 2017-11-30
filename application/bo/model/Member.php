<?php
namespace app\bo\model;

use app\bo\libs\BoModel;
use think\Config;

class Member extends BoModel
{
    protected $pk = 'm_id';

    protected $searchable = array(
        "m_code" => array(
            "name" => "用户编号",
            "type" => "text",
            "operators" => array(
                "like" => "包含",
                "=" => "等于",
            ),
        ),
        "m_name" => array(
            "name" => "用户名",
            "type" => "text",
            "operators" => array(
                "like" => "包含",
                "=" => "等于",
            ),
        ),
        "m_email" => array(
            "name" => "用户邮箱",
            "type" => "text",
            "operators" => array(
                "like" => "包含",
                "=" => "等于",
            ),
        ),
        "m_office" => array(
            "name" => "用户部门",
            "type" => "text",
            "operators" => array(
                "like" => "包含",
                "=" => "等于",
            ),
        ),
        'm_phone' => array(
            'name' => '电话',
            'type' => 'text',
            'operators' => array(
                'like' => '包含',
                '=' => '等于'
            )
        ),
        'm_department' => array(
            'name' => '科室',
            'type' => 'text',
            'operators' => array(
                'like' => '包含',
                '=' => '等于'
            )
        ),
        'm_isAdmin' => array(
            'name' => '管理员',
            'type' => 'select',
            'operators' => array(
                '=' => '等于',
            ),
            'options' => array(
                '2' => '否',
                '1' => '是'
            )
        )
    );

    public function loginMember($data)
    {
        $permissionsModel = new Permissions();
        $menuModel = new Menu();
        $data['email'] = strtoupper($data['email']);
        $member = $this->where("m_email", "=", $data['email'])->find();
        $array = ["code" => 0, "member" => ""];
        if ($member) {
            $pwd = encryptPassword($data['password']);
            if ($member->m_password == $pwd) {
                if (empty($member->m_isAdmin)) {
                    $member->menu = $permissionsModel->getList($member->m_id);
                } else {
                    $menus = $menuModel->getList();
                    Config::load(APP_PATH . "bo" . DS . "commonField.php", "", "commonField");
                    $return = Config::get("permissionsMenu", "commonField");
                    foreach ($return as $v) {
                        $menus[] = $v;
                    }
                    $member->menu = $menus;
                }
                $array['member'] = $member;
            } else {
                $array['code'] = 2;
            }
        } else {
            $array['code'] = 1;
        }
        return $array;
    }

    public function getList($search = array(), $limit)
    {
        foreach( $search as $key => $value ){
            $this->where($value['field'],$value['opt'],$value['val']);
        }
        if( $limit===false ){
            $list = $this->select();
        }else {
            $list = $this->paginate($limit);
        }
        return $list;
    }

    public function import($dataset)
    {
        $departmentModel = new Department();
        $departments = $departmentModel->all();
        $arr = [];
        foreach ($departments as $department){
            $arr[$department->m_code] = $department;
        }
        foreach ($dataset as $key=>$data){

            if( !isset($data['m_password']) || empty($data['m_password'])){
                $data['m_password'] = '123123';
            }

            $dataset[$key]['m_password'] = encryptPassword($data['m_password']);

            if(array_key_exists($data['m_code'],$arr)){
                $dataset[$key]['m_is_lead'] = 1;
                //$dataset[$key]['m_ldid'] = $arr[$data['m_code']]->d_id;
                //$dataset[$key]['m_ldname'] = $arr[$data['m_code']]->d_name;
            }

            foreach ($departments as $department){
                if($department['d_name'] == $data['m_department'] || $department['d_name'] == $data['m_department'].'-'.$data['m_office']){
                    $dataset[$key]['m_did'] = $department->d_id;
                    break;
                }
            }

        }
        return parent::import($dataset); // TODO: Change the autogenerated stub
    }

    public function getMemberByName($name,$department=false){
        $res = $this->where('m_name','=',$name)->select();
        $ret = false;
        if(count($res) == 1){
            $ret = $res[0];
        }elseif (count($res)>1){
            foreach( $res as $member){
                if( $member->m_department == $department ){
                    $ret = $member;
                    break;
                }
            }
        }
        return $ret;
    }

}