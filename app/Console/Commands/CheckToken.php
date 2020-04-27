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
                $url = sprintf('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s','wxa45e3bb7239c5059','2f0f6280dea9047347bd193747750bf8');
                $wx = new Wxxcx('wxa45e3bb7239c5059','2f0f6280dea9047347bd193747750bf8');
                $data = $wx->request($url);
                if (isset($data['access_token'])){
                    setRedisData('access_token',$data['access_token'],7000);
                    setRedisData('refresh',2);
//                $access_token = $data['access_token'];
                }
            }
        }else{
            $url = sprintf('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s','wxa45e3bb7239c5059','2f0f6280dea9047347bd193747750bf8');
            $wx = new Wxxcx('wxa45e3bb7239c5059','2f0f6280dea9047347bd193747750bf8');
            $data = $wx->request($url);
            if (isset($data['access_token'])){
                setRedisData('access_token',$data['access_token'],7000);
                setRedisData('refresh',2);
            }
        }

    }
}
