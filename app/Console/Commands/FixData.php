<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixData {param1}';

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
        $schoolName = $this->argument('param1');
        $config = [
            'longtouhuan'=>'龙头环小学',
            'huxun'=>'虎逊小学',
            'shiqi'=>'石岐中学',
            'shaxi'=>'沙溪中学'
        ];

        switch ($schoolName){
            case "shiqi":
                $data = DB::connection('mysql_shiqi')->table('fb_school')->where('notify','=',1)
                    ->whereBetween('imex_time',[date('Y-m-d H:i:s',strtotime('2020-05-10 15:06:03')-5*60),date('Y-m-d H:i:s',strtotime('2020-05-10 15:06:03')+5*60)])
                ->get()->toArray();
                dd($data);
//                DB::connection('mysql_shiqi')->table('fb_school')->where('notify','=',1)->update(['notify'=>5]);
                break;
        }
//        $db = DB::connection();
    }
}
