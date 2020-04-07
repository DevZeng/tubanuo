<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisitorPost;
use App\Libraries\Wxxcx;
use App\Models\Staff;
use App\Models\TeacherApply;
use App\Models\WxUser;
use App\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\Input;

class WxController extends Controller
{
    //
    private $app_id = 'wxa45e3bb7239c5059';
    private $scerct = '65c369313719a3e02d9b905f13d9981e';
    public function getAccessToken(Request $post)
    {
        $access_token = getUserToken('access_token');
        $force = $post->get('force',0);
        if ($force){
            $url = sprintf('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s',$this->app_id,$this->scerct);
            $wx = new Wxxcx($this->app_id,$this->scerct);
            $data = $wx->request($url);
//            dump($data);
            if (isset($data['access_token'])){
                setRedisData('access_token',$data['access_token'],7000);
                $access_token = $data['access_token'];
            }
            return jsonResponse([
                'msg'=>'ok',
                'data'=>$access_token
            ]);
        }
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
    public function insert_user(Request $post)
    {
        $token = getRedisData('access_token');
        $open_id = $post->open_id;
        $url = sprintf('https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN',$token,$open_id);
        $wx = new Wxxcx($this->app_id,$this->scerct);
        $data = $wx->request($url);
        if (!isset($data['openid'])){
            setRedisData('refresh',1);
        }
        $user = WxUser::where('user_openid','=',$data['openid'])->first();
        if (!$user){
            $user = new WxUser();
            $user->user_openid = $data['openid'];
            $user->user_image = $data['headimgurl'];
            $user->user_alias = $data['nickname'];
            $user->creat_time = date('Y-m-d H:i:s');
        }
        $user->user_name = $post->user_name;
        $user->user_sex = $post->user_sex;
        $user->user_iphone = $post->user_iphone;
        $user->user_card = $post->user_card;
        $user->user_address = $post->user_address;
        $teacher = $post->teacher;
        $whether = $post->whether;
        $positions = $post->positions;
        $date1 = $post->date1;
        $staff_status = $post->staff_status;
        $user->save();
        if ($teacher==1){
            $apply = new TeacherApply();
            $apply->user_openid = $user->user_openid;
            $apply->work_number = $post->work_number;
            $apply->user_card = $user->user_card;
            $apply->whether = $whether;
            $apply->class_id = $post->class_id;
            $apply->user_head1 = $post->user_head1;
            $apply->subjects = $post->subjects;
            $apply->save();
        }
        if ($staff_status==1){
            $staff = new Staff();
            $staff->user_openid = $user->user_openid;
            $staff->positions = $positions;
            $staff->date1 = $date1;
            $staff->user_images1 = $post->user_images1;
            $staff->save();
        }
//        if ($data[''])
        return jsonResponse([
            'msg'=>'ok'
        ]);
    }
    public function test()
    {
        $token = getRedisData('access_token');
        $open_id = 'oaHn-0Uz5B2GCTk2W4-1H3wXoItE';
        $url = sprintf('https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN',$token,$open_id);
        $wx = new Wxxcx($this->app_id,$this->scerct);
        $data = $wx->request($url);
        dd($data);
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
    public function getGrade()
    {
        $Data = DB::table('fb_grade')->pluck('class_grade')->all();
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$Data
        ]);
    }
    public function getClassByGrade(Request $post)
    {
        $class = DB::table('fb_class')->where('class_grade','=',$post->grade)->get();
        $data = [];
        if (!empty($class)){
            for ($i=0;$i<count($class);$i++){
                $data[$i]['text'] = $class[$i]->class_name;
                $data[$i]['class_id'] = $class[$i]->class_id;
            }
        }
        return jsonResponse([
            'msg'=>'ok',
            'data'=>$data
        ]);
    }

}
