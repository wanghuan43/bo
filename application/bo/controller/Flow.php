<?php

namespace app\bo\controller;

use app\bo\libs\BoController;
use think\Request;

class Flow extends BoController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index()
    {

    }
}