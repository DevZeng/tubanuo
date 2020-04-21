<?php

namespace App\Console\Commands;

use App\Libraries\Wxxcx;
use Illuminate\Console\Command;

class CheckToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refreshToken';

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
        $access_token = getUserToken('access_token');
        if ($access_token){
            $refresh = getUserToken('refresh');
            if ($refresh==1){
                $url = sprintf('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s','wx5d3adede82686b38','38373ccbb128e60d02ee0eb97d2f5272');
                $wx = new Wxxcx('wx5d3adede82686b38','38373ccbb128e60d02ee0eb97d2f5272');
                $data = $wx->request($url);
                if (isset($data['access_token'])){
                    setRedisData('access_token',$data['access_token'],7000);
                    setRedisData('refresh',2);
//                $access_token = $data['access_token'];
                }
            }
        }else{
            $url = sprintf('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s','wx5d3adede82686b38','38373ccbb128e60d02ee0eb97d2f5272');
            $wx = new Wxxcx('wx5d3adede82686b38','38373ccbb128e60d02ee0eb97d2f5272');
            $data = $wx->request($url);
            if (isset($data['access_token'])){
                setRedisData('access_token',$data['access_token'],7000);
                setRedisData('refresh',2);
            }
        }

    }
}
