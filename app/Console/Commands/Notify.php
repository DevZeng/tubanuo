<?php

namespace App\Console\Commands;

use App\Libraries\Wxxcx;
use App\Models\SchoolNotify;
use App\Models\StudentStatus;
use App\NotifyList;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Notify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify {param1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $schoolName = $this->argument('param1');
        $config = [
            'longtouhuan'=>'龙头环小学',
            'huxun'=>'虎逊小学',
            'shiqi'=>'石岐中学',
            'shaxi'=>'沙溪中学'
        ];
        $template="xcpIiC4aBCpHImefa8FgwtFY6kMoDslN5BH2ZtA4rJk";
        $day=date('Y-m-d',time());
        switch ($schoolName){
            case 'longtouhuan':
                $records = DB::connection('mysql')->table('fb_school')->where('notify','=',1)->where('imex_time','like',$day."%")->get();
                if (count($records )!=0){
                    for ($i=0;$i<count($records);$i++){

                        $student = DB::connection('mysql')->table('fb_student')->where('stu_number','=',$records[$i]->stu_number)->first();
                        if ($student){
                            $user = DB::connection('mysql')->table('fb_user')->where('user_openid','=',$student->user_openid)->first();
                            if ($user&&$user->notify==1){
                                $schoolNotify = DB::connection('mysql')->table('school_notifies')->
                                    where('user_id','=',$user->user_openid)->first();
//                                $schoolNotify = SchoolNotify::where('user_id','=',$user->user_openid)->first();
                                if ($schoolNotify){
                                    $data=[
                                        'touser'=>$schoolNotify->open_id,
                                        'template_id'=>$template,
                                        'miniprogram'=>[
                                            'appid'=>'wx5d3adede82686b38',
                                            'pagepath'=>"pages/campus-safety/index/index"
                                        ],
                                        'data'=>[
                                            'first'=>[
                                                'value'=>$config[$schoolNotify->school]
                                            ],
                                            'keyword1'=>[
                                                'value'=>$records[$i]->stu_number
                                            ],
                                            'keyword2'=>[
                                                'value'=>$records[$i]->stu_name
                                            ],
                                            'keyword3'=>[
                                                'value'=>$records[$i]->school_status==0?"离校":"进校"
                                            ],
                                            'keyword4'=>[
                                                'value'=>$records[$i]->imex_time
                                            ],
                                            'remark'=>[
                                                'value'=>$records[$i]->temp==0?' ':'体温：'.$records[$i]->temp
                                            ],
                                        ],
                                    ];
                                }

                                dump($data);
                                $access_token=getUserToken('access_token');
                                if ($access_token){
                                    $url=sprintf('https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s',$access_token);
                                    $wx=new Wxxcx('wx5d3adede82686b38','38373ccbb128e60d02ee0eb97d2f5272');
                                    $redata = $wx->request($url,json_encode($data));
                                    dump($redata);

                                        DB::connection('mysql')->table('fb_school')->where('id','=',$records[$i]->id)->update(['notify'=>2]);

                                }else{
                                    setRedisData('refresh',1);
                                }
                            }
                        }else{
                            DB::connection('mysql')->table('fb_school')->where('id','=',$records[$i]->id)->update(['notify'=>3]);
                        }
                    }
                }
                break;
            case 'huxun':
                $records = DB::connection('mysql_huxun')->table('fb_school')->where('notify','=',1)->where('imex_time','like',$day."%")->get();
//                dump($records);
                if (count($records )!=0){
                    for ($i=0;$i<count($records);$i++){

                        $student = DB::connection('mysql_huxun')->table('fb_student')->where('stu_number','=',$records[$i]->stu_number)->first();
                        if ($student){
                            $user = DB::connection('mysql_huxun')->table('fb_user')->where('user_openid','=',$student->user_openid)->first();
                            if ($user&&$user->notify==1){
                                $schoolNotify = DB::connection('mysql_huxun')->table('school_notifies')->
                                where('user_id','=',$user->user_openid)->first();
                               if ($schoolNotify){
                                    $data=[
                                        'touser'=>$schoolNotify->open_id,
                                        'template_id'=>$template,
                                        'miniprogram'=>[
                                            'appid'=>'wx9ceb6bc9883484de',
                                            'pagepath'=>"pages/campus-safety/index/index"
                                        ],
                                        'data'=>[
                                            'first'=>[
                                                'value'=>$config[$schoolNotify->school]
                                            ],
                                            'keyword1'=>[
                                                'value'=>$records[$i]->stu_number
                                            ],
                                            'keyword2'=>[
                                                'value'=>$records[$i]->stu_name
                                            ],
                                            'keyword3'=>[
                                                'value'=>$records[$i]->school_status==0?"离校":"进校"
                                            ],
                                            'keyword4'=>[
                                                'value'=>$records[$i]->imex_time
                                            ],
                                            'remark'=>[
                                                'value'=>$records[$i]->temp==0?' ':'体温：'.$records[$i]->temp
                                            ],
                                        ],
                                    ];
                                }
                                dump($data);
                                $access_token=getUserToken('access_token');
                                if ($access_token){
                                    $url=sprintf('https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s',$access_token);
                                    $wx=new Wxxcx('wx5d3adede82686b38','38373ccbb128e60d02ee0eb97d2f5272');
                                    $redata = $wx->request($url,json_encode($data));
                                    dump($redata);

                                        DB::connection('mysql_huxun')->table('fb_school')->where('id','=',$records[$i]->id)->update(['notify'=>2]);

                                }else{
                                    setRedisData('refresh',1);
                                }
                            }
                        }
                    }
                }
                break;
            case 'shiqi':
                $records = DB::connection('mysql_shiqi')->table('fb_school')->where('notify','=',1)->where('imex_time','like',$day."%")->get();
//                dump($records);
                if (count($records )!=0){
                    for ($i=0;$i<count($records);$i++){
                        $student = DB::connection('mysql_shiqi')->table('fb_student')->where('stu_number','=',$records[$i]->stu_number)->first();
                        if ($student){
                            $user = DB::connection('mysql_shiqi')->table('fb_user')->where('user_openid','=',$student->user_openid)->first();
                            if ($user&&$user->notify==1){
                                $schoolNotify = DB::connection('mysql_shiqi')->table('school_notifies')->
                                where('user_id','=',$user->user_openid)->first();
                                if ($schoolNotify){
                                    $data=[
                                        'touser'=>$schoolNotify->open_id,
                                        'template_id'=>$template,
                                        'miniprogram'=>[
                                            'appid'=>'wx10d7cd97c4bed05c',
                                            'pagepath'=>"pages/campus-safety/index/index"
                                        ],
                                        'data'=>[
                                            'first'=>[
                                                'value'=>$config[$schoolNotify->school]
                                            ],
                                            'keyword1'=>[
                                                'value'=>$records[$i]->stu_number
                                            ],
                                            'keyword2'=>[
                                                'value'=>$records[$i]->stu_name
                                            ],
                                            'keyword3'=>[
                                                'value'=>$records[$i]->school_status==0?"离校":"进校"
                                            ],
                                            'keyword4'=>[
                                                'value'=>$records[$i]->imex_time
                                            ],
                                            'remark'=>[
                                                'value'=>$records[$i]->temp==0?' ':'体温：'.$records[$i]->temp
                                            ],
                                        ],
                                    ];
                                }
                                $notifyList = new NotifyList();
                                $notifyList->open_id = $schoolNotify->open_id;
                                $notifyList->mtime = $records[$i]->imex_time;
                                $notifyList->content = json_encode($data);
                                $notifyList->save();
//                                dump($data);
//                                $access_token=getUserToken('access_token');
//                                if ($access_token){
//                                    $url=sprintf('https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s',$access_token);
//                                    $wx=new Wxxcx('wx5d3adede82686b38','38373ccbb128e60d02ee0eb97d2f5272');
//                                    $redata = $wx->request($url,json_encode($data));
//                                    dump($redata);
//
//                                        DB::connection('mysql_shiqi')->table('fb_school')->where('id','=',$records[$i]->id)->update(['notify'=>2]);
//
//
//                                }else{
//                                    setRedisData('refresh',1);
//                                }
                            }
                        }else{
                            DB::connection('mysql_shiqi')->table('fb_school')->where('id','=',$records[$i]->id)->update(['notify'=>3]);
                        }
                    }
                }
                break;
            case 'xijiao':
                $records = DB::connection('mysql_xijiao')->table('fb_school')->where('notify','=',1)->where('imex_time','like',$day."%")->get();
//                dump($records);
                if (count($records )!=0){
                    for ($i=0;$i<count($records);$i++){

                        $student = DB::connection('mysql_xijiao')->table('fb_student')->where('stu_number','=',$records[$i]->stu_number)->first();
                        if ($student){
                            $user = DB::connection('mysql_xijiao')->table('fb_user')->where('user_openid','=',$student->user_openid)->first();
                            if ($user&&$user->notify==1){
                                $schoolNotify = DB::connection('mysql_xijiao')->table('school_notifies')->
                                where('user_id','=',$user->user_openid)->first();
                                if ($schoolNotify){
                                    $data=[
                                        'touser'=>$schoolNotify->open_id,
                                        'template_id'=>$template,
                                        'miniprogram'=>[
                                            'appid'=>'wx3fe22b4ebf2ca578',
                                            'pagepath'=>"pages/campus-safety/index/index"
                                        ],
                                        'data'=>[
                                            'first'=>[
                                                'value'=>$config[$schoolNotify->school]
                                            ],
                                            'keyword1'=>[
                                                'value'=>$records[$i]->stu_number
                                            ],
                                            'keyword2'=>[
                                                'value'=>$records[$i]->stu_name
                                            ],
                                            'keyword3'=>[
                                                'value'=>$records[$i]->school_status==0?"离校":"进校"
                                            ],
                                            'keyword4'=>[
                                                'value'=>$records[$i]->imex_time
                                            ],
                                            'remark'=>[
                                                'value'=>$records[$i]->temp==0?' ':'体温：'.$records[$i]->temp
                                            ],
                                        ],
                                    ];
                                }
                                dump($data);
                                $access_token=getUserToken('access_token');
                                if ($access_token){
                                    $url=sprintf('https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s',$access_token);
                                    $wx=new Wxxcx('wx5d3adede82686b38','38373ccbb128e60d02ee0eb97d2f5272');
                                    $redata = $wx->request($url,json_encode($data));
                                    dump($redata);
                                    
                                        DB::connection('mysql_xijiao')->table('fb_school')->where('id','=',$records[$i]->id)->update(['notify'=>2]);

                                }else{
                                    setRedisData('refresh',1);
                                }
                            }
                        }
                    }
                }
                break;
        }

        //



    }
}
