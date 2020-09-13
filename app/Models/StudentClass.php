<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2020/8/28
 * Time: 9:52
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StudentClass extends Model
{
    protected $connection = 'mysql_center';
    protected $table = "fb_class";
}