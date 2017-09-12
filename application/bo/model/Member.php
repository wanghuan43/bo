<?php
namespace app\bo\model;

use app\bo\libs\BoModel;
use think\Config;

class Member extends BoModel
{
    protected $pk = 'm_id';

    public function loginMember($data)
    {
        $permissionsModel = new Permissions();
        Config::load(APP_PATH . "bo" . DS . "commonField.php", "", "commonField");
        $key = Config::get("baseKey", "commonField");
        $data['email'] = strtoupper($data['email']);
        $member = $this->where("m_email", "=", $data['email'])->find();
        $array = ["code"=>0,"member"=>""];
        if($member){
            $pwd = md5($data['password'].md5($key));
            if($member->m_password == $pwd){
                if(empty($member->m_isAdmin)){
                    $member->menu = $permissionsModel->getList($member->m_id);
                }else{
                    $menus = $permissionsModel->getList();
                    Config::load(APP_PATH . "bo" . DS . "commonField.php", "", "commonField");
                    $return = Config::get("permissionsMenu", "commonField");
                    foreach($return as $v){
                        $menus[] = $v;
                    }
                    $member->menu = $menus;
                }
                $array['member'] = $member;
            }else{
                $array['code']=2;
            }
        }else{
            $array['code']=1;
        }
        return $array;
    }
}