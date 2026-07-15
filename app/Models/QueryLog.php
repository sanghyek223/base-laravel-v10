<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueryLog extends Model
{
    use HasFactory;

    protected $primaryKey = 'sid';

    protected $guarded = [];

    protected $casts = [
        'query' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
