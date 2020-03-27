<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisitorPost;
use App\Libraries\Wxxcx;
use App\Visitor;
use Illuminate\Http\Request;
use Symfony\Component\Console\Input\Input;

class WxController extends Controller
{
    //
    private $app_id = 'wxa45e3bb7239c5059';
    private $scerct = '65c369313719a3e02d9b905f13d9981e';
    public function getAccessToken()
    {
        $access_token = getUserToken('access_token');
        if (!$access_token){
            $url = sprintf('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s',$this->app_id,$this->scerct);
            $wx = new Wxxcx($this->app_id,$this->scerct);
            $data = $wx->request($url);
//            dump($data);
            if (isset($data['access_token'])){
                setRedisData('access_token',$data['access_token'],7000);
                $access_token = $data['access_token'];
            }
        }
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$access_token
        ]);
    }
    public function login(Request $post)
    {
        $code = $post->code;
        $url = sprintf('https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code',$this->app_id,$this->scerct,$code);
        $wx = new Wxxcx($this->app_id,$this->scerct);
        $data = $wx->request($url);
        if (isset($data['openid'])){
            return jsonResponse([
            'msg'=>'ok',
            'data'=>$data['openid']
        ]);
        }else{
            return jsonResponse([
                'msg'=>'error'
            ],422);
        }
    }
    public function addVisitor(VisitorPost $post)
    {
        $visitor = new Visitor();
        $visitor->form_id = 'xxx';
        $visitor->user_openid = $post->user_openid;
        $visitor->user_name = $post->user_name;
        $visitor->user_iphone = $post->user_iphone;
        $visitor->visitor_butt = $post->visitor_butt;
        $visitor->visitor_head1 = $post->visitor_head1;
        $visitor->visitor_reason = $post->visitor_reason;
        $visitor->save();
        return jsonResponse([
            'msg'=>'ok'
        ]);
    }
}
