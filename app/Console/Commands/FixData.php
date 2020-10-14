<?php

namespace App\Console\Commands;

use App\Libraries\WxPay;
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
        $xml='<?xml version="1.0" encoding="UTF-8"?><DATA>
<RECORD no="2" code="sa" msg="OD"> </RECORD>
        <RECORD no="3" code="s23a" msg="OdD">
        <ID>DFDS</ID>
         </RECORD>
</DATA>
        ';
        $wx = WxPay::xmlToArray($xml);
        foreach ($wx as $item){
            dd($item[0]['@attributes']);
        }
        dump($wx);
        die();
        $wxpay = new WxPay('wx1da902426c4dc72b','1601515017','4d682c7755b188141940d2d60dd17b1f',
            $wx['openid']);
        $signData = $wx;
        unset($signData['sign']);

        $sign = $wxpay->getSign($signData);
        dd($sign);
        $schoolName = $this->argument('param1');
        $config = [
            'longtouhuan'=>'龙头环小学',
            'huxun'=>'虎逊小学',
            'shiqi'=>'石岐中学',
            'shaxi'=>'沙溪中学'
        ];
        $day=date('Y-m-d',time());
        switch ($schoolName){
            case "xijiao":
                $records = DB::connection('mysql_xijiao')->table('fb_school')
                    ->where('notify','=',1)->update(['notify'=>2]);
                $records = DB::connection('mysql_xijiao')->table('fb_school')->where('imex_time','like',$day."%")->update(['notify'=>1]);

//                dd($records);
//                for ($i=0;$i<count($records);$i++){
////                    DB::
//                    DB::connection('mysql_shiqi')->table('fb_school')->where('stu_number','=',$records[$i]->stu_number)
//                        ->whereBetween('imex_time',[date('Y-m-d H:i:s',strtotime('2020-05-10 15:06:03')-5*60),date('Y-m-d H:i:s',strtotime('2020-05-10 15:06:03')+5*60)])
//                        ->update(['notify'=>10]);
//                    DB::connection('mysql_shiqi')->table('fb_school')->where('id','=',$records[$i]->id)->update(['notify'=>99]);
//
//                }
//                $data = DB::connection('mysql_shiqi')->table('fb_school')->where('notify','=',1)
//                    ->whereBetween('imex_time',[date('Y-m-d H:i:s',strtotime('2020-05-10 15:06:03')-5*60),date('Y-m-d H:i:s',strtotime('2020-05-10 15:06:03')+5*60)])
//                ->get()->toArray();
//                dd($data);
//                DB::connection('mysql_shiqi')->table('fb_school')->where('notify','=',1)->update(['notify'=>5]);
                break;
        }
//        $db = DB::connection();
    }
}
