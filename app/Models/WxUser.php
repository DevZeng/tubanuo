<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WxUser extends Model
{
    //
    protected $table = 'fb_user';
    public const CREATED_AT = null;
    public const UPDATED_AT = null;
    protected $primaryKey = 'user_id';
}
