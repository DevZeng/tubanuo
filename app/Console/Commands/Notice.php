<?php

namespace App\Console\Commands;

use App\Libraries\Wxxcx;
use App\Models\StudentStatus;
use App\Models\VisitorHistory;
use App\Student;
use App\Visitor;
use Illuminate\Console\Command;

class Notice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notice';

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
        $student=Student::where('stu_status','=',1)->orWhere('stu_status','=',2)->get();
        $template="sExfJnV0OMkzfcSNSNxpBbl6DrLO2VLXfIPCejmM1lM";
        foreach ($student as $value){
            $count=StudentStatus::where('student_id',$value->stu_id)->where('student_status',1)->count();
            if ($count == 0){
                $data=[
                    'touser'=>$student->user_openid,
                    'template_id'=>$template,
                    'miniprogram'=>[
                        'appid'=>""
                    ],
                    'data'=>[
                        'first'=>[
                            'value'=>"您好,审核申请已经有结果了"
                        ],
                        'keyword1'=>[
                            'value'=>$student->stu_name
                        ],
                        'keyword2'=>[
                            'value'=>$student->stu_status==1?'通过':'不通过'
                        ],
                        'keyword3'=>[
                            'value'=>date('Y年m月d H:i:s')
                        ],
                        'remark'=>[
                            'value'=>$student->stu_status==1?"审核通过":"审核不通过,请重新提交"
                        ]
                    ],
                ];
                $access_token=getUserToken('access_token');
                if ($access_token){
                    $url=sprintf('https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s',$access_token);
                    $wx=new Wxxcx('wxa45e3bb7239c5059','65c369313719a3e02d9b905f13d9981e');
                    $redata = $wx->request($url,json_encode($data));
                    dump($redata);
                    if ($redata['errcode']==0){
                        $stu=new StudentStatus();
                        $stu->student_id=$value->stu_id;
                        $stu->stu_status=$value->stu_status;
                        $stu->save();
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
