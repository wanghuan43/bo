<?php
namespace app\bo\controller;

use app\bo\libs\BoController;
use think\Request;

class Invoice extends BoController
{
    public function searchInvoice()
    {
        $invoiceModel = new \app\bo\model\Invoice();
        $this->assign("type", "invoice");
        return $this->search($invoiceModel, "common/popused");
    }

    public function add()
    {
        return '<h2>添加发票</h2>';
    }

    public function all()
    {
        return '<h2>所有发票</h2>';
    }

}