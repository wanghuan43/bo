<?php
namespace app\bo\controller;

use app\bo\libs\BoController;

class Acceptance extends BoController
{
    public function searchAcceptance()
    {
        $acceptanceModel = new \app\bo\model\Acceptance();
        $this->assign("type", "acceptance");
        return $this->search($acceptanceModel, "common/popused");
    }
}