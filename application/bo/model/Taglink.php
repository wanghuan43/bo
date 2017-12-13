<?php
namespace app\bo\model;

use app\bo\libs\BoModel;
use app\bo\libs\CustomUtils;

class Taglink extends BoModel
{
    public function getList($ot_id, $model = "orders", $isArray = false)
    {
        $db = $this->db();
        $list = $db->table('__TAGLIB__')
            ->alias('tl')
            ->join('__TAGLINK__ tk', 'tl.tl_id = tk.tl_id', "left")
            ->where('tk.ot_id', $ot_id)
            ->where('tk.model', $model)
            ->select();
        if ($isArray) {
            foreach ($list as $key => $value) {
                $list[$key] = $value->toArray();
            }
        }
        return $list;
    }

    public function setTagLink($ot_id, $list, $model = "orders")
    {
        $tl = array();
        $tlm = new Taglib();
        foreach ($list as $value) {
            $value = trim($value);
            if (is_numeric($value)) {
                $tlm = Taglib::get($value);
                $tlm->tl_times = intval($tlm->tl_times) + 1;
            } else {
                $tlm = $tlm->where("tl_name", "=", $value)->find();
                if (empty($tlm->tl_id)) {
                    $tlm = new Taglib();
                    $tlm->tl_times = 1;
                    $tlm->tl_name = $value;
                } else {
                    $tlm->tl_times = intval($tlm->tl_times) + 1;
                }
            }
            $tlm->save();
            $tl[] = array("ot_id" => $ot_id, "tl_id" => $tlm->tl_id, "model" => $model);
        }
        $this->where("ot_id", "=", $ot_id)->where("model", "=", $model)->delete();
        if (count($tl) > 0) {
            $this->saveAll($tl);
        }
    }

    protected function doImport($dataset)
    {
        $arr = [];
        $mOrders = new \app\bo\model\Orders();
        $mTagLib = new \app\bo\model\Taglib();
        foreach ($dataset as $key => $data){
            if(empty($data['tags'])){
                unset($dataset[$key]);
                continue;
            }
            $tagLink['model'] = $data['model'];
            if($data['model'] == 'orders'){
                $order = $mOrders->where('o_no','=',$data['o_no'])->field('o_id')->find();
                if(empty($order)){
                    CustomUtils::writeImportLog('ot_id is null - '.serialize($data),strtolower($this->name));
                    continue;
                }
                $tagLink['ot_id'] = $order->o_id;
            }
            $tags = explode('\\',$data['tags']);
            foreach ($tags as $tag){
                if(empty($tag)){
                    continue;
                }
                $tag = trim($tag);
                $tagLink['tl_id'] = $mTagLib->getTagIdByName($tag);
                $arr[] = $tagLink;
            }
        }

        $this->insertDuplicate($arr);

        foreach($arr as $i){
            if(!isset($cnt[$i['tl_id']])){
                $cnt[$i['tl_id']] = 0;
            }
            $cnt[$i['tl_id']] += 1;
        }

        foreach ($cnt as $id=>$val){
            $mTagLib = new \app\bo\model\Taglib();
            $tLib = $mTagLib->where('tl_id','=',$id)->find();
            $val = $tLib->tl_times + $val;
            $mTagLib->save(['tl_times'=>$val],['tl_id'=>$id]);
        }

        return true;

    }

}
