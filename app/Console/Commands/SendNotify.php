<?php

namespace App\Console\Commands;

use App\Libraries\Wxxcx;
use App\NotifyList;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendNotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendNotify';

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
        $list = NotifyList::where('state','=',1)->groupBy('open_id')->get()->toArray();
        for ($i=0;$i<count($list);$i++){
//                    DB::
            NotifyList::where('open_id','=',$list[$i]['open_id'])
                ->where('id','!=',$list[$i]['id'])
                ->whereBetween('mtime',[date('Y-m-d H:i:s',strtotime($list[$i]['mtime'])-5*60),date('Y-m-d H:i:s',strtotime($list[$i]['mtime'])+5*60)])
                ->update(['state'=>2,'remark'=>'弃用十分钟内重复消息']);
            $access_token=getUserToken('access_token');
            if ($access_token){
                $url=sprintf('https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s',$access_token);
                $wx=new Wxxcx('wx5d3adede82686b38','38373ccbb128e60d02ee0eb97d2f5272');
                $redata = $wx->request($url,$list[$i]['content']);
                dump($redata);
//                DB::connection('mysql_xijiao')->table('fb_school')->where('id','=',$records[$i]->id)->update(['notify'=>2]);

            }else{
                setRedisData('refresh',1);
                break;
            }
//            DB::connection('mysql_shiqi')->table('fb_school')->where('id','=',$records[$i]->id)->update(['notify'=>99]);

        }
        dd($list);
    }
}
