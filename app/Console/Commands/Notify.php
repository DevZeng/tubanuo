<?php

namespace App\Console\Commands;

use App\Libraries\Wxxcx;
use App\Models\SchoolNotify;
use App\Models\StudentStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Notify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify';

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
        //
        $config = [
            'longtouhuan'=>'龙头环小学'
        ];
        $template="xcpIiC4aBCpHImefa8FgwtFY6kMoDslN5BH2ZtA4rJk";
        $records = DB::table('fb_school')->where('notify','=',1)->get();
        if (count($records )!=0){
            for ($i=0;$i<count($records);$i++){
                $student = DB::table('fb_student')->where('stu_number','=',$records[$i]->stu_number)->first();
                if ($student){
                    $user = DB::table('fb_user')->where('user_openid','=',$student->user_openid)->first();
                    if ($user&&$user->notify==1){
                        $schoolNotify = SchoolNotify::where('user_id','=',$user->user_openid)->first();
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
                            $url=sprintf('https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=%s',$access_token);
                            $wx=new Wxxcx('wx5d3adede82686b38','38373ccbb128e60d02ee0eb97d2f5272');
                            $redata = $wx->request($url,json_encode($data));
                            dump($redata);
                            if ($redata['errcode']==0){
                                DB::table('fb_school')->where('id','=',$records[$i]->id)->update(['notify'=>2]);
                            }else{
                                setRedisData('refresh',1);
                            }
                        }else{
                            setRedisData('refresh',1);
                        }
                    }
                }
            }
        }
    }
}
