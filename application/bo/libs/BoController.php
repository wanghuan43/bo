<?php

namespace app\bo\libs;

use app\bo\model\OrderUsed;
use think\Config;
use think\Controller;
use think\File;
use think\Request;
use think\Url;

class BoController extends Controller
{
    public $limit;
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
        if( !!$this->current && $this->current->m_password == encryptPassword('123123') && $nowAction != 'changepassword'){
            $this->redirect(Url::build('/dashboard/changePassword','',false));
        }
        $this->limit = !empty($_SESSION['pageLimit']) ? $_SESSION['pageLimit'] : "20";
        $this->assign("pageLimit", $this->limit);
    }

    public function setPageLimit()
    {
        $_SESSION['pageLimit'] = Request::instance()->post("pageLimit", 20);
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
                if (is_array($val)) {
                    $val = (trim($val['0']) != "" AND trim($val['1']) != "") ? $val : "";
                } else {
                    $val = trim($val);
                }
                if ($val != "") {
                    if ($opt == "between") {
                        $val = is_array($val) ? $val : explode(" ~ ", $val);
                    } elseif ($opt == "like") {
                        $val = "%$val%";
                    }
                    if (in_array($value, ['i_type', 'i_tax', 'c_type', 'a_type', 'r_type', 'o_type', 'o_lie','o_tax','m_isAdmin']) AND empty($val)) {
                        continue;
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
                if ($this->request->get('listType') == 'all') {
                    $this->assign('listType', 2);
                } else {
                    $this->assign('listType', 1);
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
    protected function filter($operator, $listType = 1, $listContainer = false, $model = false, $file = "common/filter")
    {
        $post = $this->request->post();
        $get = $this->request->get();
        $param = $this->request->param();

        if (!empty($this->model))
            $model = $this->model;

        $mName = strtolower($this->model->getModelName());
        $type = isset($param['type']) ? $param['type'] : $mName;

        if ($mName == 'company') {
            $type = 'company';
        }
        $listFile = $mName . '/list/' . $listType;

        $search = $this->getSearch(false, $post, $type);

        if (isset($post['fields']) || isset($get['page'])) {
            $file = $listFile;
        }

        if (empty($listContainer) || isset($post['fields']) || isset($get['page'])) {
            $list = $model->getList($search, $this->limit);
            $this->assign("lists", $list);
        }

        if ($operator == 'export') {
            $this->assign('btnSaveText', '导出');
        } elseif ($operator == 'select') {
            $this->assign('btnSaveText', '保存');
        }

        $this->assign('type', $type);
        $this->assign('listContainer', $listContainer);
        $this->assign('operator', $operator);
        $this->assign('listType', $listType);
        $this->assign("searchable", $model->getSearchable());
        $this->assign('url', $this->request->url());

        $listHtml = $this->fetch($listFile);
        $this->assign('listHtml', $listHtml);

        return $this->fetch($file);

    }

    protected function getSearch($model = false, $post = false, $mName = false)
    {
        if (empty($model)) {
            $model = $this->model;
        }
        if (empty($post)) {
            $post = $this->request->post();
        }
        if (empty($mName)) {
            $mName = strtolower($model->getModelName());
        }
        $param = $this->request->param();
        $type = isset($param['type']) ? $param['type'] : false;

        $search = array();
        if (isset($post['fields'])) {
            foreach ($post['fields'][$mName] as $key => $value) {
                $val = count($post['values'][$mName][$key]) > 1 ? $post['values'][$mName][$key] : trim($post['values'][$mName][$key][0]);
                $opt = trim($post['operators'][$mName][$key]);
                if (is_array($val)) {
                    $val = (trim($val['0']) != "" AND trim($val['1']) != "") ? $val : "";
                } else {
                    $val = trim($val);
                }
                if ($val != "") {
                    if ($opt == "between") {
                        $val = is_array($val) ? $val : explode(" ~ ", $val);
                    } elseif ($opt == "like") {
                        $val = "%$val%";
                    }
                    if (in_array($value, ['i_type', 'i_tax', 'c_type', 'a_type', 'r_type', 'o_type', 'o_lie','o_tax','m_isAdmin']) AND empty($val)) {
                        continue;
                    }
                    $search[] = array(
                        "field" => $value,
                        "opt" => $opt,
                        "val" => $val
                    );
                }
            }
        }

        if ($mName == 'company') {
            $co_type = $this->request->param('type');
            $search[] = [
                'field' => 'co_type',
                'opt' => '=',
                'val' => $co_type
            ];
        }
        $this->formartSearch($model, $search);
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

    public function all($trashed=2)
    {

        $this->assign("empty", "<tr><td>无数据</td></tr>");
        if (empty($this->model)) {
            $ret = '<h2>Error Page!</h2>';
        } else {

            $params = $this->request->param();

            $sort = [];

            if (isset($params['sort'])) {
                if ($params['order'] == 1) {
                    $sort[$params['sort']] = 'asc';
                } else {
                    $sort[$params['sort']] = 'desc';
                }
                $this->model->order($sort);
            }

            $aType = 1;

            if (isset($params['atype'])) {
                $aType = $params['atype'];
            } elseif (isset($params['aType'])) {
                $aType = $params['aType'];
            }

            $isAdmin = $this->current->m_isAdmin == 1 ? true : false;

            $modelName = strtolower($this->model->getModelName());
            $search = $this->getSearch();
            $list = $this->model->getList($search, $this->limit, $aType,$trashed);
            $this->assign('sort', $sort);
            $this->assign('search', $this->model->getSearchable());
            $this->assign('searchValues', $search);
            $this->assign('modelName', $modelName);
            $this->assign('other', 'main-pannel');
            $this->assign('lists', $list);
            $this->assign('types', getTypeList());
            $this->assign('aType', $aType);
            $this->assign('isAdmin', $isAdmin);
            if (isset($params['fields']) || isset($params['page']) || isset($params['sort'])) {
                $ret = $this->fetch($modelName . '/list/2');
            } else {
                $ret = $this->fetch('all');
            }
        }
        return $ret;
    }

    public function del()
    {
        if (empty($this->model) || $this->model->getModelName() == 'Orders') {
            $ret = ['flag' => 0, 'msg' => '发生错误'];
        } else {

            $ids = $this->request->post('ids/a');

            $res = $this->deleteCheck($ids);

            if ($res === true) {

                if (is_array($ids) && count($ids) > 0) {

                    if( method_exists($this->model,'getTrashedField')){
                        $res = $this->model->whereIn($this->model->getPk(),$ids)->update([$this->model->getTrashedField() => 1]);
                    }else {
                        $res = $this->model->whereIn($this->model->getPk(), $ids)->delete();
                    }

                    if ($res) {
                        $ret = ['flag' => 1, 'msg' => '删除成功'];
                    } else {
                        $ret = ['flag' => 0, 'msg' => '删除失败'];
                    }
                } else {
                    $ret = ['flag' => 0, 'msg' => '参数错误'];
                }

            } else {
                $ret = $res;
                if (!isset($ret['flag'])) $ret['flag'] = 0;
                if (!isset($ret['msg'])) $ret['msg'] = '选择项不能全部被删除';
            }

        }

        return $ret;

    }

    protected function deleteCheck($ids)
    {
        if ($this->current->m_isAdmin == 1) {
            $ret = true;
        } else {
            $ret = ['flag' => 0, 'msg' => '无权限操作'];
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
        if ($this->request->post('operator') == 'export') {

            ini_set("memory_limit", "1024M");
            $post = $this->request->post();
            $params = $this->request->param();
            $type = strtolower($this->model->getModelName());

            if (isset($params['sort'])) {
                if ($params['order'] == 1) {
                    $sort[$params['sort']] = 'asc';
                } else {
                    $sort[$params['sort']] = 'desc';
                }
                $this->model->order($sort);
            }

            if (isset($post['ids'])) {
                $res = $this->model->where($this->model->getPk(), 'IN', $post['ids'])->select();
            } else {
                $search = $this->getSearch(false, $post, $type);
                $res = $this->model->getList($search, false);
            }

            $title = ucfirst($type);

            $obj = new \PHPExcel();
            $obj->getProperties()->setCreator("新智云商机管理系统")
                ->setLastModifiedBy("新智云商机管理系统")
                ->setTitle($title)
                ->setSubject($title)
                ->setDescription($title);
            $config = Config::load(APP_PATH . 'bo' . DS . 'excelExport.php', 'boExcel');
            if ($type == 'company') {
                $co_type = $this->request->param('type');
                $config = $config['boExcel'][$type . '-' . $co_type];
            } else {
                $config = $config['boExcel'][$type];
            }
            $obj->setActiveSheetIndex(0);
            $activeSheet = $obj->getActiveSheet();
            foreach ($config as $k => $i) {
                $activeSheet->setCellValue($k . '1', $i['title']);
            }
            $col = 2;
            $types = getTypeList();
            foreach ($res as $item) {

                foreach ($config as $k => $i) {
                    $val = $item->getData($i['key']);
                    if (isset($i['type'])) {
                        if (is_array($i['type'])) {
                            if (isset($i['type'][$val]))
                                $val = $i['type'][$val];
                            else
                                $val = '';
                        } else {
                            if ($i['type'] == 'type') {
                                if (isset($types[$val]))
                                    $val = $types[$val];
                                else
                                    $val = '';
                            } elseif ($i['type'] == 'date') {
                                $val = date('Y/m/d', $val);
                            } elseif ($i['type'] == 'tax' ){
                                $val = getTaxList($val);
                            }
                        }
                    }
                    $activeSheet->setCellValue($k . $col, $val);
                }
                $col++;
            }
            $activeSheet->setTitle($type);
            $fileName = $title . '-' . date('ymdHis');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
            header('Cache-Control: max-age=0');

            $objWriter = \PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
            $objWriter->save('php://output');

            exit;

        } else {
            if ($this->model->getModelName() == 'Company' && $this->request->param('type') == 2) {
                $listType = 4;
            } else {
                $listType = 3;
            }
            return $this->filter('export', $listType);
        }

    }

    /**
     * 设置当前用户更新权限参数
     * @param $ownerId
     */
    protected function setUpdateParams($ownerId)
    {
        $isAdmin = $isOwner = false;
        $readonly = true;

        if ($this->current->m_isAdmin == 1) {
            $isAdmin = true;
        }

        if ($this->current->m_id == $ownerId) {
            $isOwner = true;
        }

        if ($isAdmin || $isOwner) {
            $readonly = false;
        }

        $this->assign('isAdmin', $isAdmin);
        $this->assign('isOwner', $isOwner);
        $this->assign('readonly', $readonly);

    }

    protected function uploadFile($file, $type = 'image')
    {
        if (empty($file)) {
            $ret = ['flag' => 2, 'msg' => '没有上传文件'];
        } else {

            $str = md5('XinZhiYun' . date('Ym'));

            $baseFolder = DS . 'files' . DS . substr($str, 8, 2) . DS . substr($str, 26, 2);

            $folder = ROOT_PATH . DS . 'public' . $baseFolder;

            if (!is_dir($folder)) {
                CustomUtils::mkdir_p($folder);
            }

            $rule = $this->model->getUploadFileValidateRule();

            $info = $file->validate($rule)->move($folder);

            if (empty($info)) {
                $ret = ['flag' => 0, 'msg' => $file->getError()];
            } else {
                $ret = ['flag' => 1, 'msg' => '文件上传成功', 'name' => $baseFolder . DS . $info->getSaveName()];
            }

        }
        return $ret;
    }

    protected function getAttachmentMimeType($file)
    {
        $file = ROOT_PATH . DS . 'public' . $file;
        $file = new File($file);
        return explode('/', $file->getMime())[0];
    }

    protected function setOrderUsed( $orders )
    {
        $ordersUsed = [];
        if(!!$orders){
            $m = new OrderUsed();
            foreach ($orders as $key=>$order){
                $invoice = 0;
                $acceptance = 0;
                $received = 0;
                $res = $m->where('ou_oid','=',$order->o_id)->select();
                if($res){
                    foreach ($res as $ou){
                        if($ou->ou_type == 1){ //发票
                            $invoice += $ou->ou_used;
                        }elseif($ou->ou_type == 2){ //验收单
                            $acceptance += $ou->ou_used;
                        }elseif($ou->ou_type == 3){ //回款
                            $received += $ou->ou_used;
                        }
                    }
                }
                $ordersUsed[$order->o_id]['invoice'] = $invoice;
                $ordersUsed[$order->o_id]['acceptance'] = $acceptance;
                $ordersUsed[$order->o_id]['received'] = $received;
            }
        }
        $this->assign('ordersUsed',$ordersUsed);
        return $ordersUsed;
    }

    public function trashed()
    {
        if(method_exists($this->model,'getTrashedField')) {
            $this->assign('formUrl','/'.strtolower($this->model->getModelName()).'/trashed');
            $this->assign('pageType', 'trashed');
            return $this->all(1);
        }else{
            return false;
        }
    }

    public function restore()
    {
        if(method_exists($this->model,'getTrashedField')){
            $ids = $this->request->post('ids/a');
            if(empty($ids)){
                $ret = ['flag'=>0,'msg'=>'参数错误'];
            }else{
                try {
                    $res = $this->model->whereIn($this->model->getPk(), $ids)->update([$this->model->getTrashedField()=>2]);
                    if($res){
                        $ret = ['flag'=>1,'msg'=>'操作成功'];
                    }else{
                        $ret = ['flag'=>0,'msg'=>'操作失败'];
                    }
                }catch (\Exception $e) {
                    $ret = ['flag'=>0,'msg'=>'发生错误'];
                }
            }
        }else{
            $ret = ['flag'=>0,'msg'=>'非法操作'];
        }
        return $ret;
    }

    public function delAttachment($id)
    {
        
    }

}