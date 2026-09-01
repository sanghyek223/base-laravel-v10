<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardCounter extends Model
{
    use HasFactory;

    protected $primaryKey = 'sid';

    protected $guarded = [];

    protected $casts = [

    ];

    public function setByData($data)
    {
        $this->b_sid = $data['sid'];
        $this->ip = request()->ip();
    }
}
