<?php

namespace app\bo\libs;

use think\Config;
use think\Controller;
use think\Request;
use think\Url;

class BoController extends Controller
{
    protected $limit = 20;
    protected $current = false;

    protected $model;
    protected $other = '';
    protected $maxSize = "20M";
    protected $extAllowed = [
        'xlsx', 'csv', 'xls', 'docx', 'png', 'gif', 'jpg', 'jpeg', 'doc', 'pdf', 'txt', 'pptx', 'ppt'
    ];

    /**
     * BoController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $nowAction = $request->action();
        $this->current = getLoginMember();
        if (!$this->current AND $nowAction != "login") {
            $this->redirect(Url::build('/dashboard/login', "", false));
        }
    }

    protected function search($model, $file = "common/poplayer", $colspan = "3")
    {
        $post = Request::instance()->post();
        $page = Request::instance()->get("page", false);
        $c_type = Request::instance()->get("c_type", false);
        $permissions = Request::instance()->get("permissions", false);
        $name = get_class($model);
        $name = strtolower(substr($name, strripos($name, "\\") + 1));
        $search = array();
        if (isset($post['fields'])) {
            foreach ($post['fields'][$name] as $key => $value) {
                $val = count($post['values'][$name][$key]) > 1 ? $post['values'][$name][$key] : trim($post['values'][$name][$key][0]);
                $opt = trim($post['operators'][$name][$key]);
                $val = is_array($val) ? ((empty($val['0']) AND empty($val['1'])) ? "" : $val) : $val;
                if (!empty($val)) {
                    if ($opt == "between") {
                        $val = is_array($val) ? $val : explode(" ~ ", $val);
                    } elseif ($opt == "like") {
                        $val = "%$val%";
                    }
                    $search[] = array(
                        "field" => $value,
                        "opt" => $opt,
                        "val" => $val
                    );
                }
            }
        }
        if ($c_type AND in_array($name, ["company", "contract"])) {
            if ($name == "company") {
                $search[] = [
                    "field" => "co_type",
                    "opt" => "=",
                    "val" => ($c_type == 1 ? 2 : 1),
                ];
            } else {
                $search[] = [
                    "field" => "c_type",
                    "opt" => "=",
                    "val" => $c_type
                ];
            }
        }
        $this->formartSearch($model, $search);
        $this->assign("fi", Request::instance()->get("fi", "0"));
        $this->assign("permissions", $permissions);
        $this->assign("other", $this->other);
        $list = $model->getList($search, $this->limit);
        $this->assign("lists", $list);
        $this->assign("empty", '<tr><td colspan="' . $colspan . '">暂无数据</td></tr>');
        if (Request::instance()->isAjax()) {
            if (count($post) > 0 OR $page) {
                if($this->request->get('listType')=='all'){
                    $this->assign('listType',2);
                }else{
                    $this->assign('listType',1);
                }
                $content = $this->fetch("list");
            } else {
                $this->assign("searchable", $model->getSearchable());
                $content = $this->fetch($file);
            }
            return array("content" => $content);
        } else {
            $this->assign("searchable", $model->getSearchable());
            return $this->fetch($file);
        }
        return "";
    }

    /**
     * @param $operator select|circulation|filter|export
     * @param int $listType
     * @param bool|string $listContainer
     * @param bool $model
     * @param string $file
     * @return mixed
     */
    protected function filter($operator,$listType=1,$listContainer=false,$model=false,$file="common/filter")
    {
        $post = $this->request->post();
        $get = $this->request->get();
        $param = $this->request->param();

        if(!empty($this->model))
            $model = $this->model;

        $mName = strtolower($this->model->getModelName());
        $type = isset($param['type'])?$param['type']:$mName;

        if($mName == 'company'){
            $type = 'company';
        }
        $listFile = $mName.'/list/'.$listType;

        $search = $this->getSearch($post,$type);

        if(isset($post['fields']) || isset($get['page'])){
            $file = $listFile;
        }

        if( empty($listContainer) || isset($post['fields']) || isset($get['page'])) {
            $list = $model->getList($search, $this->limit);
            $this->assign("lists", $list);
        }

        if($operator == 'export'){
            $this->assign('btnSaveText','导出');
        }

        $this->assign('type',$type);
        $this->assign('listContainer',$listContainer);
        $this->assign('operator',$operator);
        $this->assign('listType',$listType);
        $this->assign("searchable", $model->getSearchable());
        $this->assign('url',$this->request->url());

        $listHtml = $this->fetch($listFile);
        $this->assign('listHtml',$listHtml);

        return $this->fetch($file);

    }

    protected function getSearch($post=false,$type=false)
    {
        if(empty($post)){
            $post = $this->request->post();
        }
        if(empty($type)){
            $param = $this->request->param();
            $mName = strtolower($this->model->getModelName());
            $type = isset($param['type'])?$param['type']:$mName;
        }

        $search = array();
        if (isset($post['fields'])) {
            foreach ($post['fields'][$type] as $key => $value) {
                $val = count($post['values'][$type][$key]) > 1 ? $post['values'][$type][$key] : trim($post['values'][$type][$key][0]);
                $opt = trim($post['operators'][$type][$key]);
                $val = is_array($val) ? ((empty($val['0']) AND empty($val['1'])) ? "" : $val) : $val;
                if (!empty($val)) {
                    if ($opt == "between") {
                        $val = is_array($val) ? $val : explode(" ~ ", $val);
                    } elseif ($opt == "like") {
                        $val = "%$val%";
                    }
                    $search[] = array(
                        "field" => $value,
                        "opt" => $opt,
                        "val" => $val
                    );
                }
            }
        }

        if($type == "orders"){
            $search[] = [
                'field' => 'o_status',
                'opt'   => '<>',
                'val'   => 6
            ];
        }elseif ($this->model->getModelName()=='Orders' && $type == 'contract'){
            $search[] = [
                'field' => 'o_status',
                'opt'   => '=',
                'val'   => 6
            ];
        }

        if($type == 'company'){
            $co_type = $this->request->param('type');
            $search[] = [
                'field' => 'co_type',
                'opt'   => '=',
                'val'   => $co_type
            ];
        }

        $this->formartSearch($this->model,$search);
        return $search;
    }

    private function formartSearch($model, &$search)
    {
        foreach ($search as $key => $value) {
            $tmp = $model->getSearchableByKey($value['field']);
            if ($tmp) {
                $value['val'] = $this->formartValue($value['val'], $tmp['type']);
            }
            $search[$key] = $value;
        }
    }

    private function formartValue($value, $type)
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = $this->formartValue($val, $type);
            }
        } else {
            switch ($type) {
                case 'date':
                    $value = strtotime(trim($value));
                    break;
                default:
                    $value = trim($value);
                    break;
            }
        }
        return $value;
    }

    public function add()
    {
        if ($this->request->isPost()) {
            return $this->doAdd();
        } else {
            return $this->fetch();
        }
    }

    public function all()
    {
        if (empty($this->model)) {
            $ret = '<h2>Error Page!</h2>';
        } else {
            $list = $this->model->paginate($this->limit);
            $this->assign('lists', $list);
            $this->assign('types',getTypeList());
            $ret = $this->fetch();
        }
        return $ret;
    }

    public function del()
    {
        if (empty($this->model)) {
            $ret = ['flag' => 0, 'msg' => '发生错误'];
        } else {
            $ids = $this->request->post('ids/a');

            if (is_array($ids) && count($ids) > 0) {

                $res = $this->model->whereIn($this->model->getPk(), $ids)->delete();

                if ($res) {
                    $ret = ['flag' => 1, 'msg' => '删除成功'];
                } else {
                    $ret = ['flag' => 0, 'msg' => '删除失败'];
                }
            } else {
                $ret = ['flag' => 0, 'msg' => '参数错误'];
            }
        }

        return $ret;
    }

    protected function doAdd()
    {
        return ['flag' => 0, 'msg' => '发生错误'];
    }

    protected function saveFile($file)
    {
        $this->getMaxFileSize();
        if (!is_array($file)) {
            return array('status' => false, "message" => '参数错误');
        }
        if (!empty($file['error'])) {
            return array('status' => false, "message" => '上传出错，请联系管理员');
        }
        $max = intval(str_ireplace('m', "", $this->maxSize)) * 1024 * 1024;
        if ($file['size'] > $max) {
            return array('status' => false, "message" => '上传文件大小大于限制,当前文件不能超过：' . $this->maxSize);
        }
        $exts = array_flip($this->extAllowed);
        $tmp = explode(".", $file['name']);
        $ext = strtolower($tmp[count($tmp) - 1]);
        if (!array_key_exists($ext, $exts)) {
            return array('status' => false, "message" => '上传文件类型不正确,当前只能只可以上传这些类型文件：' . implode(",", $this->extAllowed));
        }
        $filename = str_ireplace('.' . $ext, '', $file['name']) . '-' . uniqid(mt_rand(10, 1000)) . "." . $ext;
        $data = [
            'savePath' => $_SERVER['DOCUMENT_ROOT'] . "/attachment/" . $filename,
            'path' => "/attachment/" . $filename,
            'ext' => $ext,
            'filename' => $filename,
            'size' => $file['size'],
        ];
        $up = move_uploaded_file($file['tmp_name'], $data['savePath']);
        $message = $up ? "上传成功" : "上传失败";
        return ["status" => $up, "message" => $message, "data" => $data];
    }

    protected function batchFile()
    {
        $files = false;
        foreach ($_FILES as $key => $value) {
            $files[$key] = $this->saveFile($value);
        }
        return $files;
    }

    protected function getMaxFileSize()
    {
        $postSize = intval(str_ireplace('m', '', ini_get("post_max_size")));
        $uploadSize = intval(str_ireplace('m', '', ini_get("upload_max_filesize")));
        $max = intval(str_ireplace('m', '', $this->maxSize));
        $this->maxSize = $postSize < $uploadSize ? ($postSize < $max ? $postSize : $max) : ($uploadSize < $max ? $uploadSize : $max);
        return $this->maxSize . "M";
    }

    protected function doExport()
    {
        if($this->request->post('operator') == 'export'){

            ini_set("memory_limit", "1024M");
            $post = $this->request->post();
            $type = strtolower($this->model->getModelName());

            if( isset($post['ids']) ){
                $res = $this->model->where($this->model->getPk(),'IN',$post['ids'])->select();
            }else{
                $search = $this->getSearch($post,$type);
                $res = $this->model->getList($search,false);
            }

            $title = ucfirst($type);

            $obj = new \PHPExcel();
            $obj->getProperties()->setCreator("新智云商机管理系统")
                ->setLastModifiedBy("新智云商机管理系统")
                ->setTitle($title)
                ->setSubject($title)
                ->setDescription($title);
            $config = Config::load(APP_PATH.'bo'.DS.'excelExport.php','boExcel');
            if( $type == 'company' ){
                $co_type = $this->request->param('type');
                $config = $config['boExcel'][$type.'-'.$co_type];
            }else {
                $config = $config['boExcel'][$type];
            }
            $obj->setActiveSheetIndex(0);
            $activeSheet = $obj->getActiveSheet();
            foreach( $config as $k=>$i ){
                $activeSheet->setCellValue($k.'1',$i['title']);
            }
            $col = 2;
            $types = getTypeList();
            foreach($res as $item){

                foreach($config as $k => $i ){
                    $val = $item->getData($i['key']);
                    if(isset($i['type'])){
                        if( is_array($i['type']) ){
                            if( isset($i['type'][$val]) )
                                $val = $i['type'][$val];
                            else
                                $val = '';
                        }else {
                            if ($i['type'] == 'type') {
                                if (isset($types[$val]))
                                    $val = $types[$val];
                                else
                                    $val = '';
                            } elseif ($i['type'] == 'date') {
                                $val = date('Y/m/d', $val);
                            }
                        }
                    }
                    $activeSheet->setCellValue($k.$col,$val);
                }
                $col ++;
            }
            $activeSheet->setTitle($type);
            $fileName = $title.'-'.date('ymdHis');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$fileName.'.xlsx"');
            header('Cache-Control: max-age=0');

            $objWriter = \PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
            $objWriter->save('php://output');

            exit;

        }else {
            if($this->model->getModelName() == 'Company' && $this->request->param('type') == 2 ){
                $listType = 4;
            }else{
                $listType = 3;
            }
            return $this->filter('export', $listType);
        }

    }

}