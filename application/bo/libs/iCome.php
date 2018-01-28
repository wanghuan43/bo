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
    private $nonceStr;

    public function __construct()
    {
        Config::load(APP_PATH . "bo" . DS . "icom.php", "", "icome");
        $this->baseUrl = Config::get('api', 'icome');
        $this->auth = Config::get('icome', 'icome');
        $this->nonceStr = Config::get('nonceStr', 'icome');
    }

    public function getJsAuth()
    {
        $this->getTicket();
        $timestamp = $this->getTimestamp();
        $signature = sha1("jsticket=" . $this->ticket .
            "&nonceStr=" . $this->nonceStr .
            "&timestamp=" . $timestamp .
            "&url=" . $this->getServer());
        $return = [
            'js' => $this->getJsAddress(),
            'auth' => [
                'appId' => $this->auth['appId'],
                'nonceStr' => $this->nonceStr,
                'timestamp' => $timestamp,
                'signature' => $signature,
                'jsApiList' => [
                    "system.createMultChat"
                ]
            ]
        ];
        return $return;
    }

    public function getJsAddress()
    {
        return $this->baseUrl . '/icomeapps/public/icom.js';
    }

    public function getServer()
    {
        return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . "/";
    }

    public function getTimestamp()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }

    private function getToken()
    {
        $uri = $this->baseUrl . Config::get('token', 'icome');
        $tmp = json_decode($this->postData($uri, json_encode($this->auth)), true);
        if (empty($tmp['errno'])) {
            $this->token = $tmp['data']['access_token'];
        }
    }

    private function getTicket()
    {
        $this->getToken();
        $uri = $this->baseUrl . Config::get('ticket', 'icome');
        $tmp = json_decode($this->postData($uri, json_encode(array('access_token' => $this->token))), true);
        if (empty($tmp['errno'])) {
            $this->ticket = $tmp['data']['jsticket'];
        }
    }

    private function postData($uri, $post_data)
    {
        $curl = curl_init();
        $header = ['Content-Type:application/json;charset=utf-8'];
        curl_setopt($curl, CURLOPT_URL, $uri);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }
}