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
}