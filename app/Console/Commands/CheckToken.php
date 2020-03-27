<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

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
    }
}
