<?php

namespace App\Console\Commands;

use App\NotifyList;
use Illuminate\Console\Command;

class getNotifyQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getNotifyQueue';

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

        $queues = getRedisData('tubanuo_notify_queues');
        if ($queues){
            $queues = unserialize($queues);
            if (count($queues)==0){
                $list = NotifyList::where('state','=',1)->orderBy('id','DESC')->groupBy('user_id')->pluck('id')->toArray();
                setRedisData('tubanuo_notify_queues',serialize($list),getRedisTime());
            }
        }else{
            $list = NotifyList::where('state','=',1)->orderBy('id','DESC')->groupBy('user_id')->pluck('id')->toArray();
            setRedisData('tubanuo_notify_queues',serialize($list),getRedisTime());
        }


    }
}
