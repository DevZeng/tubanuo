<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    //
    protected $primaryKey = 'visitor_id';
    protected $table = 'fb_visitor';
}
