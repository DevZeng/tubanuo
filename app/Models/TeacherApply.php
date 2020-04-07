<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherApply extends Model
{
    //
    protected $table = 'fb_teacher_apply';
    public const CREATED_AT = null;
    public const UPDATED_AT = null;

    protected $primaryKey = 'user_id';
}
