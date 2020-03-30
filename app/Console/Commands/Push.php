<?php

namespace App\Console\Commands;

use App\Libraries\Wxxcx;
use App\Models\VisitorHistory;
use App\Visitor;
use Illuminate\Console\Command;

class Push extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push';

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
        $visitors = Visitor::where('vistior_status','=',1)->orWhere('vistior_status','=',2)->get();
        foreach ($visitors as $visitor){
            $count = VisitorHistory::where('visitor_id','=',$visitor->visitor_id)->count();
            if ($count==0){
                $data = [
                    'touser'=>$visitor->user_openid,
                    'template_id'=>'HyhU2aQ1K7LsZ6mqv4OUQsIJmdTjiTwXF1HgbkwKw7s',
                    "miniprogram"=>[
                        'appid'=>''
                    ],
                    'data'=>[
                        'first'=>[
                            'value'=>$visitor->vistior_status==1?'通过':'拒绝'
                        ],
                        'keyword1'=>[
                            'value'=>'广州图巴诺'
                        ],
                        'keyword2'=>[
                            'value'=>$visitor->user_name
                        ],
                        'keyword3'=>[
                            'value'=>date('Y年m月d H:i:s')
                        ],
                        'remark'=>[
                            'value'=>''
                        ]
                    ],
                ];
                $access_token = getUserToken('access_token');
                if ($access_token){
                    //
//                    private $app_id = '';
//                    private $scerct = '';
                    $url = sprintf('https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s',$access_token);
                    $wx = new Wxxcx('wxa45e3bb7239c5059','65c369313719a3e02d9b905f13d9981e');
                    $redata = $wx->request($url,json_encode($data));
                    $his = new VisitorHistory();
                    $his->visitor_id = $visitor->visitor_id;
                    $his->save();
                    dump($redata);
                }else{
                    setRedisData('refresh',1);
                }
            }
        }
    }
}
