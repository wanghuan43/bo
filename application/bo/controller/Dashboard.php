<?php
namespace app\bo\controller;

use think\Controller;

class Dashboard extends Controller
{

    public function index()
    {
        return $this->fetch( 'index' );
    }

}
