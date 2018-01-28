<?php

namespace app\bo\libs;

use think\Config;

/**
 * Created by PhpStorm.
 * User: jerry
 * Date: 2018/1/29
 * Time: 01:43
 */
class ICome
{
    private $baseUrl;
    private $auth;
    private $token;
    private $ticket;

    public function __construct()
    {
        Config::load(APP_PATH . "bo" . DS . "reportExcel.php", "", "icome");
        $this->baseUrl = Config::get('api', 'icome');
        $this->auth = Config::get('icome', 'icome');
    }

    public function getSignature()
    {
        $this->getTicket();
    }

    private function getToken()
    {
        $uri = $this->baseUrl . Config::get('token', 'icome');
        $this->token = $this->postData($uri, $this->auth);
        print_r($this->token);exit;
    }

    private function getTicket()
    {
        $this->getToken();
        $uri = $this->baseUrl . Config::get('ticket', 'icome');
        $this->ticket = $this->postData($uri, array('access_token'=>$this->token));
    }

    private function postData($uri, $post_data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $uri);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }
}