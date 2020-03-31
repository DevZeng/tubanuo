<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/28
 * Time: 11:20
 */
namespace APP\Modules\User;


use Illuminate\Support\Facades\DB;

class UserHandle
{
    public function addUser($data){
        dd(123);
        $res = DB::table('fb_user')->insert($data);

    }

}