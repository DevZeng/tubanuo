<?php

namespace App\Libraries;


class Wxxcx
{
    private $appid;
    private $secret;
    private $code2session_url;
    private $sessionKey;
    private $access_token_url;

    public function __construct($appid,$secret,$code2session_url = "https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code")
    {
        $this->appid = $appid;
        $this->secret = $secret;
        $this->code2session_url = $code2session_url;
        $this->access_token_url =  "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s";
    }


    public function getSessionKey($code)
    {
        $code2session_url = sprintf($this->code2session_url,$this->appid,$this->secret,$code);
        $userInfo = $this->request($code2session_url);
        if(!isset($userInfo['session_key'])){
            return false;
        }
        $this->sessionKey = $userInfo['session_key'];
        return $userInfo;
    }

    public function request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        if($output === FALSE ){
            return false;
        }
        curl_close($curl);
        return json_decode($output,JSON_UNESCAPED_UNICODE);
    }

    public function decode($encryptedData,$iv)
    {
        $pc = new WxBizDataCrypt($this->appid, $this->sessionKey);
        $data = '';
        $errCode = $pc->decryptData($encryptedData,$iv,$data);
        if ($errCode !=0 ) {
//            dd($errCode);
            throw new \Exception('EncryptedData解密失败！');
        }
        return $data;
    }
    public function getAccessToken()
    {
        $access_token_url = sprintf($this->access_token_url,$this->appid,$this->secret);
        $access_token = $this->request($access_token_url);
        if(!isset($access_token['access_token'])){
            return false;
        }
        return $access_token;
    }
    public function createNoncestr($length = 32) {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    public function getSign($Obj) {
        foreach ($Obj as $k => $v) {
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        $result_ = sha1($String);
        return $result_;
    }
    public function formatBizQueryParaMap($paraMap, $urlencode) {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if ($urlencode) {
                $v = urlencode($v);
            }
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar = '';
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }
    public function get_http_array($url,$post_data,$type='json') {
        if($type=='json'){
            $headers = array("Content-type: application/json;charset=UTF-8");
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($post_data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS,$post_data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}