<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2020/8/28
 * Time: 14:14
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FbStudent extends Model
{
    protected $connection = 'mysql_center';
    protected $table = "fb_student";
}