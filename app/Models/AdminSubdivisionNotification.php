<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSubdivisionNotification extends Model
{
    protected $fillable = ['admin_cid', 'subdivision_id'];
    public $timestamps = false;
}
