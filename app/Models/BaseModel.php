<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * Default hidden fields for api
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
